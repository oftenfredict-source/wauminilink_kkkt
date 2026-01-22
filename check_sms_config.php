<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SMS Configuration Diagnostic ===\n\n";

// Check SMS settings
$enabled = \App\Services\SettingsService::get('enable_sms_notifications', false);
$apiUrl = \App\Services\SettingsService::get('sms_api_url');
$apiKey = \App\Services\SettingsService::get('sms_api_key');
$username = \App\Services\SettingsService::get('sms_username');
$password = \App\Services\SettingsService::get('sms_password');
$senderId = \App\Services\SettingsService::get('sms_sender_id', 'KKKT Ushirika wa Longuo');

echo "SMS Notifications Enabled: " . ($enabled ? "YES ✓" : "NO ✗") . "\n";
echo "API URL: " . ($apiUrl ?: "NOT SET ✗") . "\n";
echo "API Key: " . ($apiKey ? "SET ✓" : "NOT SET ✗") . "\n";
echo "Username: " . ($username ?: "NOT SET ✗") . "\n";
echo "Password: " . ($password ? "SET ✓" : "NOT SET ✗") . "\n";
echo "Sender ID: " . $senderId . "\n\n";

// Determine authentication method
if (!empty($username) && !empty($password)) {
    echo "Authentication Method: Username/Password ✓\n";
    if (empty($apiUrl)) {
        echo "Note: API URL will use default: https://messaging-service.co.tz/link/sms/v1/text/single\n";
    }
} elseif (!empty($apiUrl) && !empty($apiKey)) {
    echo "Authentication Method: API Key (Bearer Token) ✓\n";
} else {
    echo "Authentication Method: NOT CONFIGURED ✗\n";
    echo "ERROR: Need either (username and password) or (apiUrl and apiKey)\n";
}

echo "\n=== Configuration Status ===\n";
if ($enabled && ((!empty($username) && !empty($password)) || (!empty($apiUrl) && !empty($apiKey)))) {
    echo "✓ SMS is properly configured and enabled\n";
} else {
    echo "✗ SMS is NOT properly configured\n";
    if (!$enabled) {
        echo "  - SMS notifications are disabled\n";
    }
    if (empty($username) || empty($password)) {
        if (empty($apiUrl) || empty($apiKey)) {
            echo "  - No authentication credentials configured\n";
        }
    }
}

echo "\n=== Test SMS Sending ===\n";
if ($enabled && ((!empty($username) && !empty($password)) || (!empty($apiUrl) && !empty($apiKey)))) {
    echo "Testing SMS to +255743001243...\n";
    $smsService = app(\App\Services\SmsService::class);
    $result = $smsService->sendDebug('+255743001243', 'Test SMS from WauminiLink diagnostic script');
    
    echo "\nResult:\n";
    echo "  Success: " . ($result['ok'] ? "YES ✓" : "NO ✗") . "\n";
    if (isset($result['status'])) {
        echo "  HTTP Status: " . $result['status'] . "\n";
    }
    if (isset($result['reason'])) {
        echo "  Reason: " . $result['reason'] . "\n";
        if (stripos($result['reason'], 'REJECTED') !== false || stripos($result['reason'], 'SENDER') !== false) {
            echo "\n  ⚠️  WARNING: Sender ID may not be registered with SMS provider!\n";
            echo "     Current Sender ID: " . $senderId . "\n";
            echo "     Action: Contact SMS provider to register this Sender ID\n";
        }
    }
    if (isset($result['error'])) {
        echo "  Error: " . $result['error'] . "\n";
    }
    if (isset($result['body'])) {
        $body = $result['body'];
        echo "  Response Body: " . substr($body, 0, 300) . "\n";
        
        // Check for rejection in response
        $responseData = json_decode($body, true);
        if (isset($responseData['messages']) && is_array($responseData['messages'])) {
            foreach ($responseData['messages'] as $msg) {
                if (isset($msg['status'])) {
                    $status = $msg['status'];
                    if (isset($status['groupName']) && stripos($status['groupName'], 'REJECTED') !== false) {
                        echo "\n  ⚠️  CRITICAL: SMS was REJECTED by provider!\n";
                        echo "     Status: " . ($status['groupName'] ?? 'Unknown') . "\n";
                        echo "     Description: " . ($status['description'] ?? 'Unknown') . "\n";
                        echo "     Action Required: Register Sender ID '" . $senderId . "' with SMS provider\n";
                    }
                }
            }
        }
    }
} else {
    echo "Skipping test - SMS not properly configured\n";
}

echo "\n=== Recent SMS Logs ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $smsLines = array_filter($lines, function($line) {
        return stripos($line, 'sms') !== false;
    });
    $recentSmsLines = array_slice($smsLines, -10);
    foreach ($recentSmsLines as $line) {
        echo trim($line) . "\n";
    }
} else {
    echo "Log file not found\n";
}

echo "\n=== Recommendations ===\n";
if (!$enabled) {
    echo "1. Enable SMS notifications by running: php artisan sms:enable\n";
    echo "   Or visit: /setup-sms route\n";
}
if (empty($username) && empty($password) && (empty($apiUrl) || empty($apiKey))) {
    echo "2. Configure SMS credentials:\n";
    echo "   - Set username and password, OR\n";
    echo "   - Set API URL and API Key\n";
    echo "   Run: php artisan sms:enable --username=your_username --password=your_password\n";
}

