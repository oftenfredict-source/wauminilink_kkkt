<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- EXPANDED DIAGNOSTIC: COMMUNITY OFFERINGS ---\n\n";

$offerings = DB::table('community_offerings')
    ->orderBy('id', 'desc')
    ->limit(15)
    ->get();

if ($offerings->isEmpty()) {
    echo "No community offerings found.\n";
} else {
    echo str_pad("ID", 5) . " | " . str_pad("Type", 20) . " | " . str_pad("Date", 12) . " | " . str_pad("Amount", 12) . " | " . "Status\n";
    echo str_repeat("-", 70) . "\n";
    
    foreach ($offerings as $offering) {
        echo str_pad($offering->id, 5) . " | " . 
             str_pad($offering->offering_type, 20) . " | " . 
             str_pad($offering->offering_date, 12) . " | " . 
             str_pad(number_format($offering->amount, 0), 12) . " | " . 
             $offering->status . "\n";
        
        $items = DB::table('community_offering_items')
            ->where('community_offering_id', $offering->id)
            ->get();
            
        if ($items->count() > 0) {
            echo "  Items (" . $items->count() . "):\n";
            foreach ($items as $item) {
                $member = DB::table('members')->where('id', $item->member_id)->first();
                $name = $member ? $member->full_name : "UNKNOWN (ID: {$item->member_id})";
                echo "    - {$name} | Amount: " . number_format($item->amount, 0) . "\n";
            }
        } else {
            echo "  * NO ITEMS (BREAKDOWN MISSING) *\n";
        }
        echo "\n";
    }
}
