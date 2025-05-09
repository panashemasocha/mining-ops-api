<?php

namespace App\Providers;

use App\Services\RequisitionStatsService;
use Illuminate\Support\ServiceProvider;

class RequisitionStatsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RequisitionStatsService::class, function ($app) {
            return new RequisitionStatsService();
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