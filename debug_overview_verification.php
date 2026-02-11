<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offering;
use App\Models\CommunityOffering;
use App\Models\PledgePayment;
use Carbon\Carbon;

$start = Carbon::now()->startOfYear();
$end = Carbon::now()->endOfYear();

// Mirrors Controller Logic after Fix
$communityOfferingsQuery = CommunityOffering::whereBetween('offering_date', [$start, $end])->where('status', 'completed');
$communityOfferings = $communityOfferingsQuery->get();

$offerings = Offering::whereBetween('offering_date', [$start, $end])->where('approval_status', 'approved');

$specialOfferingsSummary = [
    'umoja' => $communityOfferings->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja'])->sum('amount') + 
               (clone $offerings)->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja'])->sum('amount'),
    'jengo' => $communityOfferings->whereIn('offering_type', ['Sadaka ya Jengo', 'sadaka_jengo'])->sum('amount') +
               (clone $offerings)->whereIn('offering_type', ['Sadaka ya Jengo', 'sadaka_jengo'])->sum('amount'),
];

$totalPledgesPaid = PledgePayment::whereBetween('payment_date', [$start, $end])->sum('amount');
$totalGiving = $specialOfferingsSummary['umoja'] + $specialOfferingsSummary['jengo'] + $totalPledgesPaid;

$transactionsCount = $communityOfferingsQuery->count() + 
                     PledgePayment::whereBetween('payment_date', [$start, $end])->count() + 
                     (clone $offerings)->whereIn('offering_type', ['Sadaka ya Umoja', 'Sadaka ya Jengo', 'sadaka_umoja', 'sadaka_jengo'])->count();

echo "--- FINAL VERIFICATION ---\n";
echo "Total Focused Giving: " . number_format($totalGiving, 2) . "\n";
echo "Umoja Sum: " . number_format($specialOfferingsSummary['umoja'], 2) . "\n";
echo "Jengo Sum: " . number_format($specialOfferingsSummary['jengo'], 2) . "\n";
echo "Pledges Sum: " . number_format($totalPledgesPaid, 2) . "\n";
echo "Transaction Count: $transactionsCount\n";

// Check specifically Helena Shija
$helena = \App\Models\Member::where('full_name', 'LIKE', '%Helena Shija%')->first();
if ($helena) {
    echo "\nHelena Shija Data:\n";
    $hItems = \App\Models\CommunityOfferingItem::where('member_id', $helena->id)->get();
    echo "- Community Items Count: " . $hItems->count() . " (Sum: " . $hItems->sum('amount') . ")\n";
}

// Check specifically Ally Ally
$ally = \App\Models\Member::where('full_name', 'LIKE', '%Ally Ally%')->first();
if ($ally) {
    echo "\nAlly Ally Data:\n";
    $aItems = \App\Models\CommunityOfferingItem::where('member_id', $ally->id)->get();
    echo "- Community Items Count: " . $aItems->count() . " (Sum: " . $aItems->sum('amount') . ")\n";
}
echo "--- VERIFICATION COMPLETE ---\n";
