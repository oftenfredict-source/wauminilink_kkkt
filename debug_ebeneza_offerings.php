<?php

use App\Models\Community;
use App\Models\CommunityOffering;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Debugging Ebeneza Community Offerings ===\n\n";

// Find Ebeneza community
$ebeneza = Community::where('name', 'LIKE', '%Ebeneza%')->get();
if ($ebeneza->isEmpty()) {
    echo "No community found matching 'Ebeneza'.\n";
    echo "All communities:\n";
    foreach (Community::all() as $c) {
        echo "- ID: {$c->id}, Name: {$c->name}, Campus ID: {$c->campus_id}\n";
    }
} else {
    foreach ($ebeneza as $c) {
        echo "Found Community: ID: {$c->id}, Name: {$c->name}, Campus ID: {$c->campus_id}\n";
        
        // Find offerings for this community
        $offerings = CommunityOffering::where('community_id', $c->id)->get();
        echo "  Offerings count: " . $offerings->count() . "\n";
        foreach ($offerings as $o) {
            echo "  - ID: {$o->id}, Status: {$o->status}, Amount: {$o->amount}, Date: {$o->offering_date}, Created At: {$o->created_at}\n";
            echo "    Recorded by (Elder ID): {$o->church_elder_id}\n";
            echo "    Leader ID: " . ($o->evangelism_leader_id ?? 'N/A') . "\n";
            echo "    Secretary ID: " . ($o->secretary_id ?? 'N/A') . "\n";
        }
    }
}

echo "\n=== Current User Info ===\n";
$user = auth()->user();
if ($user) {
    echo "User ID: {$user->id}\n";
    echo "Roles: " . ($user->role ? $user->role->name : 'No role') . "\n";
    echo "Campus ID: " . ($user->campus_id ?? 'N/A') . "\n";
    echo "Is Elder: " . ($user->isChurchElder() ? 'Yes' : 'No') . "\n";
    echo "Is Leader: " . ($user->isEvangelismLeader() ? 'Yes' : 'No') . "\n";
    echo "Is Secretary: " . ($user->isSecretary() ? 'Yes' : 'No') . "\n";
} else {
    echo "No user logged in (running from CLI).\n";
}
