<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offering;
use App\Models\CommunityOfferingItem;
use App\Models\Member;

$memberId = 52;
$member = Member::find($memberId);

echo "Checking contributions for member: " . ($member ? $member->full_name : "NOT FOUND") . " (ID: $memberId)\n\n";

echo "--- GENERAL OFFERINGS (Offering Model) ---\n";
$general = Offering::where('member_id', $memberId)->get();
foreach ($general as $o) {
    echo "ID: {$o->id}, Date: {$o->offering_date->format('Y-m-d')}, Type: '{$o->offering_type}', Amount: {$o->amount}, Status: '{$o->approval_status}'\n";
}
if ($general->isEmpty()) echo "No general offerings found.\n";

echo "\n--- COMMUNITY OFFERING ITEMS (CommunityOfferingItem Model) ---\n";
$community = CommunityOfferingItem::where('member_id', $memberId)
    ->with('offering')
    ->get();
foreach ($community as $item) {
    $o = $item->offering;
    echo "Item ID: {$item->id}, Date: " . ($o ? $o->offering_date->format('Y-m-d') : 'N/A') . ", Type: '" . ($o ? $o->offering_type : 'N/A') . "', Amount: {$item->amount}, Status: '" . ($o ? $o->status : 'N/A') . "'\n";
}
if ($community->isEmpty()) echo "No community offerings found.\n";
