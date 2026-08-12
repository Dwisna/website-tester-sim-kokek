<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Paksa Laravel selalu generate URL/asset pakai https://
        // Perlu karena Railway terminate SSL di proxy, tapi request
        // masuk ke container Laravel sebagai http:// biasa.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}