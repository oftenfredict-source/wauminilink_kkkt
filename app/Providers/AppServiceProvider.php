<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Session\Events\SessionStarted;
use App\Notifications\Channels\SmsChannel;
use App\Services\SmsService;
use App\Session\DatabaseSessionHandler;

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
    }
}