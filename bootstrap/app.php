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
        /**
         * Global middleware applied to every request
         */
        $middleware->append([
            // Example: \App\Http\Middleware\TrustProxies::class,
            // Example: \Fruitcake\Cors\HandleCors::class,
        ]);

        /**
         * Web middleware group
         */
        $middleware->appendToGroup('web', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, // <-- only framework CSRF
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Remove PreventBackHistory from here
        ]);

        /**
         * API middleware group (if any)
         */
        $middleware->appendToGroup('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Optional custom exception handling
    })
    ->create();
