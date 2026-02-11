<?php

use App\Models\CommunityOffering;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Latest Community Offering ===\n\n";

$latest = CommunityOffering::orderBy('id', 'desc')->first();
if ($latest) {
    echo "ID: {$latest->id}\n";
    echo "Total Amount: {$latest->total_amount}\n";
    echo "Offering Date: {$latest->offering_date}\n";
    echo "Year: " . Carbon::parse($latest->offering_date)->year . "\n";
    echo "Type: {$latest->offering_type}\n";
    echo "Approval Status: {$latest->approval_status}\n";
    echo "Created: {$latest->created_at}\n\n";
    
    if ($latest->approval_status !== 'approved') {
        echo "⚠️  ISSUE: This offering is NOT approved yet!\n";
        echo "   Current status: {$latest->approval_status}\n";
        echo "   It needs to be approved before it shows in analytics.\n";
    }
    
    $year = Carbon::parse($latest->offering_date)->year;
    if ($year != 2026) {
        echo "⚠️  ISSUE: This offering is from year {$year}, not 2026!\n";
        echo "   The analytics page defaults to 2026.\n";
        echo "   Change the year filter to {$year} to see this data.\n";
    }
} else {
    echo "No community offerings found.\n";
}
