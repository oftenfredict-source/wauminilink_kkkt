<?php

use App\Models\Community;
use App\Models\CommunityOffering;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Refined Debugging for Ebeneza ===\n\n";

// Find Ebeneza community
$ebeneza = Community::where('name', 'LIKE', '%Ebeneza%')->first();
if (!$ebeneza) {
    echo "No community found matching 'Ebeneza'.\n";
} else {
    echo "Community: ID: {$ebeneza->id}, Name: {$ebeneza->name}, Campus ID: {$ebeneza->campus_id}\n";
    echo "Managed by (Elder ID): " . ($ebeneza->church_elder_id ?? 'NONE') . "\n";
    if ($ebeneza->church_elder_id) {
        $elder = User::find($ebeneza->church_elder_id);
        echo "  Elder Name: " . ($elder ? $elder->name : 'Unknown User') . "\n";
    }
    
    echo "\nOfferings for this community:\n";
    $offerings = CommunityOffering::where('community_id', $ebeneza->id)->get();
    foreach ($offerings as $o) {
        echo "- ID: {$o->id}, Amount: {$o->amount}, Status: {$o->status}\n";
        echo "  Recorded by Elder ID: {$o->church_elder_id}\n";
        $recorder = User::find($o->church_elder_id);
        echo "  Recorder Name: " . ($recorder ? $recorder->name : 'Unknown User') . "\n";
    }
}

echo "\n=== Evangelism Leaders for Campus 2 ===\n";
$campus2Leaders = User::whereHas('member.activeLeadershipPositions', function($q) {
    $q->where('position', 'evangelism_leader');
})->get();

foreach ($campus2Leaders as $leader) {
    echo "- ID: {$leader->id}, Name: {$leader->name}\n";
}
