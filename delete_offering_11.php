<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- DELETING INCOMPLETE OFFERING ID 11 ---\n";

DB::transaction(function() {
    // Check if it exists and has items
    $offering = DB::table('community_offerings')->where('id', 11)->first();
    if (!$offering) {
        echo "Offering ID 11 not found.\n";
        return;
    }

    $itmCount = DB::table('community_offering_items')->where('community_offering_id', 11)->count();
    echo "Found offering ID 11: {$offering->offering_type}, Total: {$offering->amount}, Items: {$itmCount}\n";

    if ($itmCount == 0) {
        DB::table('community_offerings')->where('id', 11)->delete();
        echo "Offering ID 11 deleted successfully.\n";
    } else {
        echo "ABORT: Offering ID 11 actually HAS items! Not deleting.\n";
    }
});

echo "--- COMPLETE ---\n";
