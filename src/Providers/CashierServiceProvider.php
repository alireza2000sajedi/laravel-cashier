<?php

namespace Ars\Cashier\Providers;

use Illuminate\Support\ServiceProvider;

class CashierServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish migration files
        $this->publishes([
            __DIR__ . '/../../migrations' => database_path('migrations'),
        ], 'migrations');

        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../../config/otp-code.php' => config_path('otp-code.php'),
        ], 'config');
    }
}
