<?php
// app/Providers/FcmServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FcmService;

class FcmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FcmService::class, function ($app) {
            return new FcmService();
        });

        // You can also add an alias
        $this->app->alias(FcmService::class, 'fcm');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}