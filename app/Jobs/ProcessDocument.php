<?php

namespace App\Jobs;

use App\Enums\ResponseStatus;
use App\Models\Document;
use App\Services\AiServiceInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $documentId;
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
    public function __construct(string $documentId)
    {
        $this->documentId = $documentId;
    }

    /**
     * Execute the job.
     */
    public function handle(AiServiceInterface $aiService): void
    {
        $document = Document::find($this->documentId);

        if (! $document) {
            Log::warning('ProcessDocument: document not found', ['id' => $this->documentId]);
            return;
        }

        // mark as in progress
        $document->update(['status' => ResponseStatus::InProgress]);

        $response = $aiService->createDocumentResponse($document->url, $document->instructions);

        $document->update([
            'response' => $response->outputText,
            'status' => ResponseStatus::Completed,
        ]);
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(Exception $exception): void
    {
        Log::error('ProcessDocument failed permanently', ['id' => $this->documentId, 'error' => $exception->getMessage()]);

        $document = Document::find($this->documentId);
        if ($document) {
            $document->update([
                'status' => ResponseStatus::Failed,
                'response' => ['error' => $exception->getMessage()],
            ]);
        }
    }
}
