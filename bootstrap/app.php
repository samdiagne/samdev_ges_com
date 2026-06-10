<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Faire confiance à tous les proxies (ngrok, load balancer, etc.)
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'shop/payment/webhook',
        ]);

        $middleware->alias([
            'admin'        => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'gestionnaire' => \App\Http\Middleware\EnsureUserIsGestionnaire::class,
            'active'       => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
