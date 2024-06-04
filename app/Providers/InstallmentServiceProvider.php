<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\InstallmentService;
use App\Services\Impl\InstallmentServiceImpl;

class InstallmentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(
            InstallmentService::class,
            fn ($app) => new InstallmentServiceImpl()
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
