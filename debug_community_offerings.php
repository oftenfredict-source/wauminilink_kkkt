<?php

use App\Models\CommunityOffering;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Checking Community Offerings ===\n\n";

// Get the latest community offering
$latestCommunityOffering = CommunityOffering::orderBy('created_at', 'desc')->first();
if ($latestCommunityOffering) {
    echo "Latest Community Offering:\n";
    echo "  ID: {$latestCommunityOffering->id}\n";
    echo "  Total Amount: {$latestCommunityOffering->total_amount}\n";
    echo "  Date: {$latestCommunityOffering->offering_date}\n";
    echo "  Year: " . Carbon::parse($latestCommunityOffering->offering_date)->year . "\n";
    echo "  Type: {$latestCommunityOffering->offering_type}\n";
    echo "  Approval Status: {$latestCommunityOffering->approval_status}\n";
    echo "  Created At: {$latestCommunityOffering->created_at}\n\n";
}

// Check 2026 approved community offerings
$communityOfferings2026 = CommunityOffering::whereYear('offering_date', 2026)
    ->where('approval_status', 'approved')
    ->get();

echo "2026 Approved Community Offerings Count: " . $communityOfferings2026->count() . "\n";
echo "2026 Approved Community Offerings Total: " . $communityOfferings2026->sum('total_amount') . "\n\n";

if ($communityOfferings2026->count() > 0) {
    echo "Details:\n";
    foreach($communityOfferings2026 as $off) {
        echo "  ID: {$off->id}, Amount: {$off->total_amount}, Date: {$off->offering_date}, Type: {$off->offering_type}\n";
    }
}

echo "\n=== ISSUE IDENTIFIED ===\n";
echo "The Analytics controller is only querying the 'offerings' table.\n";
echo "It does NOT include 'community_offerings' table in the calculations.\n";
echo "This is why your Mid-Week Offering is not showing in Net Income.\n";
