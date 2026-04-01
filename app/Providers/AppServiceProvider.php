<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // Force HTTPS quand APP_URL est en https (ngrok, production)
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
