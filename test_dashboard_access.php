<?php
/**
 * Quick test script to check dashboard access
 * Run: php test_dashboard_access.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Campus;

echo "=== Dashboard Access Test ===\n\n";

// Check if migrations have been run
try {
    $hasUsharikaAdmin = \Schema::hasColumn('users', 'is_usharika_admin');
    $hasCampusIdLeaders = \Schema::hasColumn('leaders', 'campus_id');
    
    echo "Database Check:\n";
    echo "- is_usharika_admin column: " . ($hasUsharikaAdmin ? "✓ EXISTS" : "✗ MISSING") . "\n";
    echo "- leaders.campus_id column: " . ($hasCampusIdLeaders ? "✓ EXISTS" : "✗ MISSING") . "\n\n";
    
    if (!$hasUsharikaAdmin || !$hasCampusIdLeaders) {
        echo "⚠️  WARNING: Migrations not run! Run: php artisan migrate\n\n";
    }
} catch (\Exception $e) {
    echo "Error checking database: " . $e->getMessage() . "\n\n";
}

// Check campuses
echo "Campuses:\n";
$campuses = Campus::all();
if ($campuses->count() == 0) {
    echo "⚠️  No campuses found! Create main campus first.\n\n";
} else {
    foreach ($campuses as $campus) {
        echo "- {$campus->name} ({$campus->code}) - " . ($campus->is_main_campus ? "MAIN" : "BRANCH") . "\n";
    }
    echo "\n";
}

// Check users
echo "Users:\n";
$users = User::with('campus')->get();
foreach ($users as $user) {
    $campusName = $user->campus ? $user->campus->name : "NO CAMPUS";
    $isUsharika = $user->is_usharika_admin ? "YES" : "NO";
    echo "- {$user->name} ({$user->role}) - Campus: {$campusName}, Usharika Admin: {$isUsharika}\n";
}
echo "\n";

// Check routes
echo "Routes Check:\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $branchRoute = $routes->getByName('branch.dashboard');
    $usharikaRoute = $routes->getByName('usharika.dashboard');
    
    echo "- branch.dashboard: " . ($branchRoute ? "✓ EXISTS" : "✗ MISSING") . "\n";
    echo "- usharika.dashboard: " . ($usharikaRoute ? "✓ EXISTS" : "✗ MISSING") . "\n\n";
} catch (\Exception $e) {
    echo "Error checking routes: " . $e->getMessage() . "\n\n";
}

echo "=== Test Complete ===\n";














