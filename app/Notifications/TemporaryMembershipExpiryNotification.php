<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TemporaryMembershipExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $member;
    protected $type; // 'expiring' or 'expired'
    protected $daysUntilExpiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(Member $member, $type = 'expiring', $daysUntilExpiry = null)
    {
        $this->member = $member;
        $this->type = $type;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Add email if user has email
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
        
        if ($this->type === 'expired') {
            $subject = 'Temporary Membership Expired - Action Required';
            $message = "The temporary membership for **{$this->member->full_name}** has expired on {$this->member->membership_end_date->format('F d, Y')}.";
        } else {
            $subject = 'Temporary Membership Expiring Soon';
            $message = "The temporary membership for **{$this->member->full_name}** will expire in {$this->daysUntilExpiry} days (on {$this->member->membership_end_date->format('F d, Y')}).";
        }
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($message)
            ->line('**Member Details:**')
            ->line("• Full Name: {$this->member->full_name}")
            ->line("• Membership Type: Temporary")
            ->line("• Expiry Date: {$this->member->membership_end_date->format('F d, Y')}")
            ->line("• Member ID: {$this->member->member_id}")
            ->action('View Member Details', url("/members/{$this->member->id}"))
            ->line('Please take appropriate action: extend the membership, convert to permanent, or mark as completed.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'temporary_membership_' . $this->type,
            'member_id' => $this->member->id,
            'member_name' => $this->member->full_name,
            'member_member_id' => $this->member->member_id,
            'membership_type' => 'Temporary',
            'expiry_date' => $this->member->membership_end_date->format('Y-m-d'),
            'expiry_date_formatted' => $this->member->membership_end_date->format('F d, Y'),
            'days_until_expiry' => $this->daysUntilExpiry,
            'message' => $this->type === 'expired' 
                ? "Temporary membership for {$this->member->full_name} has expired on {$this->member->membership_end_date->format('F d, Y')}."
                : "Temporary membership for {$this->member->full_name} will expire in {$this->daysUntilExpiry} days (on {$this->member->membership_end_date->format('F d, Y')}).",
        ];
    }
}
