<?php

namespace App\Notifications;

use App\Models\Leader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaderAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $leader;

    /**
     * Create a new notification instance.
     */
    public function __construct(Leader $leader)
    {
        $this->leader = $leader;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Add email if member has email
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $churchName = \App\Services\SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');
        
        return (new MailMessage)
            ->subject('Hongera! Umechaguliwa kuwa Kiongozi')
            ->greeting("Shalom {$notifiable->full_name},")
            ->line("Hongera! Umechaguliwa rasmi kuwa **{$this->leader->position_display}** wa kanisa la {$churchName}.")
            ->line("Tarehe ya uteuzi: {$this->leader->appointment_date->format('d M Y')}")
            ->line("Mungu akupe hekima, ujasiri na neema katika kutimiza wajibu huu wa kiroho.")
            ->line("Tunaamini uongozi wako utaleta umoja, upendo, na maendeleo katika huduma ya Bwana.")
            ->salutation("Baraka,\nTimu ya Uongozi");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $churchName = \App\Services\SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');
        
        return [
            'leader_id' => $this->leader->id,
            'member_id' => $this->leader->member_id,
            'position' => $this->leader->position,
            'position_display' => $this->leader->position_display,
            'appointment_date' => $this->leader->appointment_date->format('Y-m-d'),
            'message' => "Hongera! Umechaguliwa kuwa {$this->leader->position_display} wa kanisa la {$churchName}.",
            'type' => 'leader_appointment',
            'church_name' => $churchName
        ];
    }
}





