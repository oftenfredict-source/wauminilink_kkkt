<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- TARGETED CHECK: SADAKA YA JENGO STATUS ---\n\n";

$offerings = DB::table('community_offerings')
    ->where('offering_type', 'Sadaka ya Jengo')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($offerings as $o) {
    echo "ID: {$o->id} | Date: {$o->offering_date} | Amt: " . number_format($o->amount, 0) . " | Status: {$o->status}\n";
    
    $itemCount = DB::table('community_offering_items')->where('community_offering_id', $o->id)->count();
    echo "  Items Linked: {$itemCount}\n";
}
