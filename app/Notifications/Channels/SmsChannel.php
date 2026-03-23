<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\SmsService;

class SmsChannel
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);

        if (empty($message)) {
            return;
        }

        // Send SMS using the SMS service if phone number is available
        if (!empty($notifiable->phone_number)) {
            $this->smsService->send($notifiable->phone_number, $message);
        } else {
            \Log::warning("SmsChannel: Skipping SMS send - no phone number for notifiable", [
                'notifiable_id' => $notifiable->id ?? 'unknown',
                'notification' => get_class($notification)
            ]);
        }
    }
}



