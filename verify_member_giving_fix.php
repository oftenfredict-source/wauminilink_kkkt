<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CommunityOfferingItem;
use App\Models\Member;
use Carbon\Carbon;

echo "--- FINAL MEMBER GIVING VERIFICATION ---\n";

$startDate = Carbon::now()->startOfYear();
$endDate = Carbon::now()->endOfYear();

$mids = [51, 52]; // Ally and Helena

foreach ($mids as $mid) {
    $member = Member::find($mid);
    echo "\nMember: " . ($member ? $member->full_name : "UNKNOWN") . " (ID: $mid)\n";
    
    $communityOfferings = CommunityOfferingItem::where('member_id', $mid)
        ->whereHas('offering', function($q) use ($startDate, $endDate) {
            $q->whereBetween('offering_date', [$startDate, $endDate])
              ->where('status', 'completed');
        })
        ->get();
    
    echo "Community Offerings Count: " . $communityOfferings->count() . "\n";
    echo "Total Community Amount: " . $communityOfferings->sum('amount') . "\n";
    
    foreach ($communityOfferings as $item) {
        echo " - Item ID: {$item->id}, Amt: {$item->amount}, Date: {$item->offering->offering_date}\n";
    }
}

echo "\n--- VERIFICATION COMPLETE ---\n";
