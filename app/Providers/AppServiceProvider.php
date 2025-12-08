<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 1. Import the Paginator
use Illuminate\Pagination\Paginator;

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
        // 2. Tell Laravel to use Bootstrap styles (No SVGs)
        Paginator::useBootstrap();
    }
}