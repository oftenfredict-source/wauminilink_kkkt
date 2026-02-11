<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$outputFile = 'full_diag_output.txt';
$out = "FULL DATA DIAGNOSTIC\n\n";

$out .= "--- MEMBERS ---\n";
$members = DB::table('members')->where('full_name', 'LIKE', '%Helena%')
    ->orWhere('full_name', 'LIKE', '%Ally%')
    ->get();
foreach ($members as $m) {
    $out .= "ID: {$m->id} | Name: {$m->full_name} | Env: [{$m->envelope_number}]\n";
}

$out .= "\n--- COMMUNITY OFFERINGS TODAY ---\n";
$today = date('Y-m-d');
$offerings = DB::table('community_offerings')->where('offering_date', '>=', date('Y-m-01'))->get(); // Show this month
foreach ($offerings as $o) {
    $out .= "ID: {$o->id} | Date: {$o->offering_date} | Type: {$o->offering_type} | Amt: {$o->amount} | Status: {$o->status}\n";
    
    $items = DB::table('community_offering_items')->where('community_offering_id', $o->id)->get();
    foreach ($items as $i) {
        $out .= "  -> Item ID: {$i->id} | MID: [" . ($i->member_id ?? 'NULL') . "] | Env: [{$i->envelope_number}] | Amt: {$i->amount}\n";
    }
}

$out .= "\n--- GENERAL OFFERINGS (Helena & Ally) ---\n";
$mids = $members->pluck('id')->toArray();
$genOfferings = DB::table('offerings')->whereIn('member_id', $mids)->get();
foreach ($genOfferings as $go) {
    $out .= "ID: {$go->id} | MID: {$go->member_id} | Date: {$go->offering_date} | Type: {$go->offering_type} | Amt: {$go->amount} | Status: {$go->approval_status}\n";
}

file_put_contents($outputFile, $out);
echo "Full diagnostic written to $outputFile\n";
