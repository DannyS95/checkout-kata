<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Managers\CartInstanceManager;
use Illuminate\Contracts\Foundation\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
