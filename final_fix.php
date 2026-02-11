<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Targeted Repair Start...\n";

// ID 52 is Helena, ID 51 is Ally Ally
$fixes = [
    7 => 52,
    8 => 51
];

foreach ($fixes as $itemId => $memberId) {
    $count = DB::table('community_offering_items')
        ->where('id', $itemId)
        ->update(['member_id' => $memberId]);
    
    if ($count > 0) {
        echo "Updated Item $itemId: set member_id to $memberId\n";
    } else {
        echo "Failed to update Item $itemId (maybe already set?)\n";
    }
}

// Also check for any other items with envelope 01 or 02 that might be orphaned
$orphans = DB::table('community_offering_items')->whereIn('envelope_number', ['01', '02'])->whereNull('member_id')->get();
echo "Found " . $orphans->count() . " remaining orphans for env 01/02.\n";

echo "Targeted Repair Complete.\n";
