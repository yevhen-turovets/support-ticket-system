<?php

namespace App\Providers;

use App\Repositories\Contracts\TicketRepositoryInterface;
use App\Repositories\EloquentTicketRepository;
use App\Services\Contracts\AIServiceInterface;
use App\Services\FakeAIService;
use App\Services\OpenAIService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TicketRepositoryInterface::class, EloquentTicketRepository::class);

        if (config('ai.driver') === 'fake') {
            $this->app->bind(AIServiceInterface::class, FakeAIService::class);

            return;
        }

        $this->app->bind(AIServiceInterface::class, OpenAIService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
