<?php
/**
 * Quick password reset script
 * Usage: php reset_user_password.php <email> [new_password]
 * Example: php reset_user_password.php admin@waumini.com password123
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

if ($argc < 2) {
    echo "Usage: php reset_user_password.php <email> [new_password]\n";
    echo "Example: php reset_user_password.php admin@waumini.com password123\n\n";
    echo "Available users:\n";
    $users = User::select('id', 'name', 'email', 'role')->get();
    foreach ($users as $user) {
        echo "  - {$user->email} ({$user->name}, {$user->role})\n";
    }
    exit(1);
}

$email = $argv[1];
$newPassword = $argv[2] ?? 'password123';

$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found: {$email}\n";
    echo "\nAvailable users:\n";
    $users = User::select('id', 'name', 'email', 'role')->get();
    foreach ($users as $user) {
        echo "  - {$user->email} ({$user->name})\n";
    }
    exit(1);
}

$user->password = Hash::make($newPassword);
$user->save();

echo "✅ Password reset successfully!\n";
echo "  User: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  New Password: {$newPassword}\n";
echo "\nYou can now login with:\n";
echo "  Email: {$email}\n";
echo "  Password: {$newPassword}\n";














