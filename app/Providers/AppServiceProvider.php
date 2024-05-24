<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (in_array(env('APP_ENV'), ['testing', 'local'])) {
            DB::listen(function (QueryExecuted $query) {
                Log::info('Query: ' . $query->sql);
            });
        }

    }
}
