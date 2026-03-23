<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\SmsService;

class FinancialApprovalNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Add email if pastor has email
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        // Add SMS if pastor has phone number
        if (!empty($notifiable->phone_number)) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $type = ucfirst($this->data['type']);
        $amount = number_format($this->data['amount'], 0);
        $date = $this->data['date']->format('M d, Y');
        $memberName = $this->data['member_name'];
        $recordedBy = $this->data['recorded_by'];

        $mailMessage = (new MailMessage)
            ->subject("New {$type} Requires Approval - Waumini Link")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("A new {$type} has been recorded and requires your approval.")
            ->line("**Details:**")
            ->line("- Amount: TZS {$amount}")
            ->line("- Date: {$date}")
            ->line("- Member: {$memberName}")
            ->line("- Recorded by: {$recordedBy}");

        // Add fund breakdown if available
        if (isset($this->data['fund_breakdown']) && !empty($this->data['fund_breakdown'])) {
            $mailMessage->line("**Fund Allocation Breakdown:**");
            foreach ($this->data['fund_breakdown'] as $allocation) {
                $primaryIndicator = $allocation['is_primary'] ? ' (Primary)' : '';
                $mailMessage->line("- " . ucfirst(str_replace('_', ' ', $allocation['offering_type'])) . $primaryIndicator . ": TZS " . number_format($allocation['amount']));
            }
        }

        $mailMessage->action('Review & Approve', url('/finance/approval/dashboard'))
            ->line('Please log in to review and approve this financial record.')
            ->line('Thank you for using Waumini Link!');

        return $mailMessage;
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        try {
            // Translate financial type to Swahili
            $swahiliType = $this->translateFinancialTypeToSwahili($this->data['type']);

            // Ensure amount is properly formatted - handle null/0 cases
            $amountValue = $this->data['amount'] ?? 0;
            if ($amountValue <= 0) {
                \Log::warning('Financial approval SMS with zero or missing amount', [
                    'type' => $this->data['type'],
                    'record_id' => $this->data['record_id'] ?? null,
                    'amount' => $amountValue,
                    'data' => $this->data
                ]);
            }
            $amount = number_format($amountValue, 0);
            $memberName = $this->data['member_name'] ?? 'Mwanachama';

            $message = "Habari Mchungaji! Kuna {$swahiliType} ya TZS {$amount} kutoka kwa {$memberName} inahitaji uthibitisho wako. Tafadhali ingia kwenye mfumo wa Waumini Link ili kuithibitisha. Asante!";

            // The SmsChannel handles the actual sending using the message returned here.
            // Manually calling $smsService->send here causes duplicate SMS messages.

            return $message;
        } catch (\Exception $e) {
            \Log::error('Error generating financial approval SMS content: ' . $e->getMessage(), [
                'type' => $this->data['type'] ?? 'unknown',
                'record_id' => $this->data['record_id'] ?? null,
                'amount' => $this->data['amount'] ?? null,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return "Financial approval notification failed to send via SMS";
        }
    }

    /**
     * Translate financial type to Swahili
     */
    private function translateFinancialTypeToSwahili(string $type): string
    {
        $translations = [
            'tithe' => 'Zaka',
            'offering' => 'Sadaka',
            'donation' => 'Michango',
            'expense' => 'Matumizi',
            'budget' => 'Bajeti',
            'pledge' => 'Ahadi'
        ];

        return $translations[$type] ?? ucfirst($type);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'type' => 'financial_approval',
            'financial_type' => $this->data['type'],
            'record_id' => $this->data['record_id'],
            'amount' => $this->data['amount'],
            'date' => $this->data['date']->format('Y-m-d'),
            'member_name' => $this->data['member_name'],
            'recorded_by' => $this->data['recorded_by'],
            'message' => "New {$this->data['type']} requires approval - TZS " . number_format($this->data['amount'], 0),
            'action_url' => '/finance/approval/dashboard'
        ];

        // Add fund breakdown if available
        if (isset($this->data['fund_breakdown']) && !empty($this->data['fund_breakdown'])) {
            $data['fund_breakdown'] = $this->data['fund_breakdown'];
            $data['message'] .= " (with fund allocation details)";
        }

        return $data;
    }
}








