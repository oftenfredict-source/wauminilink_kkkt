<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- DELETING COMMUNITY OFFERING ID 9 ---\n";

DB::transaction(function() {
    $deleted = DB::table('community_offerings')->where('id', 9)->delete();
    echo "Deleted $deleted offering record(s)\n";
    
    $remaining = DB::table('community_offerings')->count();
    echo "Remaining community offerings: $remaining\n";
});

echo "--- DELETION COMPLETE ---\n";
echo "\nBOTH forms have now been updated with:\n";
echo "1. Prominent WARNING banner\n";
echo "2. Client-side validation to prevent empty submissions\n";
echo "\nPlease try submitting again. The form will now REQUIRE envelope breakdown.\n";
