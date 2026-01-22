<?php
/**
 * Script to assign campuses to existing users
 * Run: php assign_campuses_to_users.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Campus;

echo "=== Assigning Campuses to Users ===\n\n";

// Get main campus
$mainCampus = Campus::where('is_main_campus', true)->first();

if (!$mainCampus) {
    echo "❌ ERROR: No main campus found! Please create main campus first.\n";
    exit(1);
}

echo "Main Campus: {$mainCampus->name} (ID: {$mainCampus->id})\n\n";

// Get all users without campus_id
$usersWithoutCampus = User::whereNull('campus_id')->get();

if ($usersWithoutCampus->count() == 0) {
    echo "✓ All users already have campus assigned.\n";
} else {
    echo "Found {$usersWithoutCampus->count()} users without campus:\n";
    
    foreach ($usersWithoutCampus as $user) {
        // Assign to main campus by default
        $user->campus_id = $mainCampus->id;
        
        // If admin, also set as Usharika admin
        if ($user->isAdmin()) {
            $user->is_usharika_admin = true;
            echo "- {$user->name} ({$user->role}) → Assigned to MAIN CAMPUS + Usharika Admin\n";
        } else {
            echo "- {$user->name} ({$user->role}) → Assigned to MAIN CAMPUS\n";
        }
        
        $user->save();
    }
    
    echo "\n✓ All users assigned to main campus!\n";
}

// Set existing admins as Usharika admins
$admins = User::where('role', 'admin')->where('is_usharika_admin', false)->get();
if ($admins->count() > 0) {
    echo "\nSetting admins as Usharika admins:\n";
    foreach ($admins as $admin) {
        $admin->is_usharika_admin = true;
        $admin->save();
        echo "- {$admin->name} → Usharika Admin\n";
    }
}

echo "\n=== Complete ===\n";
echo "\nNext steps:\n";
echo "1. Login as admin\n";
echo "2. You should see 'Usharika Dashboard' in sidebar\n";
echo "3. Create branches and assign users to them\n";














