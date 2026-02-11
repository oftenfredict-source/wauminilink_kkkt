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

echo "MEMBER: " . ($member ? $member->full_name : "NOT FOUND") . " (ID: $memberId)\n";

echo "\n--- OFFERINGS TABLE ---\n";
$offerings = Offering::where('member_id', $memberId)->get();
foreach ($offerings as $o) {
    printf("ID: %d | Date: %s | Type: %s | Amt: %d | Status: %s\n", 
        $o->id, 
        $o->offering_date->format('Y-m-d'), 
        $o->offering_type, 
        $o->amount, 
        $o->approval_status
    );
}

echo "\n--- COMMUNITY ITEMS TABLE ---\n";
$community = CommunityOfferingItem::where('member_id', $memberId)->get();
foreach ($community as $item) {
    $o = $item->offering;
    printf("Item ID: %d | Date: %s | Type: %s | Amt: %d | Status: %s\n", 
        $item->id, 
        ($o ? $o->offering_date->format('Y-m-d') : 'N/A'), 
        ($o ? $o->offering_type : 'N/A'), 
        $item->amount, 
        ($o ? $o->status : 'N/A')
    );
}
echo "\nDONE\n";
