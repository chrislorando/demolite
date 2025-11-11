<?php

namespace App\Jobs;

use App\Enums\ResponseStatus;
use App\Models\CurriculumVitae;
use App\Models\Document;
use App\Services\AiServiceInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $cvId;
    /**
     * Number of attempts. Set to null to allow retrying until retryUntil() expires.
     * We'll use retryUntil() to control how long we want to keep retrying.
     */
    public ?int $tries = null;

    /**
     * The number of seconds to wait before retrying the job.
     * Can be a single int or an array of backoff seconds per attempt.
     */
    public int|array $backoff = [5, 10, 15];

    /**
     * Keep retrying until this timestamp. Return a DateTimeInterface in the future.
     * Set this to a sensible limit (e.g., 7 days) to avoid indefinite retry loops.
     */
    public function retryUntil(): \DateTimeInterface
    {
        return now()->addDays(7);
    }

    /**
     * Create a new job instance.
     */
    public function __construct(string $cvId)
    {
        $this->cvId = $cvId;
    }

    /**
     * Execute the job.
     */
    public function handle(AiServiceInterface $aiService): void
    {
        $document = CurriculumVitae::find($this->cvId);

        if (! $document) {
            Log::warning('ProcessCv: document not found', ['id' => $this->cvId]);
            return;
        }

        // mark as in progress
        $document->update(['status' => ResponseStatus::InProgress]);

        $response = $aiService->createCvScreeningResponse($document->file_url, $document->job_offer);

        $raw = $response->outputText;

        // Attempt to decode the response as JSON. Support strict decode first, then lenient fallback.
        $parsed = null;
        if (!empty($raw)) {
            try {
                $parsed = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                // Lenient fallback
                $maybe = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $parsed = $maybe;
                } else {
                    // Sometimes APIs wrap JSON in code fences or quotes: try to clean and decode
                    $clean = preg_replace('/^```(?:json)?\s*/i', '', $raw);
                    $clean = preg_replace('/\s*```$/', '', $clean);
                    $clean = trim($clean, "\"'");
                    $maybe2 = json_decode($clean, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $parsed = $maybe2;
                    }
                }
            }
        }

        $update = [
            'response' => $raw,
            'status' => ResponseStatus::Completed,
        ];

        if (is_array($parsed)) {
            // Map expected keys into DB columns. The API returns 0-100 numeric values.
            if (array_key_exists('job_position', $parsed)) {
                $update['job_position'] = is_scalar($parsed['job_position']) ? (string) $parsed['job_position'] : json_encode($parsed['job_position']);
            }

            if (array_key_exists('skill_match', $parsed)) {
                $update['skill_match'] = is_numeric($parsed['skill_match']) ? (float) $parsed['skill_match'] : null;
            }

            if (array_key_exists('experience_match', $parsed)) {
                $update['experience_match'] = is_numeric($parsed['experience_match']) ? (float) $parsed['experience_match'] : null;
            }

            if (array_key_exists('education_match', $parsed)) {
                $update['education_match'] = is_numeric($parsed['education_match']) ? (float) $parsed['education_match'] : null;
            }

            if (array_key_exists('overall_score', $parsed)) {
                $update['overall_score'] = is_numeric($parsed['overall_score']) ? (float) $parsed['overall_score'] : null;
            }

            if (array_key_exists('summary', $parsed)) {
                $update['summary'] = is_scalar($parsed['summary']) ? (string) $parsed['summary'] : json_encode($parsed['summary']);
            }

            if (array_key_exists('suggestion', $parsed)) {
                $update['suggestion'] = is_scalar($parsed['suggestion']) ? (string) $parsed['suggestion'] : json_encode($parsed['suggestion']);
            }

            if (array_key_exists('is_recommended', $parsed)) {
                $update['is_recommended'] = is_numeric($parsed['is_recommended']) ? (int) $parsed['is_recommended'] : null;
            }

            if (array_key_exists('cover_letter', $parsed)) {
                if($update['is_recommended']==1){
                    $update['cover_letter'] = is_scalar($parsed['cover_letter']) ? (string) $parsed['cover_letter'] : json_encode($parsed['cover_letter']);
                }else{
                    $update['cover_letter'] = null;
                }
            }


        }

        logger($parsed);

        $document->update($update);
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(Exception $exception): void
    {
        Log::error('ProcessDocument failed permanently', ['id' => $this->cvId, 'error' => $exception->getMessage()]);

        $document = CurriculumVitae::find($this->cvId);
        if ($document) {
            $document->update([
                'status' => ResponseStatus::Failed,
                'response' => ['error' => $exception->getMessage()],
            ]);
        }
    }
}
