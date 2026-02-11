<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offering;
use App\Models\CommunityOfferingItem;
use App\Models\Member;
use Carbon\Carbon;

$startDate = Carbon::now()->subDays(3)->toDateString();
$endDate = Carbon::now()->toDateString();
echo "Checking all contributions from $startDate to $endDate\n\n";

echo "--- COMMUNITY OFFERING ITEMS ---\n";
$items = CommunityOfferingItem::whereHas('offering', function($q) use ($startDate, $endDate) {
    $q->whereBetween('offering_date', [$startDate, $endDate]);
})->with(['member', 'offering'])->get();

foreach ($items as $item) {
    echo "ID: {$item->id}, Amt: {$item->amount}, Member: " . ($item->member ? $item->member->full_name : "UNKNOWN") . " (ID: {$item->member_id}), Status: " . ($item->offering ? $item->offering->status : "N/A") . "\n";
}

echo "\n--- GENERAL OFFERINGS ---\n";
$offerings = Offering::whereDate('offering_date', $today)->with('member')->get();
foreach ($offerings as $o) {
    echo "ID: {$o->id}, Amt: {$o->amount}, Type: {$o->offering_type}, Member: " . ($o->member ? $o->member->full_name : "UNKNOWN") . " (ID: {$o->member_id}), Status: {$o->approval_status}\n";
}
