<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Session\Events\SessionStarted;
use App\Notifications\Channels\SmsChannel;
use App\Services\SmsService;
use App\Services\TranslationService;
use App\View\Composers\LanguageComposer;
use App\Session\DatabaseSessionHandler;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TranslationService as singleton
        $this->app->singleton(TranslationService::class, function ($app) {
            return new TranslationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Skip subdirectory detection for local development
        // Only apply subdirectory logic for production/staging environments
        $appEnv = env('APP_ENV', 'local');
        $skipAutoDetection = env('APP_SKIP_SUBDIRECTORY_AUTO_DETECT', false);

        // Handle subdirectory hosting (e.g., /demo/)
        // This ensures asset() helper includes the subdirectory in URLs
        $subdirectory = env('APP_SUBDIRECTORY', '');

        // Auto-detect subdirectory from request if not set in env
        // Skip auto-detection if:
        // 1. Already set in env
        // 2. Local environment (unless explicitly enabled)
        // 3. Skip flag is set
        // 4. APP_URL already contains a path (not just domain)
        if (empty($subdirectory) && !$skipAutoDetection && $appEnv !== 'local' && request()) {
            $appUrl = config('app.url');
            // If APP_URL already contains a path (not just domain), don't auto-detect
            $urlPath = parse_url($appUrl, PHP_URL_PATH);
            if (empty($urlPath) || $urlPath === '/') {
                // Try to detect from SCRIPT_NAME first (more reliable for subdirectory hosting)
                if (isset($_SERVER['SCRIPT_NAME'])) {
                    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
                    if ($scriptPath !== '/' && $scriptPath !== '\\' && $scriptPath !== '.') {
                        $subdirectory = rtrim($scriptPath, '/');
                    }
                }

                // Fallback: try to detect from request URI
                if (empty($subdirectory)) {
                    $path = parse_url(request()->getRequestUri(), PHP_URL_PATH);
                    // Extract subdirectory from path (e.g., /demo/... -> /demo)
                    if (preg_match('#^/([^/]+)/#', $path, $matches)) {
                        // Check if it's not a route (common Laravel routes)
                        $commonRoutes = ['login', 'register', 'api', 'storage', 'assets', 'css', 'js', 'images'];
                        if (!in_array($matches[1], $commonRoutes)) {
                            $subdirectory = '/' . $matches[1];
                        }
                    }
                }
            }
        }

        // Set asset URL to include subdirectory
        if (!empty($subdirectory)) {
            $appUrl = config('app.url');
            // Ensure subdirectory doesn't already exist in APP_URL
            if (strpos($appUrl, $subdirectory) === false) {
                $appUrl = rtrim($appUrl, '/') . $subdirectory;
            }
            URL::forceRootUrl($appUrl);

            // Also update the public disk URL to include subdirectory
            config(['filesystems.disks.public.url' => $appUrl . '/storage']);
        } else {
            // For local development with artisan serve, don't force URL - let Laravel auto-detect
            // Only force URL if APP_URL is explicitly set and we're not in local environment
            if ($appEnv !== 'local') {
                $appUrl = config('app.url');
                if (!empty($appUrl)) {
                    URL::forceRootUrl($appUrl);
                }
            }
            // When APP_ENV is 'local' and no subdirectory, Laravel will auto-detect the URL
            // This allows artisan serve to work correctly with http://127.0.0.1:8000
        }

        // Extend the session manager to use our custom database handler
        Session::extend('database', function ($app) {
            $connection = $app['db']->connection($app['config']['session.connection']);
            $table = $app['config']['session.table'];
            $lifetime = $app['config']['session.lifetime'];
            $encrypter = $app->bound('encrypter') ? $app['encrypter'] : null;

            return new DatabaseSessionHandler($connection, $table, $lifetime, $encrypter);
        });

        // Register SMS notification channel
        Notification::extend('sms', function ($app) {
            return new SmsChannel($app->make(SmsService::class));
        });

        // Register Blade directive for auto-translation
        Blade::directive('trans', function ($expression) {
            return "<?php echo autoTranslate($expression); ?>";
        });

        // Register view composer for all views to provide translation helper
        View::composer('*', LanguageComposer::class);

        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();
    }
}