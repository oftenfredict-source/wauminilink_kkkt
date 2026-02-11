<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- CORRECTING ENVELOPE BREAKDOWN FOR OFFERING ID 10 ---\n";

DB::transaction(function() {
    // First, delete the incorrect item
    $deleted = DB::table('community_offering_items')
        ->where('community_offering_id', 10)
        ->delete();
    
    echo "Deleted $deleted incorrect item(s)\n";
    
    // Now add the correct breakdown
    $items = [
        [
            'community_offering_id' => 10,
            'member_id' => 51, // Ally Ally
            'envelope_number' => '02',
            'amount' => 20000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'community_offering_id' => 10,
            'member_id' => 52, // Helena Shija
            'envelope_number' => '01',
            'amount' => 40000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];
    
    foreach ($items as $item) {
        DB::table('community_offering_items')->insert($item);
        $member = DB::table('members')->where('id', $item['member_id'])->first();
        echo "Added: {$member->full_name} (Env: {$item['envelope_number']}) = {$item['amount']}\n";
    }
    
    echo "\nVerification:\n";
    $insertedItems = DB::table('community_offering_items')
        ->where('community_offering_id', 10)
        ->get();
    
    $total = 0;
    foreach ($insertedItems as $i) {
        $member = DB::table('members')->where('id', $i->member_id)->first();
        echo "- {$member->full_name}: {$i->amount}\n";
        $total += $i->amount;
    }
    echo "Total: {$total}\n";
});

echo "\n--- CORRECTION COMPLETE ---\n";
echo "Now check:\n";
echo "- Helena's report: http://127.0.0.1:8000/reports/member-giving?member_id=52 (should show 40,000)\n";
echo "- Ally's report: http://127.0.0.1:8000/reports/member-giving?member_id=51 (should show 20,000)\n";
