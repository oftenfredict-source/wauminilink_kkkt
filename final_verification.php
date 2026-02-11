<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CommunityOfferingItem;
use App\Models\Member;
use Carbon\Carbon;

echo "--- FINAL VERIFICATION ---\n\n";

$startDate = Carbon::now()->startOfYear();
$endDate = Carbon::now()->endOfYear();

$members = [
    ['id' => 51, 'name' => 'Ally Ally', 'expected' => 20000],
    ['id' => 52, 'name' => 'Helena Shija', 'expected' => 40000],
];

foreach ($members as $m) {
    echo "Member: {$m['name']} (ID: {$m['id']})\n";
    
    $communityOfferings = CommunityOfferingItem::where('member_id', $m['id'])
        ->whereHas('offering', function($q) use ($startDate, $endDate) {
            $q->whereBetween('offering_date', [$startDate, $endDate])
              ->where('status', 'completed');
        })
        ->get();
    
    $total = $communityOfferings->sum('amount');
    
    echo "  Community Offerings Count: " . $communityOfferings->count() . "\n";
    echo "  Total Amount: " . number_format($total, 2) . " TZS\n";
    echo "  Expected: " . number_format($m['expected'], 2) . " TZS\n";
    echo "  Status: " . ($total == $m['expected'] ? "✅ CORRECT" : "❌ MISMATCH") . "\n";
    
    foreach ($communityOfferings as $item) {
        echo "    - Offering ID: {$item->offering->id}, Amount: {$item->amount}, Date: {$item->offering->offering_date}\n";
    }
    
    echo "\n";
}

echo "--- VERIFICATION COMPLETE ---\n";
echo "\nPlease check the member reports in your browser:\n";
echo "- Ally: http://127.0.0.1:8000/reports/member-giving?member_id=51\n";
echo "- Helena: http://127.0.0.1:8000/reports/member-giving?member_id=52\n";
