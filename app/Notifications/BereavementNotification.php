<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\BereavementEvent;
use App\Services\SmsService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Log;

class BereavementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $bereavementEvent;
    protected $type; // 'created', 'reminder', 'deadline_approaching'

    public function __construct(BereavementEvent $bereavementEvent, $type = 'created')
    {
        $this->bereavementEvent = $bereavementEvent;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->getSubject();
        $greeting = $this->getGreeting();
        $message = $this->getMessage();

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->line('Bereavement Details:')
            ->line('🕊️ Deceased/Affected: ' . $this->bereavementEvent->deceased_name)
            ->line('📅 Incident Date: ' . $this->bereavementEvent->incident_date->format('F j, Y'))
            ->line('📆 Contribution Period: ' . $this->bereavementEvent->contribution_start_date->format('M j') . ' - ' . $this->bereavementEvent->contribution_end_date->format('M j, Y'))
            ->line('⏰ Days Remaining: ' . $this->bereavementEvent->days_remaining . ' days')
            ->when($this->bereavementEvent->family_details, function ($mail) {
                return $mail->line('👨‍👩‍👧‍👦 Family Details: ' . $this->bereavementEvent->family_details);
            })
            ->when($this->bereavementEvent->related_departments, function ($mail) {
                return $mail->line('🏛️ Related Departments: ' . $this->bereavementEvent->related_departments);
            })
            ->action('View Bereavement Event', url('/bereavement/' . $this->bereavementEvent->id))
            ->line('Thank you for your support during this difficult time.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'bereavement_' . $this->type,
            'bereavement_event_id' => $this->bereavementEvent->id,
            'deceased_name' => $this->bereavementEvent->deceased_name,
            'incident_date' => $this->bereavementEvent->incident_date->format('Y-m-d'),
            'contribution_end_date' => $this->bereavementEvent->contribution_end_date->format('Y-m-d'),
            'days_remaining' => $this->bereavementEvent->days_remaining,
            'message' => $this->getMessage(),
        ];
    }

    /**
     * Send SMS notification
     */
    public function sendSmsNotification($member)
    {
        try {
            if (!SettingsService::get('enable_sms_notifications', false)) {
                Log::info('SMS notifications disabled, skipping bereavement SMS');
                return false;
            }

            $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');
            $message = $this->buildSmsMessage($member, $churchName);

            $smsService = app(SmsService::class);
            $result = $smsService->send($member->phone_number, $message);

            if ($result) {
                Log::info('Bereavement SMS sent to member', [
                    'member_id' => $member->id,
                    'phone' => $member->phone_number,
                    'bereavement_event_id' => $this->bereavementEvent->id
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send bereavement SMS notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build SMS message in Swahili
     */
    private function buildSmsMessage($member, $churchName)
    {
        $memberName = $member->full_name;
        $deceasedName = $this->bereavementEvent->deceased_name;
        $endDate = $this->bereavementEvent->contribution_end_date->format('d/m/Y');
        $daysRemaining = $this->bereavementEvent->days_remaining;

        switch ($this->type) {
            case 'created':
                $message = "Shalom {$memberName}, kuna tukio la kusikitisha la kifo cha {$deceasedName}.\n";
                $message .= "Tunaomba msaada wako kwa familia. Muda wa mchango ni hadi {$endDate} ({$daysRemaining} siku zimebaki).\n";
                $message .= "Asante kwa kuwa sehemu ya familia ya Mungu. - {$churchName}";
                break;

            case 'reminder':
                $message = "Shalom {$memberName}, kumbuka mchango wa kusikitisha kwa {$deceasedName}.\n";
                $message .= "Muda unaoendelea hadi {$endDate} ({$daysRemaining} siku zimebaki).\n";
                $message .= "Asante. - {$churchName}";
                break;

            case 'deadline_approaching':
                $message = "Shalom {$memberName}, muda wa mchango wa kusikitisha kwa {$deceasedName} unakaribia kuisha.\n";
                $message .= "Tafadhali fanya mchango wako kabla ya {$endDate} ({$daysRemaining} siku zimebaki).\n";
                $message .= "Asante. - {$churchName}";
                break;

            default:
                $message = "Shalom {$memberName}, kuna tukio la kusikitisha la kifo cha {$deceasedName}.\n";
                $message .= "Muda wa mchango ni hadi {$endDate}. Asante. - {$churchName}";
        }

        return $message;
    }

    private function getSubject()
    {
        switch ($this->type) {
            case 'created':
                return '🕊️ Bereavement Notice: ' . $this->bereavementEvent->deceased_name;
            case 'reminder':
                return '⏰ Reminder: Bereavement Contribution';
            case 'deadline_approaching':
                return '⚠️ Deadline Approaching: Bereavement Contribution';
            default:
                return 'Bereavement Notification';
        }
    }

    private function getGreeting()
    {
        return 'Hello Church Member!';
    }

    private function getMessage()
    {
        switch ($this->type) {
            case 'created':
                return 'We are saddened to inform you of a bereavement in our church community. Your support and contributions are greatly appreciated during this difficult time.';
            case 'reminder':
                return 'This is a friendly reminder about the ongoing bereavement contribution. Your support means a lot to the affected family.';
            case 'deadline_approaching':
                return 'The contribution deadline is approaching. Please submit your contribution if you haven\'t already.';
            default:
                return 'You have a notification about a bereavement event.';
        }
    }
}





