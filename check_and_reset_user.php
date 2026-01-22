<?php
/**
 * Script to check users and reset password if needed
 * Run: php check_and_reset_user.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== User Check & Password Reset Tool ===\n\n";

// List all users
echo "All Users in System:\n";
echo str_repeat("-", 80) . "\n";
$users = User::select('id', 'name', 'email', 'role', 'campus_id', 'member_id')->get();

if ($users->count() == 0) {
    echo "❌ No users found in the system!\n";
    exit(1);
}

foreach ($users as $user) {
    $campus = $user->campus ? $user->campus->name : 'NO CAMPUS';
    echo sprintf(
        "ID: %d | Name: %s | Email: %s | Role: %s | Campus: %s\n",
        $user->id,
        $user->name,
        $user->email ?? 'NO EMAIL',
        $user->role,
        $campus
    );
}

echo "\n" . str_repeat("-", 80) . "\n\n";

// Check specific user
echo "Enter email or user ID to check/reset password (or 'all' to reset all passwords): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if ($input === 'all') {
    echo "\n⚠️  Resetting ALL user passwords to 'password123'\n";
    echo "Press Enter to continue or Ctrl+C to cancel...";
    $handle = fopen("php://stdin", "r");
    fgets($handle);
    fclose($handle);
    
    foreach ($users as $user) {
        $user->password = Hash::make('password123');
        $user->save();
        echo "✓ Reset password for: {$user->name} ({$user->email})\n";
        echo "  New password: password123\n";
    }
    echo "\n✅ All passwords reset!\n";
} else {
    // Find user
    $user = null;
    if (is_numeric($input)) {
        $user = User::find($input);
    } else {
        $user = User::where('email', $input)->first();
    }
    
    if (!$user) {
        echo "❌ User not found!\n";
        exit(1);
    }
    
    echo "\nUser Found:\n";
    echo "  ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
    echo "  Campus: " . ($user->campus ? $user->campus->name : 'NO CAMPUS') . "\n";
    echo "  Has Password: " . ($user->password ? 'YES' : 'NO') . "\n";
    
    echo "\nReset password? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $confirm = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirm) === 'y') {
        echo "Enter new password (or press Enter for 'password123'): ";
        $handle = fopen("php://stdin", "r");
        $newPassword = trim(fgets($handle));
        fclose($handle);
        
        if (empty($newPassword)) {
            $newPassword = 'password123';
        }
        
        $user->password = Hash::make($newPassword);
        $user->save();
        
        echo "\n✅ Password reset successfully!\n";
        echo "  Email: {$user->email}\n";
        echo "  New Password: {$newPassword}\n";
    } else {
        echo "\nPassword reset cancelled.\n";
    }
}

echo "\n=== Done ===\n";














