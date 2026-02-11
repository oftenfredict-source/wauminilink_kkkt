<?php

use App\Models\Community;
use App\Models\CommunityOffering;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Comprehensive Visibility Debug ===\n\n";

// 1. Check User 46 (Often Fred)
$user = User::find(46);
echo "User 46: " . ($user ? $user->name : "NOT FOUND") . "\n";
if ($user) {
    $campus = $user->getCampus();
    echo "Campus: " . ($campus ? "{$campus->name} (ID: {$campus->id})" : "NULL") . "\n";
    
    if ($campus) {
        $communities = Community::where('campus_id', $campus->id)->get();
        echo "Communities in Campus {$campus->id}:\n";
        foreach ($communities as $c) {
            echo "- ID: {$c->id}, Name: {$c->name}\n";
            
            $offerings = CommunityOffering::withTrashed()->where('community_id', $c->id)->get();
            echo "  Offerings (withTrashed): " . $offerings->count() . "\n";
            foreach ($offerings as $o) {
                echo "    * ID: {$o->id}, Status: {$o->status}, Amount: {$o->amount}, Deleted: " . ($o->deleted_at ? "YES ({$o->deleted_at})" : "NO") . "\n";
            }
        }
    }
}

echo "\n=== Evangelism Leader Controller Logic Stats ===\n";
if ($user) {
    $campus = $user->getCampus();
    $communityIds = Community::where('campus_id', $campus->id)->pluck('id');
    $pendingOfferings = CommunityOffering::whereIn('community_id', $communityIds)
            ->where('status', 'pending_evangelism')
            ->count();
    $pendingOfferingsAmount = CommunityOffering::whereIn('community_id', $communityIds)
            ->where('status', 'pending_evangelism')
            ->sum('amount');
    echo "Pending Count (from Controller Logic): {$pendingOfferings}\n";
    echo "Pending Amount (from Controller Logic): {$pendingOfferingsAmount}\n";
}
