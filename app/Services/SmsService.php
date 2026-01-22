<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\SettingsService;
use Carbon\Carbon;

class SmsService
{
    /**
     * Send an SMS using configured provider settings.
     */
    public function send(string $toPhoneE164, string $message): bool
    {
        try {
            $result = $this->sendInternal($toPhoneE164, $message, false);
            return $result['ok'] === true;
        } catch (\Throwable $e) {
            Log::error('SMS send exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS and return detailed response for debugging.
     */
    public function sendDebug(string $toPhoneE164, string $message): array
    {
        try {
            return $this->sendInternal($toPhoneE164, $message, true);
        } catch (\Throwable $e) {
            Log::error('SMS send exception (debug): ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send leader appointment notification SMS
     */
    public function sendLeaderAppointmentNotification(string $toPhoneE164, string $leaderName, string $position, string $churchName): bool
    {
        try {
            $message = $this->buildLeaderAppointmentMessage($leaderName, $position, $churchName);
            return $this->send($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Leader appointment SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send leader appointment notification SMS with debug info
     */
    public function sendLeaderAppointmentNotificationDebug(string $toPhoneE164, string $leaderName, string $position, string $churchName): array
    {
        try {
            $message = $this->buildLeaderAppointmentMessage($leaderName, $position, $churchName);
            return $this->sendDebug($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Leader appointment SMS debug failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send payment approval notification SMS to member
     */
    public function sendPaymentApprovalNotification(string $toPhoneE164, string $memberName, string $paymentType, float $amount, string $paymentDate): bool
    {
        try {
            $message = $this->buildPaymentApprovalMessage($memberName, $paymentType, $amount, $paymentDate);
            return $this->send($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Payment approval SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment approval notification SMS with debug info
     */
    public function sendPaymentApprovalNotificationDebug(string $toPhoneE164, string $memberName, string $paymentType, float $amount, string $paymentDate): array
    {
        try {
            $message = $this->buildPaymentApprovalMessage($memberName, $paymentType, $amount, $paymentDate);
            return $this->sendDebug($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Payment approval SMS debug failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send weekly assignment duty notification SMS
     */
    public function sendWeeklyAssignmentNotification(string $toPhoneE164, string $leaderName, string $startDate, string $endDate, string $churchName): bool
    {
        try {
            $message = $this->buildWeeklyAssignmentMessage($leaderName, $startDate, $endDate, $churchName);
            return $this->send($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Weekly assignment SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send weekly assignment duty notification SMS with debug info
     */
    public function sendWeeklyAssignmentNotificationDebug(string $toPhoneE164, string $leaderName, string $startDate, string $endDate, string $churchName): array
    {
        try {
            $message = $this->buildWeeklyAssignmentMessage($leaderName, $startDate, $endDate, $churchName);
            return $this->sendDebug($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Weekly assignment SMS debug failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send pledge reminder notification SMS
     */
    public function sendPledgeReminderNotification(string $toPhoneE164, string $memberName, string $pledgeType, float $remainingAmount, string $dueDate): bool
    {
        try {
            $message = $this->buildPledgeReminderMessage($memberName, $pledgeType, $remainingAmount, $dueDate);
            return $this->send($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Pledge reminder SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send pledge reminder notification SMS with debug info
     */
    public function sendPledgeReminderNotificationDebug(string $toPhoneE164, string $memberName, string $pledgeType, float $remainingAmount, string $dueDate): array
    {
        try {
            $message = $this->buildPledgeReminderMessage($memberName, $pledgeType, $remainingAmount, $dueDate);
            return $this->sendDebug($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Pledge reminder SMS debug failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send promise guest notification SMS
     */
    public function sendPromiseGuestNotification(string $toPhoneE164, string $guestName, \App\Models\SundayService $service): bool
    {
        try {
            $message = $this->buildPromiseGuestNotificationMessage($guestName, $service->service_date, $service);
            return $this->send($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Promise guest SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send promise guest notification SMS with debug info
     */
    public function sendPromiseGuestNotificationDebug(string $toPhoneE164, string $guestName, \App\Models\SundayService $service): array
    {
        try {
            $message = $this->buildPromiseGuestNotificationMessage($guestName, $service->service_date, $service);
            return $this->sendDebug($toPhoneE164, $message);
        } catch (\Throwable $e) {
            Log::error('Promise guest SMS debug failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Build promise guest notification message template
     */
    public function buildPromiseGuestNotificationMessage(string $guestName, $serviceDate, \App\Models\SundayService $service = null): string
    {
        // Get custom template from settings or use default
        $template = SettingsService::get('sms_promise_guest_template', 
            "Shalom {{name}}, tunakukumbusha kuhusu ahadi yako ya kuhudhuria ibada ya Jumapili tarehe {{date}}.\n\n" .
            "Tunatarajia kukuona na kukushukuru kwa kuwa sehemu ya familia yetu ya kiroho.\n\n" .
            "{{service_details}}\n\n" .
            "Karibu sana! Mungu akubariki."
        );

        // Format date in Swahili format (dd/mm/yyyy)
        if ($serviceDate instanceof \Carbon\Carbon) {
            $formattedDate = $serviceDate->format('d/m/Y');
        } elseif ($service && $service->service_date) {
            $formattedDate = $service->service_date->format('d/m/Y');
        } else {
            $formattedDate = date('d/m/Y', strtotime($serviceDate));
        }

        // Build service details
        $serviceDetails = [];
        if ($service) {
            if ($service->start_time) {
                $serviceDetails[] = "Muda: " . $service->start_time->format('H:i');
            }
            if ($service->venue) {
                $serviceDetails[] = "Mahali: " . $service->venue;
            }
            if ($service->theme) {
                $serviceDetails[] = "Mada: " . $service->theme;
            }
        }
        
        $serviceDetailsText = !empty($serviceDetails) 
            ? implode("\n", $serviceDetails) 
            : "Tarehe: " . $formattedDate;

        // Replace placeholders
        $message = str_replace('{{name}}', $guestName, $template);
        $message = str_replace('{{date}}', $formattedDate, $message);
        $message = str_replace('{{service_details}}', $serviceDetailsText, $message);

        return $message;
    }

    /**
     * Build the payment approval message template
     */
    private function buildPaymentApprovalMessage(string $memberName, string $paymentType, float $amount, string $paymentDate): string
    {
        // Get custom template from settings or use default
        $template = SettingsService::get('sms_payment_approval_template', 
            "Hongera {{name}}! {{payment_type}} yako ya TZS {{amount}} tarehe {{date}} imethibitishwa na imepokelewa kikamilifu.\n" .
            "Asante kwa mchango wako wa kiroho. Mungu akubariki!"
        );

        // Format amount with commas
        $formattedAmount = number_format($amount, 0);
        
        // Format date
        $formattedDate = date('d/m/Y', strtotime($paymentDate));

        // Replace placeholders
        $message = str_replace('{{name}}', $memberName, $template);
        $message = str_replace('{{payment_type}}', $paymentType, $message);
        $message = str_replace('{{amount}}', $formattedAmount, $message);
        $message = str_replace('{{date}}', $formattedDate, $message);

        return $message;
    }

    /**
     * Build the leader appointment message template
     */
    private function buildLeaderAppointmentMessage(string $leaderName, string $position, string $churchName): string
    {
        // Get custom template from settings or use default
        $template = SettingsService::get('sms_leader_appointment_template', 
            "Hongera {{name}}! Umechaguliwa rasmi kuwa {{position}} wa kanisa la {{church_name}}.\n\n" .
            "Mungu akupe hekima, ujasiri na neema katika kutimiza wajibu huu wa kiroho.\n\n" .
            "Tunakuombea uongozi wenye upendo, umoja na maendeleo katika huduma ya Bwana."
        );

        // Translate position to Swahili
        $swahiliPosition = $this->translatePositionToSwahili($position);

        // Replace placeholders
        $message = str_replace('{{name}}', $leaderName, $template);
        $message = str_replace('{{position}}', $swahiliPosition, $message);
        $message = str_replace('{{church_name}}', $churchName, $message);

        return $message;
    }

    /**
     * Translate English position to Swahili
     */
    private function translatePositionToSwahili(string $position): string
    {
        $translations = [
            'pastor' => 'Mchungaji',
            'assistant_pastor' => 'Msaidizi wa Mchungaji',
            'secretary' => 'Katibu',
            'assistant_secretary' => 'Msaidizi wa Katibu',
            'treasurer' => 'Mweka Hazina',
            'assistant_treasurer' => 'Msaidizi wa Mweka Hazina',
            'elder' => 'Mzee wa Kanisa',
            'deacon' => 'Shamashi',
            'deaconess' => 'Shamasha',
            'youth_leader' => 'Kiongozi wa Vijana',
            'children_leader' => 'Kiongozi wa Watoto',
            'worship_leader' => 'Kiongozi wa Ibada',
            'choir_leader' => 'Kiongozi wa Kwaya',
            'usher_leader' => 'Kiongozi wa Wakaribishaji',
            'evangelism_leader' => 'Kiongozi wa Uinjilisti',
            'prayer_leader' => 'Kiongozi wa Maombi',
            'other' => 'Kiongozi'
        ];

        return $translations[$position] ?? $position;
    }

    /**
     * Build the weekly assignment duty message template
     */
    private function buildWeeklyAssignmentMessage(string $leaderName, string $startDate, string $endDate, string $churchName): string
    {
        // Format dates in Swahili format (dd/mm/yyyy)
        $formattedStartDate = date('d/m/Y', strtotime($startDate));
        $formattedEndDate = date('d/m/Y', strtotime($endDate));

        // Shortened SMS message (without church name)
        $message = "Shalom {$leaderName}, Umechaguliwa kuwa kiongozi wa shughuli za kanisa wiki ya {$formattedStartDate}–{$formattedEndDate}. Mungu akuongoze.";

        return $message;
    }

    /**
     * Build the pledge reminder message template
     */
    private function buildPledgeReminderMessage(string $memberName, string $pledgeType, float $remainingAmount, string $dueDate): string
    {
        // Get custom template from settings or use default
        $template = SettingsService::get('sms_pledge_reminder_template', 
            "Shalom {{name}}, tunakukumbusha kuhusu ahadi yako ya {{pledge_type}}; kiasi kilichobaki ni {{remaining_amount}} na mwisho ni {{due_date}}. Mungu akubariki sana kwa moyo wako wa utoaji"
        );

        // Translate pledge type to Swahili
        $swahiliPledgeType = $this->translatePledgeTypeToSwahili($pledgeType);

        // Format amount with commas
        $formattedAmount = 'TZS ' . number_format($remainingAmount, 0);
        
        // Format date in Swahili format (dd/mm/yyyy) or use provided text
        if ($dueDate && $dueDate !== 'Hakuna tarehe maalum' && strtotime($dueDate) !== false) {
            $formattedDate = date('d/m/Y', strtotime($dueDate));
        } else {
            $formattedDate = $dueDate ?: 'Hakuna tarehe maalum';
        }

        // Replace placeholders
        $message = str_replace('{{name}}', $memberName, $template);
        $message = str_replace('{{pledge_type}}', $swahiliPledgeType, $message);
        $message = str_replace('{{remaining_amount}}', $formattedAmount, $message);
        $message = str_replace('{{due_date}}', $formattedDate, $message);

        return $message;
    }

    /**
     * Translate English pledge type to Swahili
     */
    private function translatePledgeTypeToSwahili(string $pledgeType): string
    {
        $translations = [
            'building' => 'ujenzi',
            'mission' => 'misioni',
            'missions' => 'misioni',
            'special' => 'mradi maalum',
            'special_project' => 'mradi maalum',
            'general' => 'jumla',
            'other' => 'nyingine'
        ];

        return $translations[strtolower($pledgeType)] ?? $pledgeType;
    }

    private function sendInternal(string $toPhoneE164, string $message, bool $debug): array
    {
        try {
            $enabled = SettingsService::get('enable_sms_notifications', false);
            if (!$enabled) {
                Log::info('SMS sending skipped: feature disabled');
                return [
                    'ok' => false,
                    'reason' => 'disabled'
                ];
            }

            $apiUrl = SettingsService::get('sms_api_url');
            $senderId = SettingsService::get('sms_sender_id', 'KKKT Ushirika wa Longuo');
            $apiKey = SettingsService::get('sms_api_key');
            $username = SettingsService::get('sms_username');
            $password = SettingsService::get('sms_password');

            // Check for username/password authentication first (primary method)
            if (!empty($username) && !empty($password)) {
                // Provide a safe default URL for username/password flow if missing
                if (empty($apiUrl)) {
                    $apiUrl = 'https://messaging-service.co.tz/link/sms/v1/text/single';
                }
            } elseif (!empty($apiUrl) && !empty($apiKey)) {
                // Use API key authentication if available
                // No action needed, will use Bearer token auth
            } else {
                // Neither authentication method is available
                Log::warning('SMS config missing: Need either (username and password) or (apiUrl and apiKey)');
                return [
                    'ok' => false,
                    'reason' => 'config_missing'
                ];
            }

            // Normalize phone: API expects 255XXXXXXXXX (no plus). Convert +255... to 255...
            $normalizedPhone = ltrim($toPhoneE164, '+');

            // If username/password are provided, use GET with query params (messaging-service.co.tz pattern)
            if (!empty($username) && !empty($password)) {
                // Build URL with query parameters (matching the exact API format)
                // http_build_query automatically URL encodes all values (password: Emca@#12 becomes Emca@%2312)
                $queryParams = http_build_query([
                    'username' => $username,
                    'password' => $password, // Automatically URL encoded by http_build_query
                    'from' => $senderId,
                    'to' => $normalizedPhone,
                    'text' => $message, // Automatically URL encoded by http_build_query
                ]);
                
                // Construct full URL
                $fullUrl = $apiUrl . '?' . $queryParams;
                
                // Use cURL directly to match the exact API format
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $fullUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ]);
                
                $responseBody = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);
                
                if ($curlError) {
                    Log::error('SMS cURL error', [
                        'error' => $curlError,
                        'to' => $toPhoneE164
                    ]);
                    return $debug
                        ? ['ok' => false, 'error' => 'cURL Error: ' . $curlError, 'request' => ['url' => $fullUrl]]
                        : ['ok' => false];
                }
                
                $requestMeta = [
                    'method' => 'GET',
                    'url' => $fullUrl,
                    'query_params' => [
                        'username' => $username,
                        'password' => '***', // Hide password in logs
                        'from' => $senderId,
                        'to' => $normalizedPhone,
                        'text' => $message
                    ]
                ];
                
                // Check if request was successful (HTTP 200-299)
                if ($httpCode >= 200 && $httpCode < 300) {
                    // Parse response to check for rejection
                    $responseData = json_decode($responseBody, true);
                    $isRejected = false;
                    $rejectionReason = null;
                    
                    // Check if message was rejected by provider
                    if (isset($responseData['messages']) && is_array($responseData['messages'])) {
                        foreach ($responseData['messages'] as $msg) {
                            if (isset($msg['status'])) {
                                $status = $msg['status'];
                                // Check for rejection statuses
                                if (isset($status['groupName']) && 
                                    (stripos($status['groupName'], 'REJECTED') !== false || 
                                     stripos($status['groupName'], 'FAILED') !== false ||
                                     stripos($status['groupName'], 'ERROR') !== false)) {
                                    $isRejected = true;
                                    $rejectionReason = $status['description'] ?? $status['name'] ?? 'Message rejected by provider';
                                    break;
                                }
                            }
                        }
                    }
                    
                    if ($isRejected) {
                        Log::error('SMS rejected by provider', [
                            'to' => $toPhoneE164,
                            'reason' => $rejectionReason,
                            'response' => $responseBody
                        ]);
                        return $debug
                            ? ['ok' => false, 'status' => $httpCode, 'body' => $responseBody, 'reason' => $rejectionReason, 'request' => $requestMeta]
                            : ['ok' => false, 'reason' => $rejectionReason];
                    }
                    
                    Log::info('SMS sent successfully', ['to' => $toPhoneE164]);
                    return $debug
                        ? ['ok' => true, 'status' => $httpCode, 'body' => $responseBody, 'request' => $requestMeta]
                        : ['ok' => true];
                } else {
                    // HTTP error
                    Log::error('SMS send failed', [
                        'status' => $httpCode,
                        'body' => $responseBody,
                    ]);
                    return $debug
                        ? ['ok' => false, 'status' => $httpCode, 'body' => $responseBody, 'request' => $requestMeta]
                        : ['ok' => false];
                }
            } else {
                // Default: Bearer POST with JSON
                $payload = [
                    'to' => $normalizedPhone,
                    'message' => $message,
                    'sender' => $senderId,
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                ])->timeout(15)->post($apiUrl, $payload);
                $requestMeta = ['method' => 'POST', 'url' => $apiUrl, 'payload' => $payload];
                
                if ($response->successful()) {
                    // Check response body for rejection status
                    $responseBody = $response->body();
                    $responseData = json_decode($responseBody, true);
                    
                    // Check if message was rejected by provider
                    $isRejected = false;
                    $rejectionReason = null;
                    
                    if (isset($responseData['messages']) && is_array($responseData['messages'])) {
                        foreach ($responseData['messages'] as $msg) {
                            if (isset($msg['status'])) {
                                $status = $msg['status'];
                                // Check for rejection statuses
                                if (isset($status['groupName']) && 
                                    (stripos($status['groupName'], 'REJECTED') !== false || 
                                     stripos($status['groupName'], 'FAILED') !== false ||
                                     stripos($status['groupName'], 'ERROR') !== false)) {
                                    $isRejected = true;
                                    $rejectionReason = $status['description'] ?? $status['name'] ?? 'Message rejected by provider';
                                    break;
                                }
                            }
                        }
                    }
                    
                    if ($isRejected) {
                        Log::error('SMS rejected by provider', [
                            'to' => $toPhoneE164,
                            'reason' => $rejectionReason,
                            'response' => $responseBody
                        ]);
                        return $debug
                            ? ['ok' => false, 'status' => $response->status(), 'body' => $responseBody, 'reason' => $rejectionReason, 'request' => $requestMeta]
                            : ['ok' => false, 'reason' => $rejectionReason];
                    }
                    
                    Log::info('SMS sent successfully', ['to' => $toPhoneE164]);
                    return $debug
                        ? ['ok' => true, 'status' => $response->status(), 'body' => $responseBody, 'request' => $requestMeta]
                        : ['ok' => true];
                }

                Log::error('SMS send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $debug
                    ? ['ok' => false, 'status' => $response->status(), 'body' => $response->body(), 'request' => $requestMeta]
                    : ['ok' => false];
            }
        } catch (\Throwable $e) {
            Log::error('SMS send exception: ' . $e->getMessage());
            return $debug ? ['ok' => false, 'error' => $e->getMessage()] : ['ok' => false];
        }
    }
}


