<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offering;
use App\Models\CommunityOffering;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$start = Carbon::now()->startOfYear();
$end = Carbon::now()->endOfYear();

echo "DEBUG OVERVIEW LOGIC\n";
echo "Date Range: {$start->toDateString()} to {$end->toDateString()}\n";

// Simulate Secretary (Non-Elder)
$isChurchElder = false;
$communityIds = [];

$offerings = Offering::whereBetween('offering_date', [$start, $end])->where('approval_status', 'approved');
$totalOfferingsRaw = (clone $offerings)->sum('amount');
echo "Total Approved Offerings In Year: $totalOfferingsRaw\n";

$communityOfferingsQuery = CommunityOffering::whereBetween('offering_date', [$start, $end])->where('status', 'completed');
$communityOfferings = $communityOfferingsQuery->get();
echo "Total Completed Community Offerings: {$communityOfferings->sum('amount')}\n";

$specialOfferingsSummary = [
    'umoja' => $communityOfferings->where('offering_type', 'Sadaka ya Umoja')->sum('amount') + 
               (clone $offerings)->where('offering_type', 'Sadaka ya Umoja')->sum('amount'),
    'jengo' => $communityOfferings->where('offering_type', 'Sadaka ya Jengo')->sum('amount') +
               (clone $offerings)->where('offering_type', 'Sadaka ya Jengo')->sum('amount'),
];

echo "Umoja Sum: {$specialOfferingsSummary['umoja']}\n";
echo "Jengo Sum: {$specialOfferingsSummary['jengo']}\n";

$pledgePaymentsQuery = \App\Models\PledgePayment::whereBetween('payment_date', [$start, $end]);
$totalPledgesPaid = $pledgePaymentsQuery->sum('amount');
echo "Pledges Paid: $totalPledgesPaid\n";

$totalGiving = $specialOfferingsSummary['umoja'] + $specialOfferingsSummary['jengo'] + $totalPledgesPaid;
echo "FINAL CALC TOTAL GIVING: $totalGiving\n";

$transactionsCount = $communityOfferings->count() + $pledgePaymentsQuery->count();
echo "TRANSACTION COUNT (Current Logic): $transactionsCount\n";

echo "\nLatest Community Offering Item types:\n";
foreach(App\Models\CommunityOffering::latest()->limit(5)->get() as $co) {
    echo "- ID: {$co->id}, Type: '{$co->offering_type}', Amt: {$co->amount}, Status: '{$co->status}'\n";
}

echo "\nLatest Offering Table types (Approved):\n";
foreach(App\Models\Offering::where('approval_status', 'approved')->latest()->limit(5)->get() as $o) {
    echo "- ID: {$o->id}, Type: '{$o->offering_type}', Amt: {$o->amount}, Date: {$o->offering_date}\n";
}
