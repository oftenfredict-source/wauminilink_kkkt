<?php

/**
 * Quick Fix: Configure SMS Settings
 * Run this file to quickly set up SMS configuration
 * Usage: php QUICK_FIX_SMS_CONFIG.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "========================================\n";
    echo "SMS Configuration Quick Fix\n";
    echo "========================================\n\n";
    
    // Enable SMS notifications
    \App\Services\SettingsService::set('enable_sms_notifications', true, 'boolean');
    echo "✅ SMS notifications enabled\n";
    
    // Set SMS API URL
    $apiUrl = 'https://messaging-service.co.tz/link/sms/v1/text/single';
    \App\Services\SettingsService::set('sms_api_url', $apiUrl, 'string');
    echo "✅ SMS API URL set: {$apiUrl}\n";
    
    // Set SMS Username
    $username = 'emcatechn';
    \App\Services\SettingsService::set('sms_username', $username, 'string');
    echo "✅ SMS Username set: {$username}\n";
    
    // Set SMS Password
    $password = 'Emca@#12';
    \App\Services\SettingsService::set('sms_password', $password, 'string');
    echo "✅ SMS Password set: ***\n";
    
    // Set SMS Sender ID
    $senderId = 'WauminiLnk';
    \App\Services\SettingsService::set('sms_sender_id', $senderId, 'string');
    echo "✅ SMS Sender ID set: {$senderId}\n";
    
    echo "\n========================================\n";
    echo "Configuration Complete!\n";
    echo "========================================\n\n";
    
    // Verify configuration
    echo "Verifying configuration...\n\n";
    
    $enabled = \App\Services\SettingsService::get('enable_sms_notifications', false);
    $apiUrlCheck = \App\Services\SettingsService::get('sms_api_url');
    $usernameCheck = \App\Services\SettingsService::get('sms_username');
    $passwordCheck = \App\Services\SettingsService::get('sms_password');
    $senderIdCheck = \App\Services\SettingsService::get('sms_sender_id');
    
    echo "Current Settings:\n";
    echo "  Enable SMS: " . ($enabled ? 'Yes' : 'No') . "\n";
    echo "  API URL: " . ($apiUrlCheck ?: 'Not set') . "\n";
    echo "  Username: " . ($usernameCheck ?: 'Not set') . "\n";
    echo "  Password: " . ($passwordCheck ? 'Set' : 'Not set') . "\n";
    echo "  Sender ID: " . ($senderIdCheck ?: 'Not set') . "\n\n";
    
    if ($enabled && $usernameCheck && $passwordCheck) {
        echo "✅ SMS configuration is complete!\n";
        echo "You can now assign leadership positions and SMS will be sent.\n\n";
        
        // Test SMS (optional)
        echo "Would you like to test SMS? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $test = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($test) === 'y') {
            echo "\nEnter test phone number (e.g., +255614863345): ";
            $handle = fopen("php://stdin", "r");
            $testPhone = trim(fgets($handle));
            fclose($handle);
            
            if ($testPhone) {
                echo "\nSending test SMS...\n";
                $smsService = app(\App\Services\SmsService::class);
                $result = $smsService->sendDebug($testPhone, 'Test SMS from WauminiLink system. Configuration successful!');
                
                if ($result['ok'] ?? false) {
                    echo "✅ Test SMS sent successfully!\n";
                } else {
                    echo "❌ Test SMS failed: " . ($result['reason'] ?? $result['error'] ?? 'Unknown error') . "\n";
                    if (isset($result['body'])) {
                        echo "Response: " . $result['body'] . "\n";
                    }
                }
            }
        }
    } else {
        echo "❌ Configuration incomplete. Please check the settings above.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}






