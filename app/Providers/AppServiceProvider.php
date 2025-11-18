<?php

namespace App\Providers;

use App\Services\AiServiceInterface;
use App\Services\OpenAiService;
use App\Repositories\ConversationRepositoryInterface;
use App\Repositories\ConversationRepository;
use App\Repositories\ConversationItemRepositoryInterface;
use App\Repositories\ConversationItemRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AiServiceInterface::class, OpenAiService::class);
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
        $this->app->bind(ConversationItemRepositoryInterface::class, ConversationItemRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
