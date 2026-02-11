<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- MANUALLY ADDING ENVELOPE BREAKDOWN TO OFFERING ID 10 ---\n";

DB::transaction(function() {
    // Check if offering 10 exists
    $offering = DB::table('community_offerings')->where('id', 10)->first();
    
    if (!$offering) {
        echo "ERROR: Offering ID 10 not found!\n";
        return;
    }
    
    echo "Found offering: ID {$offering->id}, Amount: {$offering->amount}\n";
    
    // Add envelope breakdown items
    // Assuming the 60,000 is from Helena (you can adjust this)
    $items = [
        [
            'community_offering_id' => 10,
            'member_id' => 52, // Helena Shija
            'envelope_number' => '01',
            'amount' => 60000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];
    
    foreach ($items as $item) {
        DB::table('community_offering_items')->insert($item);
        echo "Added item: Envelope {$item['envelope_number']}, Member ID {$item['member_id']}, Amount {$item['amount']}\n";
    }
    
    echo "\nVerification:\n";
    $insertedItems = DB::table('community_offering_items')
        ->where('community_offering_id', 10)
        ->get();
    
    foreach ($insertedItems as $i) {
        $member = DB::table('members')->where('id', $i->member_id)->first();
        echo "- Item ID {$i->id}: {$member->full_name} (Env: {$i->envelope_number}) = {$i->amount}\n";
    }
});

echo "\n--- MANUAL BREAKDOWN COMPLETE ---\n";
echo "Now check Helena's member giving report at:\n";
echo "http://127.0.0.1:8000/reports/member-giving?member_id=52\n";
