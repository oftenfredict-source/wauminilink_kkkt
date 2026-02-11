<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- MEMBER GIVING DATA REPAIR START ---\n";

DB::transaction(function() {
    // Member IDs from previous investigation:
    // Ally Ally: ID 51 (Env: 02)
    // Helena Shija: ID 52 (Env: 01)
    
    // Items found:
    // Item ID 3: Env 54, 60k
    // Item ID 4: Env 55, 60k
    
    $allyId = 51;
    $helenaId = 52;
    
    echo "Linking Item 3 to Ally Ally (ID $allyId)...\n";
    $c3 = DB::table('community_offering_items')->where('id', 3)->update(['member_id' => $allyId]);
    echo "Rows updated: $c3\n";
    
    echo "Linking Item 4 to Helena Shija (ID $helenaId)...\n";
    $c4 = DB::table('community_offering_items')->where('id', 4)->update(['member_id' => $helenaId]);
    echo "Rows updated: $c4\n";
    
    // Verify results
    $i3 = DB::table('community_offering_items')->where('id', 3)->first();
    $i4 = DB::table('community_offering_items')->where('id', 4)->first();
    
    echo "\nVerification:\n";
    echo "Item 3 (60k): Member ID is " . ($i3->member_id ?? 'NULL') . "\n";
    echo "Item 4 (60k): Member ID is " . ($i4->member_id ?? 'NULL') . "\n";
});

echo "--- REPAIR COMPLETE ---\n";
