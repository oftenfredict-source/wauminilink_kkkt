<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offering;
use App\Models\CommunityOffering;
use App\Models\CommunityOfferingItem;
use App\Models\PledgePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$start = Carbon::now()->startOfYear();
$end = Carbon::now()->endOfYear();

echo "Date Range: {$start->toDateString()} to {$end->toDateString()}\n\n";

// Offerings count
$offeringsQuery = Offering::whereBetween('offering_date', [$start, $end])->where('approval_status', 'approved');
$totalOfferings = (clone $offeringsQuery)->sum('amount');
echo "General Offerings Total: $totalOfferings\n";

$offeringTypes = (clone $offeringsQuery)
    ->select('offering_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
    ->groupBy('offering_type')
    ->get();

echo "Offering Types Breakdown:\n";
foreach ($offeringTypes as $ot) {
    echo "- {$ot->offering_type}: {$ot->total_amount} ({$ot->count} items)\n";
}

// Community Offerings
$communityOfferings = CommunityOffering::whereBetween('offering_date', [$start, $end])
    ->where('status', 'completed')
    ->get();
$totalCommunity = $communityOfferings->sum('amount');
echo "\nCommunity Offerings Total: $totalCommunity\n";
foreach ($communityOfferings as $co) {
    echo "- id: {$co->id}, type: {$co->offering_type}, amount: {$co->amount}, status: {$co->status}\n";
}

// Special Summary
$specialSummary = [
    'umoja' => $communityOfferings->where('offering_type', 'Sadaka ya Umoja')->sum('amount') + 
               (clone $offeringsQuery)->where('offering_type', 'Sadaka ya Umoja')->sum('amount'),
    'jengo' => $communityOfferings->where('offering_type', 'Sadaka ya Jengo')->sum('amount') +
               (clone $offeringsQuery)->where('offering_type', 'Sadaka ya Jengo')->sum('amount'),
];

echo "\nSpecial Summary:\n";
echo "- Umoja: {$specialSummary['umoja']}\n";
echo "- Jengo: {$specialSummary['jengo']}\n";

// Pledge Payments
$totalPledges = PledgePayment::whereBetween('payment_date', [$start, $end])->sum('amount');
echo "\nPledge Payments Total: $totalPledges\n";

$totalGiving = $specialSummary['umoja'] + $specialSummary['jengo'] + $totalPledges;
echo "\nTotal Focused Giving: $totalGiving\n";

// Check raw data for Helena specifically if she's missing
$helena = \App\Models\Member::where('full_name', 'LIKE', '%Helena Shija%')->first();
if ($helena) {
    echo "\nHelena Shija (ID: {$helena->id}) Raw Offerings Today:\n";
    $today = Carbon::now()->startOfDay();
    $raw = Offering::where('member_id', $helena->id)->whereDate('offering_date', $today)->get();
    foreach ($raw as $r) {
        echo "- ID: {$r->id}, type: {$r->offering_type}, amt: {$r->amount}, status: {$r->approval_status}, date: {$r->offering_date}\n";
    }
}
