<?php

use App\Models\Offering;
use App\Models\CommunityOffering;
use App\Models\Tithe;
use App\Models\Expense;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Checking Latest Offerings ===\n\n";

// Get the latest offering
$latestOffering = Offering::orderBy('created_at', 'desc')->first();
if ($latestOffering) {
    echo "Latest Offering:\n";
    echo "  ID: {$latestOffering->id}\n";
    echo "  Amount: {$latestOffering->amount}\n";
    echo "  Date: {$latestOffering->offering_date}\n";
    echo "  Year: " . Carbon::parse($latestOffering->offering_date)->year . "\n";
    echo "  Type: {$latestOffering->offering_type}\n";
    echo "  Approval Status: {$latestOffering->approval_status}\n";
    echo "  Created At: {$latestOffering->created_at}\n\n";
}

// Check 2026 approved offerings
$offerings2026 = Offering::whereYear('offering_date', 2026)
    ->where('approval_status', 'approved')
    ->get();

echo "2026 Approved Offerings Count: " . $offerings2026->count() . "\n";
echo "2026 Approved Offerings Total: " . $offerings2026->sum('amount') . "\n\n";

if ($offerings2026->count() > 0) {
    echo "Details:\n";
    foreach($offerings2026 as $off) {
        echo "  ID: {$off->id}, Amount: {$off->amount}, Date: {$off->offering_date}, Type: {$off->offering_type}\n";
    }
}

// Check 2026 approved tithes
$tithes2026 = Tithe::whereYear('tithe_date', 2026)
    ->where('approval_status', 'approved')
    ->sum('amount');

echo "\n2026 Approved Tithes Total: {$tithes2026}\n";

// Check 2026 approved expenses
$expenses2026 = Expense::whereYear('expense_date', 2026)
    ->where('approval_status', 'approved')
    ->sum('amount');

echo "2026 Approved Expenses Total: {$expenses2026}\n";

// Calculate Net Income
$netIncome = ($tithes2026 + $offerings2026->sum('amount')) - $expenses2026;
echo "\n=== Expected Net Income for 2026 ===\n";
echo "Income (Tithes + Offerings): " . ($tithes2026 + $offerings2026->sum('amount')) . "\n";
echo "Expenses: {$expenses2026}\n";
echo "Net Income: {$netIncome}\n";
