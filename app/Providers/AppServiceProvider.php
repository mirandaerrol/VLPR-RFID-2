<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // FIX for Railway 419 Error:
        // Force Laravel to use HTTPS if we are not on a local computer.
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}