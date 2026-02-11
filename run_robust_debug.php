<?php

use App\Models\User;
use App\Models\Community;
use App\Models\CommunityOffering;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$output = "=== Robust Debug Output ===\n\n";

// 1. Current Context (User 46 - Often Fred)
$user = User::find(46);
$output .= "User ID: 46 | Name: " . ($user ? $user->name : "NOT FOUND") . "\n";
if ($user) {
    $campus = $user->getCampus();
    $output .= "User Campus: " . ($campus ? "{$campus->name} (ID: {$campus->id})" : "NONE") . "\n";
    $output .= "User Role: " . ($user->role ? $user->role->name : "NONE") . "\n";
}

// 2. Ebeneza Communities
$communities = Community::where('name', 'LIKE', '%Ebeneza%')->withTrashed()->get();
$output .= "\nEbeneza Communities:\n";
foreach ($communities as $c) {
    $output .= "- ID: {$c->id} | Name: {$c->name} | Campus: {$c->campus_id} | Deleted: " . ($c->deleted_at ?? 'NO') . "\n";
}

// 3. All Pending/Completed Ebeneza Offerings
$output .= "\nEbeneza Offerings:\n";
foreach ($communities as $c) {
    $offerings = CommunityOffering::withTrashed()->where('community_id', $c->id)->get();
    foreach ($offerings as $o) {
        $output .= "- ID: {$o->id} | CommID: {$o->community_id} | Amount: {$o->amount} | Status: {$o->status} | Deleted: " . ($o->deleted_at ?? 'NO') . "\n";
    }
}

file_put_contents('robust_debug.txt', $output);
echo "Robust debug written to robust_debug.txt\n";
