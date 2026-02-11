<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- ULTIMATE FIX START ---\n";

// 1. Find the correct member IDs
$helena = DB::table('members')->where('full_name', 'LIKE', '%Helena Shija%')->first();
$ally = DB::table('members')->where('full_name', 'LIKE', '%Ally Ally%')->where('envelope_number', '02')->first();

if (!$helena || !$ally) {
    echo "ERROR: Member not found!\n";
    if($helena) echo "Helena: {$helena->id}\n";
    if($ally) echo "Ally: {$ally->id}\n";
    // exit(); // Don't exit, might have different names
}

$helenaId = $helena ? $helena->id : 52;
$allyId = $ally ? $ally->id : 51;

echo "Targeting Helena (ID $helenaId) and Ally (ID $allyId)\n";

// 2. Perform the update
$hCount = DB::table('community_offering_items')->where('id', 7)->update(['member_id' => $helenaId]);
$aCount = DB::table('community_offering_items')->where('id', 8)->update(['member_id' => $allyId]);

echo "Updated Item 7: $hCount row(s)\n";
echo "Updated Item 8: $aCount row(s)\n";

// 3. Verify
$item7 = DB::table('community_offering_items')->where('id', 7)->first();
$item8 = DB::table('community_offering_items')->where('id', 8)->first();

echo "Verification:\n";
echo "Item 7: member_id=[" . ($item7->member_id ?? 'NULL') . "]\n";
echo "Item 8: member_id=[" . ($item8->member_id ?? 'NULL') . "]\n";

echo "--- ULTIMATE FIX COMPLETE ---\n";
