<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OpenAiService;

class RetrieveOpenAiModel extends Command
{
    protected $signature = 'app:retrieve-open-ai-model {model}';
    protected $description = 'Retrieve details for a specific OpenAI model';

    public function handle(OpenAiService $openAiService): int
    {
        $modelId = $this->argument('model');
        try {
            $modelInfo = $openAiService->retrieveModel($modelId);
            $this->info('Model details:');
            $this->line(json_encode($modelInfo, JSON_PRETTY_PRINT));
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
