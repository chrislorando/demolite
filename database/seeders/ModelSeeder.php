<?php

namespace Database\Seeders;

use App\Enums\ModelStatus;
use App\Enums\ModelType;
use App\Models\AiModel;
use Illuminate\Database\Seeder;

class ModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            [
                'id' => 'gpt-4.1-mini',
                'object' => 'model',
                'owned_by' => 'openai',
                'status' => ModelStatus::Active,
                'type' => ModelType::Text,
                'window_context' => '1047576',
            ],
            [
                'id' => 'gpt-4.1-nano',
                'object' => 'model',
                'owned_by' => 'openai',
                'status' => ModelStatus::Active,
                'type' => ModelType::Text,
                'window_context' => '1047576',
            ],
            [
                'id' => 'gpt-4o-mini',
                'object' => 'model',
                'owned_by' => 'openai',
                'status' => ModelStatus::Active,
                'type' => ModelType::Text,
                'window_context' => '128000',
            ],
            [
                'id' => 'gpt-5-mini',
                'object' => 'model',
                'owned_by' => 'openai',
                'status' => ModelStatus::Active,
                'type' => ModelType::Text,
                'window_context' => '400000',
            ],
            [
                'id' => 'gpt-5-nano',
                'object' => 'model',
                'owned_by' => 'openai',
                'status' => ModelStatus::Active,
                'type' => ModelType::Text,
                'window_context' => '400000',
            ],
            [
                'id' => 'whisper-1',
                'object' => 'model',
                'owned_by' => 'openai',
                'status' => ModelStatus::Active,
                'type' => ModelType::Audio,
                'window_context' => null,
            ],
        ];

        foreach ($models as $model) {
            AiModel::updateOrCreate(
                ['id' => $model['id']],
                $model
            );
        }
    }
}
