<?php

use App\Models\Community;
use App\Models\CommunityOffering;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Final Verification of Community Offering Visibility ===\n\n";

// Emulate Evangelism Leader (Often Fred, ID 46) logic
echo "Checking logic for Evangelism Leader Often Fred (ID 46)...\n";
$user = User::find(46);
if (!$user) {
    echo "User 46 not found.\n";
} else {
    $campus = $user->getCampus();
    $communityIds = Community::where('campus_id', $campus->id)->pluck('id')->toArray();
    
    echo "Campus: {$campus->name} (ID: {$campus->id})\n";
    echo "Communities in Campus: " . implode(', ', $communityIds) . "\n";
    
    $pendingForLeader = CommunityOffering::whereIn('community_id', $communityIds)
        ->whereIn('status', ['pending_evangelism', 'pending_secretary'])
        ->count();
    
    echo "Offerings visible in Index: {$pendingForLeader}\n";
    
    $dashboardPending = CommunityOffering::whereIn('community_id', $communityIds)
        ->where('status', 'pending_evangelism')
        ->count();
    
    echo "Offerings counted in Dashboard 'Pending Offerings': {$dashboardPending}\n";
}

echo "\nChecking logic for Admin/Secretary...\n";
// Admin should see both pending_evangelism and pending_secretary
$totalPending = CommunityOffering::whereIn('status', ['pending_evangelism', 'pending_secretary'])->count();
echo "Total offerings visible to Admin: {$totalPending}\n";

$ebeneza = Community::where('name', 'LIKE', '%Ebeneza%')->first();
if ($ebeneza) {
    $ebenezaPending = CommunityOffering::where('community_id', $ebeneza->id)
        ->whereIn('status', ['pending_evangelism', 'pending_secretary'])
        ->count();
    echo "Ebeneza pending offerings visible: {$ebenezaPending}\n";
}
