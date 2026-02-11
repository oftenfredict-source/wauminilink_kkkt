<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- DELETING COMMUNITY OFFERING ID 7 ---\n";

DB::transaction(function() {
    $deleted = DB::table('community_offerings')->where('id', 7)->delete();
    echo "Deleted $deleted offering record(s)\n";
    
    $remaining = DB::table('community_offerings')->count();
    echo "Remaining community offerings: $remaining\n";
});

echo "--- DELETION COMPLETE ---\n";
echo "\nPlease re-submit the offering using the envelope breakdown feature.\n";
echo "See the walkthrough document for detailed instructions.\n";
