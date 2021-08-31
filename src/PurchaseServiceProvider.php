<?php

namespace Fh\Purchase;

use Illuminate\Support\ServiceProvider;

class PurchaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerMigrations();
        $this->registerPublishing();
    }

    private function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/purchases.php' => config_path('purchases.php'),
            ], 'purchases-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'purchases-migrations');

            $this->publishes([
                __DIR__ . '/../database/factories' => database_path('factories'),
            ], 'purchases-factories');
        }
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/purchases.php',
            'purchases'
        );
    }
}
