<?php
// Script to list church elders or create one if none exist
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Leader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Helper to check role
// Based on previous checks, roles might be stored in 'role' column or related via Leader model
// Let's check 'role' column first
$elders = User::where('role', 'church_elder')->get();

echo "Found " . $elders->count() . " users with role 'church_elder'.\n";

if ($elders->count() > 0) {
    echo "Here are some credentials you can use:\n";
    foreach ($elders->take(3) as $elder) {
        echo "Email: " . $elder->email . "\n";
        // Reset password to 'password' for easy testing if needed?
        // Better to just show email, user might know password or we can reset one specific
    }
} else {
    echo "No Church Elders found. Searching via Leader model...\n";

    // Maybe they are defined in 'leaders' table and linked to user?
    // Let's look for a user who is linked to a Leader record with position 'Elder' or similar
    // For now, let's just create a test elder user

    echo "Creating a test Church Elder user...\n";

    try {
        $user = User::create([
            'name' => 'Test Elder',
            'email' => 'elder@test.com',
            'password' => Hash::make('password'),
            'role' => 'church_elder',
            'campus_id' => 1, // Assuming campus 1 exists
            'is_active' => true
        ]);

        echo "Created Test User:\n";
        echo "Email: elder@test.com\n";
        echo "Password: password\n";

    } catch (\Exception $e) {
        echo "Error creating user: " . $e->getMessage() . "\n";
    }
}

// Also check for Treasurer
$treasurers = User::where('role', 'treasurer')->get();
echo "\nFound " . $treasurers->count() . " users with role 'treasurer'.\n";
if ($treasurers->count() > 0) {
    foreach ($treasurers->take(1) as $t) {
        echo "Treasurer Email: " . $t->email . "\n";
    }
} else {
    // Create test treasurer
    echo "Creating test Treasurer...\n";
    try {
        User::create([
            'name' => 'Test Treasurer',
            'email' => 'treasurer@test.com',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'campus_id' => 1
        ]);
        echo "Created Treasurer: treasurer@test.com / password\n";
    } catch (\Exception $e) {
        echo "Error creating treasurer: " . $e->getMessage() . "\n";
    }
}
