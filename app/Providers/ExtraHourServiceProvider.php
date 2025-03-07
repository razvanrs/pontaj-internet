<?php

namespace App\Providers;

use App\Services\ExtraHourService;
use App\Services\ReconciliationService;
use Illuminate\Support\ServiceProvider;

class ExtraHourServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ExtraHourService::class, function ($app) {
            return new ExtraHourService();
        });

        $this->app->singleton(ReconciliationService::class, function ($app) {
            return new ReconciliationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
