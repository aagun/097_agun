<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DebtService;
use App\Services\Impl\DebtServiceImpl;

class DebtServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(
            DebtService::class,
            fn ($app) => new DebtServiceImpl()
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
