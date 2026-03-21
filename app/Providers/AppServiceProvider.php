<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

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
        // Share the detection backend URL with all views (for browser-direct access)
        View::share('detectionBackendUrl', config('services.detection_backend.client_url', 'http://127.0.0.1:5000'));
        View::share('detectionApiKey', config('services.detection_backend.api_key', ''));

        if (env('APP_ENV') !== 'local') {
            // 1. Force generated links to be HTTPS
            URL::forceScheme('https');

            // 2. Trust Railway Proxies
            // This tells Laravel: "If the proxy says it's HTTPS, believe it."
            Request::setTrustedProxies(
                ['*'], // Trust all proxies (Railway uses dynamic IPs)
                \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
            );
        }
    }
}