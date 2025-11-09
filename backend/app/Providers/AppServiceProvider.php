<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\AuthorizationService::class, fn() => new \App\Services\AuthorizationService());
        $this->app->singleton(\App\Services\NotificationService::class, fn() => new \App\Services\NotificationService());
        $this->app->singleton(\App\Services\TransactionService::class, function ($app) {
            return new \App\Services\TransactionService(
                $app->make(\App\Services\AuthorizationService::class),
                $app->make(\App\Services\NotificationService::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
