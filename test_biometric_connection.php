<?php

/**
 * Quick Biometric Device Connection Test Script
 * 
 * Usage: php test_biometric_connection.php [IP] [PORT] [PASSWORD]
 * Example: php test_biometric_connection.php 192.168.100.108 4370 0
 */

require __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ZKTecoService;

// Get parameters from command line or use defaults
$ip = $argv[1] ?? config('zkteco.ip', '192.168.100.108');
$port = isset($argv[2]) ? (int)$argv[2] : config('zkteco.port', 4370);
$password = isset($argv[3]) ? (int)$argv[3] : config('zkteco.password', 0);

echo "\n";
echo "========================================\n";
echo "  Biometric Device Connection Test\n";
echo "========================================\n";
echo "Device IP: {$ip}\n";
echo "Port: {$port}\n";
echo "Comm Key: {$password}\n";
echo "========================================\n\n";

try {
    echo "Step 1: Initializing ZKTecoService...\n";
    $zkteco = new ZKTecoService($ip, $port, $password);
    echo "✓ Service initialized\n\n";

    echo "Step 2: Attempting to connect to device...\n";
    $startTime = microtime(true);
    
    if ($zkteco->connect()) {
        $connectionTime = round(microtime(true) - $startTime, 2);
        echo "✓ Connected successfully! (took {$connectionTime} seconds)\n\n";

        echo "Step 3: Getting device information...\n";
        try {
            $deviceInfo = $zkteco->getDeviceInfo();
            if ($deviceInfo) {
                echo "✓ Device Information:\n";
                echo "  - Device Name: " . ($deviceInfo['device_name'] ?? 'N/A') . "\n";
                echo "  - Serial Number: " . ($deviceInfo['serial_number'] ?? 'N/A') . "\n";
                echo "  - Firmware Version: " . ($deviceInfo['firmware_version'] ?? 'N/A') . "\n";
                echo "  - Device Time: " . ($zkteco->getTime() ?? 'N/A') . "\n";
            }
        } catch (\Exception $e) {
            echo "⚠ Could not get device info: " . $e->getMessage() . "\n";
        }
        echo "\n";

        echo "Step 4: Getting users from device...\n";
        try {
            $users = $zkteco->getUsers();
            echo "✓ Found " . count($users) . " user(s) on device\n";
            if (count($users) > 0) {
                echo "  Sample users (first 3):\n";
                $count = 0;
                foreach ($users as $userId => $user) {
                    if ($count++ >= 3) break;
                    $name = $user['name'] ?? 'N/A';
                    $uid = $user['uid'] ?? $userId;
                    echo "    - User ID: {$uid}, Name: {$name}\n";
                }
            }
        } catch (\Exception $e) {
            echo "⚠ Could not get users: " . $e->getMessage() . "\n";
        }
        echo "\n";

        echo "Step 5: Getting attendance records...\n";
        try {
            $attendance = $zkteco->getAttendances();
            echo "✓ Found " . count($attendance) . " attendance record(s)\n";
            if (count($attendance) > 0) {
                echo "  Sample records (first 3):\n";
                $count = 0;
                foreach ($attendance as $record) {
                    if ($count++ >= 3) break;
                    $uid = $record['uid'] ?? 'N/A';
                    $timestamp = $record['timestamp'] ?? 'N/A';
                    $status = $record['status'] ?? 'N/A';
                    echo "    - User ID: {$uid}, Time: {$timestamp}, Status: {$status}\n";
                }
            }
        } catch (\Exception $e) {
            echo "⚠ Could not get attendance: " . $e->getMessage() . "\n";
        }
        echo "\n";

        echo "Step 6: Disconnecting...\n";
        $zkteco->disconnect();
        echo "✓ Disconnected\n\n";

        echo "========================================\n";
        echo "  ✓ ALL TESTS PASSED!\n";
        echo "========================================\n";
        echo "\nYour biometric device is working correctly!\n";
        echo "You can now use the web interface at: /biometric/test\n\n";

    } else {
        echo "✗ Connection failed!\n\n";
        echo "Troubleshooting:\n";
        echo "1. Check if device is powered on\n";
        echo "2. Verify IP address: {$ip}\n";
        echo "3. Check network connectivity (try: ping {$ip})\n";
        echo "4. Verify port: {$port}\n";
        echo "5. Check Comm Key (password) on device\n";
        echo "6. Ensure firewall allows port {$port}\n\n";
        exit(1);
    }

} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
    echo "Error Details:\n";
    echo "- " . get_class($e) . "\n";
    echo "- File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    
    if (strpos($e->getMessage(), 'timeout') !== false) {
        echo "Timeout Error - Possible causes:\n";
        echo "1. Device is not powered on\n";
        echo "2. Wrong IP address\n";
        echo "3. Network connectivity issues\n";
        echo "4. Firewall blocking connection\n\n";
    } elseif (strpos($e->getMessage(), '2005') !== false || strpos($e->getMessage(), 'UNAUTH') !== false) {
        echo "Authentication Error - Possible causes:\n";
        echo "1. Wrong Comm Key (password)\n";
        echo "2. Device requires authentication\n";
        echo "3. Check device settings: System → Communication → Comm Key\n\n";
    }
    
    exit(1);
}












