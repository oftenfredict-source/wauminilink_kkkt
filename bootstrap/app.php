<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append([
            // Example: \App\Http\Middleware\TrustProxies::class,
            // Example: \Fruitcake\Cors\HandleCors::class,
        ]);

        $middleware->trustProxies(at: '*');

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
         * Add CheckSessionRevoked middleware FIRST to check if session was revoked
         * This forces logout if session was deleted by admin
         * Must run before UpdateSessionUserId to detect revoked sessions
         */
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\CheckSessionRevoked::class,
        ]);

        /**
         * Add UpdateSessionUserId middleware after StartSession
         * This ensures user_id is set in sessions table for tracking
         */
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\UpdateSessionUserId::class,
        ]);

        /**
         * Add SetLocale middleware to set application locale from session
         * This allows users to switch between English and Swahili
         */
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SetLocale::class,
        ]);

        /**
         * API middleware group (if any)
         */
        $middleware->appendToGroup('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        /**
         * Register middleware aliases
         */
        $middleware->alias([
            'treasurer' => \App\Http\Middleware\TreasurerMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'branch.access' => \App\Http\Middleware\BranchAccess::class,
        ]);

        /**
         * Append ActivityLogMiddleware to web group to track all activities
         */
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\ActivityLogMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Helper function to redirect to login for 419 errors
        $redirectToLogin = function ($request, $message = 'Your session has expired. Please login again.') {
            // For AJAX/JSON requests, return a JSON response that triggers redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your session has expired. Redirecting to login...',
                    'error' => 'Page Expired',
                    'redirect' => route('login')
                ], 419);
            }

            // For normal form requests, always redirect to login page
            // Check if we're already on login/OTP/forgot-password pages to avoid redirect loops
            $currentPath = $request->path();
            $loginRelatedPaths = ['login', 'login/otp', 'login/otp/verify', 'forgot-password', 'reset-password'];

            $isLoginPage = false;
            foreach ($loginRelatedPaths as $path) {
                if (str_starts_with($currentPath, $path)) {
                    $isLoginPage = true;
                    break;
                }
            }

            // If already on login page, just refresh it
            if ($isLoginPage) {
                return redirect()->route('login')
                    ->with('error', 'Your session has expired. Please try again.');
            }

            // Otherwise, redirect to login page
            return redirect()->route('login')
                ->with('error', $message);
        };

        // Friendly handling for CSRF token mismatch (HTTP 419 "Page Expired")
        // Always redirect to login page instead of showing error page
        // This handler has priority and catches TokenMismatchException first
        $exceptions->render(function (TokenMismatchException $e, $request) use ($redirectToLogin) {
            return $redirectToLogin($request);
        });

        // Also handle HTTP 419 status code directly (catch-all for any 419 errors)
        // This catches any other exceptions that result in 419 status
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) use ($redirectToLogin) {
            if ($e->getStatusCode() === 419) {
                return $redirectToLogin($request);
            }

            return null; // Let other exceptions be handled normally
        });
    })
    ->create();
