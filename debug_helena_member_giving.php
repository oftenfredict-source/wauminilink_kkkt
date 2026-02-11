<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offering;
use App\Models\CommunityOfferingItem;
use App\Models\Member;
use Carbon\Carbon;

$memberId = 52; // Helena Shija
$startDate = Carbon::now()->startOfYear();
$endDate = Carbon::now()->endOfYear();

echo "Member Giving Debug for Helena Shija (ID: $memberId)\n";
echo "Date Range: {$startDate->toDateString()} to {$endDate->toDateString()}\n\n";

$offerings = Offering::where('member_id', $memberId)
    ->where('approval_status', 'approved')
    ->whereBetween('offering_date', [$startDate, $endDate])
    ->get();

echo "General Offerings: " . $offerings->count() . " (Sum: " . $offerings->sum('amount') . ")\n";

$communityOfferings = CommunityOfferingItem::where('member_id', $memberId)
    ->whereHas('offering', function($q) use ($startDate, $endDate) {
        $q->whereBetween('offering_date', [$startDate, $endDate])
          ->where('status', 'completed');
    })
    ->get();

echo "Community Offering Items: " . $communityOfferings->count() . " (Sum: " . $communityOfferings->sum('amount') . ")\n";

foreach ($communityOfferings as $item) {
    echo " - Item ID: {$item->id}, Amount: {$item->amount}, Offering ID: {$item->community_offering_id}\n";
    $o = $item->offering;
    if ($o) {
        echo "   Offering Date: {$o->offering_date}, Status: {$o->status}, Type: {$o->offering_type}\n";
    } else {
        echo "   Offering NOT FOUND!\n";
    }
}

$totalOfferings = $offerings->sum('amount') + $communityOfferings->sum('amount');
echo "\nCalculated Total Offerings: $totalOfferings\n";
