<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- DELETING COMMUNITY OFFERING ID 6 ---\n";

DB::transaction(function() {
    // Delete the offering (items will cascade delete due to foreign key)
    $deleted = DB::table('community_offerings')->where('id', 6)->delete();
    
    echo "Deleted $deleted offering record(s)\n";
    
    // Verify deletion
    $remaining = DB::table('community_offerings')->count();
    echo "Remaining community offerings: $remaining\n";
});

echo "--- DELETION COMPLETE ---\n";
echo "\nYou can now re-submit the offering with the proper envelope breakdown.\n";
