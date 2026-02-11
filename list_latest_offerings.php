<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- LATEST COMMUNITY OFFERINGS ---\n\n";

$offerings = DB::table('community_offerings')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($offerings as $o) {
    echo "ID: {$o->id} | Type: {$o->offering_type} | Amt: " . number_format($o->amount, 0) . " | Status: {$o->status} | Created: {$o->created_at}\n";
    $itemCount = DB::table('community_offering_items')->where('community_offering_id', $o->id)->count();
    echo "  Items Linked: {$itemCount}\n";
}
