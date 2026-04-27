<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Events\BackupHasFailed;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class BackupNotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(BackupWasSuccessful::class, function (BackupWasSuccessful $event) {
            $this->sendSmsNotification("Success: Backup for " . config('backup.backup.name') . " completed successfully on " . now()->format('Y-m-d H:i:s'));
        });

        Event::listen(BackupHasFailed::class, function (BackupHasFailed $event) {
            $error = $event->exception->getMessage();
            $this->sendSmsNotification("FAILED: Backup for " . config('backup.backup.name') . " failed! Error: " . $error);
        });
    }

    /**
     * Send SMS notification using the SmsService
     */
    protected function sendSmsNotification(string $message): void
    {
        $phone = env('BACKUP_NOTIFICATION_PHONE');

        if (empty($phone)) {
            Log::info('Backup SMS notification skipped: no phone number configured.');
            return;
        }

        try {
            $smsService = app(SmsService::class);
            $smsService->send($phone, $message);
            Log::info('Backup SMS notification sent to ' . $phone);
        } catch (\Throwable $e) {
            Log::error('Failed to send backup SMS notification: ' . $e->getMessage());
        }
    }
}
