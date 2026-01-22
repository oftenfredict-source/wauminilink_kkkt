<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\SettingsService;
use App\Services\SmsService;

echo "=== SMS Test ===\n\n";

// Check if SMS is enabled
$enabled = SettingsService::get('enable_sms_notifications', false);
echo "SMS Enabled: " . ($enabled ? "YES" : "NO") . "\n";

if (!$enabled) {
    echo "\n⚠️  SMS notifications are DISABLED!\n";
    echo "To enable, run: php enable_sms.php\n";
    echo "Or set in System Settings: enable_sms_notifications = true\n";
    exit(1);
}

// Check configuration
$username = SettingsService::get('sms_username', '');
$password = SettingsService::get('sms_password', '');
$apiUrl = SettingsService::get('sms_api_url', '');
$apiKey = SettingsService::get('sms_api_key', '');

echo "Username: " . ($username ?: "NOT SET") . "\n";
echo "Password: " . ($password ? "SET" : "NOT SET") . "\n";
echo "API URL: " . ($apiUrl ?: "NOT SET (will use default)") . "\n";
echo "API Key: " . ($apiKey ? "SET" : "NOT SET") . "\n\n";

if (empty($username) && empty($password) && empty($apiKey)) {
    echo "⚠️  SMS credentials NOT configured!\n";
    echo "Run: php enable_sms.php\n";
    exit(1);
}

// Test SMS
echo "Testing SMS to +255743001243...\n";
$smsService = app(SmsService::class);
$result = $smsService->sendDebug('+255743001243', 'Test SMS from Waumini Link system');

echo "\nResult:\n";
print_r($result);

if ($result['ok'] ?? false) {
    echo "\n✅ SMS sent successfully!\n";
} else {
    echo "\n❌ SMS failed!\n";
    echo "Reason: " . ($result['reason'] ?? $result['error'] ?? 'Unknown') . "\n";
}

















