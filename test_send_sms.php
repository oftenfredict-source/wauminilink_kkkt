<?php

/**
 * Test SMS Sending
 * Usage: php test_send_sms.php [phone_number]
 * Example: php test_send_sms.php +255614863345
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "========================================\n";
    echo "SMS Sending Test\n";
    echo "========================================\n\n";
    
    // Check SMS configuration
    echo "Checking SMS Configuration...\n\n";
    
    $enabled = \App\Services\SettingsService::get('enable_sms_notifications', false);
    $apiUrl = \App\Services\SettingsService::get('sms_api_url');
    $username = \App\Services\SettingsService::get('sms_username');
    $password = \App\Services\SettingsService::get('sms_password');
    $senderId = \App\Services\SettingsService::get('sms_sender_id');
    
    echo "Current Settings:\n";
    echo "  Enable SMS: " . ($enabled ? '✅ Yes' : '❌ No') . "\n";
    echo "  API URL: " . ($apiUrl ?: '❌ Not set') . "\n";
    echo "  Username: " . ($username ?: '❌ Not set') . "\n";
    echo "  Password: " . ($password ? '✅ Set' : '❌ Not set') . "\n";
    echo "  Sender ID: " . ($senderId ?: '❌ Not set') . "\n\n";
    
    if (!$enabled) {
        echo "❌ SMS notifications are disabled!\n";
        echo "Run: php QUICK_FIX_SMS_CONFIG.php to enable SMS\n";
        exit(1);
    }
    
    if (!$username || !$password) {
        echo "❌ SMS credentials are missing!\n";
        echo "Run: php QUICK_FIX_SMS_CONFIG.php to configure SMS\n";
        exit(1);
    }
    
    echo "✅ Configuration looks good!\n\n";
    
    // Get phone number from command line argument or use default
    $phoneNumber = $argv[1] ?? null;
    
    if (empty($phoneNumber)) {
        // Use default test number from your example
        $phoneNumber = '+255614863345';
        echo "No phone number provided. Using default test number: {$phoneNumber}\n";
        echo "(You can provide a phone number as argument: php test_send_sms.php +255XXXXXXXXX)\n\n";
    }
    
    // Normalize phone number (add + if missing)
    if (!str_starts_with($phoneNumber, '+') && !str_starts_with($phoneNumber, '255')) {
        $phoneNumber = '+255' . ltrim($phoneNumber, '0');
    } elseif (!str_starts_with($phoneNumber, '+') && str_starts_with($phoneNumber, '255')) {
        $phoneNumber = '+' . $phoneNumber;
    }
    
    echo "Phone number: {$phoneNumber}\n\n";
    
    // Test message
    $testMessage = "Test SMS from WauminiLink system. Configuration is working correctly! " . date('Y-m-d H:i:s');
    
    echo "Sending test SMS...\n";
    echo "Message: {$testMessage}\n\n";
    
    $smsService = app(\App\Services\SmsService::class);
    $result = $smsService->sendDebug($phoneNumber, $testMessage);
    
    echo "========================================\n";
    echo "SMS Send Result\n";
    echo "========================================\n\n";
    
    if ($result['ok'] ?? false) {
        echo "✅ SMS sent successfully!\n\n";
        echo "Details:\n";
        echo "  Status: " . ($result['status'] ?? 'N/A') . "\n";
        if (isset($result['body'])) {
            $body = $result['body'];
            // Try to decode JSON response
            $jsonBody = json_decode($body, true);
            if ($jsonBody) {
                echo "  Response (JSON):\n";
                echo "    " . json_encode($jsonBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
            } else {
                echo "  Response: " . $body . "\n";
            }
        }
    } else {
        echo "❌ SMS failed to send!\n\n";
        echo "Error Details:\n";
        echo "  Reason: " . ($result['reason'] ?? 'Unknown') . "\n";
        if (isset($result['error'])) {
            echo "  Error: " . $result['error'] . "\n";
        }
        if (isset($result['status'])) {
            echo "  HTTP Status: " . $result['status'] . "\n";
        }
        if (isset($result['body'])) {
            $body = $result['body'];
            $jsonBody = json_decode($body, true);
            if ($jsonBody) {
                echo "  Response (JSON):\n";
                echo "    " . json_encode($jsonBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
            } else {
                echo "  Response: " . $body . "\n";
            }
        }
    }
    
    echo "\n";
    
    // Show request details if available
    if (isset($result['request'])) {
        echo "Request Details:\n";
        if (isset($result['request']['url'])) {
            // Hide password in URL
            $safeUrl = preg_replace('/password=[^&]*/', 'password=***', $result['request']['url']);
            echo "  URL: " . $safeUrl . "\n";
        }
        if (isset($result['request']['method'])) {
            echo "  Method: " . $result['request']['method'] . "\n";
        }
    }
    
    echo "\n========================================\n";
    
    // Exit with appropriate code
    exit(($result['ok'] ?? false) ? 0 : 1);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
