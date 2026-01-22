<?php
/**
 * Check SMS Configuration and Status
 * Run: php check_sms_status.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\SettingsService;

echo "=== SMS Configuration Status ===\n\n";

// Check SMS settings
$enabled = SettingsService::get('enable_sms_notifications', false);
$apiUrl = SettingsService::get('sms_api_url', '');
$apiKey = SettingsService::get('sms_api_key', '');
$username = SettingsService::get('sms_username', '');
$password = SettingsService::get('sms_password', '');
$senderId = SettingsService::get('sms_sender_id', 'WAUMINI');

echo "SMS Notifications Enabled: " . ($enabled ? "YES ✓" : "NO ✗") . "\n";
echo "API URL: " . ($apiUrl ?: "NOT SET ✗") . "\n";
echo "API Key: " . ($apiKey ? "SET ✓" : "NOT SET ✗") . "\n";
echo "Username: " . ($username ?: "NOT SET ✗") . "\n";
echo "Password: " . ($password ? "SET ✓" : "NOT SET ✗") . "\n";
echo "Sender ID: " . $senderId . "\n\n";

// Determine authentication method
$hasAuth = false;
if (!empty($username) && !empty($password)) {
    echo "Authentication Method: Username/Password ✓\n";
    $hasAuth = true;
    if (empty($apiUrl)) {
        echo "Note: Will use default URL: https://messaging-service.co.tz/link/sms/v1/text/single\n";
    }
} elseif (!empty($apiUrl) && !empty($apiKey)) {
    echo "Authentication Method: API Key (Bearer Token) ✓\n";
    $hasAuth = true;
} else {
    echo "Authentication Method: NOT CONFIGURED ✗\n";
    echo "ERROR: Need either (username and password) OR (apiUrl and apiKey)\n";
}

echo "\n=== Overall Status ===\n";
if ($enabled && $hasAuth) {
    echo "✓ SMS is properly configured and enabled\n";
    echo "\nTo test SMS, run: php artisan sms:test\n";
} else {
    echo "✗ SMS is NOT properly configured\n";
    echo "\n=== How to Fix ===\n";
    if (!$enabled) {
        echo "1. Enable SMS notifications:\n";
        echo "   php artisan sms:enable\n";
        echo "   OR visit: /setup-sms route\n";
    }
    if (!$hasAuth) {
        echo "2. Configure SMS credentials:\n";
        echo "   php artisan sms:enable --username=your_username --password=your_password\n";
        echo "   OR set in System Settings in the admin panel\n";
    }
}

echo "\n=== Recent SMS Logs ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $smsLogs = [];
    $lines = explode("\n", $logs);
    $recentLines = array_slice($lines, -50); // Last 50 lines
    
    foreach ($recentLines as $line) {
        if (stripos($line, 'SMS') !== false || stripos($line, 'OTP') !== false) {
            $smsLogs[] = $line;
        }
    }
    
    if (!empty($smsLogs)) {
        echo "Found " . count($smsLogs) . " recent SMS-related log entries:\n";
        foreach (array_slice($smsLogs, -10) as $log) {
            echo "  " . substr($log, 0, 150) . "...\n";
        }
    } else {
        echo "No recent SMS logs found.\n";
    }
} else {
    echo "Log file not found.\n";
}

echo "\n";

















