<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$items = DB::table('community_offering_items')->get();
echo "TOTAL ITEMS: " . $items->count() . "\n";
foreach ($items as $i) {
    echo "ID:{$i->id} | COID:{$i->community_offering_id} | MID:" . ($i->member_id ?? 'NULL') . " | ENV:{$i->envelope_number} | AMT:{$i->amount}\n";
}

$offs = DB::table('community_offerings')->get();
echo "\nTOTAL OFFERINGS: " . $offs->count() . "\n";
foreach ($offs as $o) {
    echo "ID:{$o->id} | Date:{$o->offering_date} | Amt:{$o->amount} | Status:{$o->status}\n";
}
