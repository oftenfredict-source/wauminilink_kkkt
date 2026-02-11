<?php

use App\Models\Community;
use App\Models\Offering;
use App\Models\CommunityOffering;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$communityId = 6;
$start = Carbon::now()->startOfMonth();
$end = Carbon::now()->endOfMonth();

echo "Debug Report for Community ID: $communityId\n";
echo "Date Range: " . $start->toDateString() . " to " . $end->toDateString() . "\n";

// 1. Check Individual Member Offerings
$memberOfferingsQuery = Offering::whereHas('member', function ($query) use ($communityId) {
    $query->where('community_id', $communityId);
})
    ->whereBetween('offering_date', [$start, $end]);

$totalMemberOfferings = (clone $memberOfferingsQuery)->sum('amount');
$approvedMemberOfferings = (clone $memberOfferingsQuery)->where('approval_status', 'approved')->sum('amount');
$pendingMemberOfferings = (clone $memberOfferingsQuery)->where('approval_status', 'pending')->sum('amount');

echo "\n--- Individual Member Offerings ---\n";
echo "Total (All Status): " . number_format($totalMemberOfferings, 2) . "\n";
echo "Approved: " . number_format($approvedMemberOfferings, 2) . "\n";
echo "Pending: " . number_format($pendingMemberOfferings, 2) . "\n";
echo "Count: " . $memberOfferingsQuery->count() . "\n";

// 2. Check Community Offerings (Mid-week/Service inputs)
$communityOfferingsQuery = CommunityOffering::where('community_id', $communityId)
    ->whereBetween('created_at', [$start, $end]); // Assuming created_at or is there a specific date field?

$totalCommunityOfferings = $communityOfferingsQuery->sum('amount');

echo "\n--- Community Offerings (Direct) ---\n";
echo "Total: " . number_format($totalCommunityOfferings, 2) . "\n";
echo "Count: " . $communityOfferingsQuery->count() . "\n";

// 3. Check for Data Outside Range (to see if it's just a date issue)
$allTimeMemberOfferings = Offering::whereHas('member', function ($query) use ($communityId) {
    $query->where('community_id', $communityId);
})->sum('amount');

$allTimeCommunityOfferings = CommunityOffering::where('community_id', $communityId)->sum('amount');

echo "\n--- All Time Data Check ---\n";
echo "All Time Member Offerings: " . number_format($allTimeMemberOfferings, 2) . "\n";
echo "All Time Community Offerings: " . number_format($allTimeCommunityOfferings, 2) . "\n";
