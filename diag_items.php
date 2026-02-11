<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CommunityOfferingItem;
use Illuminate\Support\Facades\DB;

$outputFile = 'diag_output.txt';
$out = "Checking community_offering_items table...\n";

// Use raw DB query to avoid any model casting issues
$items = DB::table('community_offering_items')->get();

foreach ($items as $item) {
    $out .= "ID: " . $item->id . 
         " | member_id: " . (is_null($item->member_id) ? "NULL" : "[" . $item->member_id . "]") . 
         " | type: " . gettype($item->member_id) .
         " | envelope: [" . $item->envelope_number . "]\n";
}

$out .= "\nChecking members for comparison:\n";
$members = DB::table('members')->whereIn('full_name', ['Helena Shija', 'Ally Ally', 'ALLY ALLY'])->get();
foreach ($members as $m) {
    $out .= "ID: " . $m->id . " | Name: " . $m->full_name . " | Envelope: [" . $m->envelope_number . "]\n";
}

file_put_contents($outputFile, $out);
echo "Output written to $outputFile\n";
