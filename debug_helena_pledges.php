<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pledge;
use App\Models\Member;

$memberId = 52;
$member = Member::find($memberId);

if (!$member) {
    echo "Member 52 not found!\n";
    // Try to find by name
    $member = Member::where('full_name', 'LIKE', '%Helena%')->first();
    if ($member) {
        echo "Found member by name: " . $member->full_name . " (ID: " . $member->id . ")\n";
        $memberId = $member->id;
    } else {
        exit;
    }
} else {
    echo "Checking pledges for: " . $member->full_name . " (ID: " . $member->id . ")\n";
}

$pledges = Pledge::where('member_id', $memberId)->get();

echo "Total Pledges Found: " . $pledges->count() . "\n";

foreach ($pledges as $pledge) {
    echo "--------------------------------------------------\n";
    echo "ID: " . $pledge->id . "\n";
    echo "Date: " . $pledge->pledge_date . "\n";
    echo "Amount: " . $pledge->pledge_amount . "\n";
    echo "Paid: " . $pledge->amount_paid . "\n";
    echo "Type: " . $pledge->pledge_type . "\n";
    echo "Status: " . $pledge->status . "\n";
    echo "Item: " . ($pledge->item_name ?? 'N/A') . "\n"; // Check if there's an item column
    echo "Description: " . ($pledge->description ?? 'N/A') . "\n";
}
