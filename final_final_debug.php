<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "COMMUNITY OFFERINGS:\n";
$off = DB::table('community_offerings')->get();
foreach ($off as $o) {
    echo "ID:{$o->id} | Date:{$o->offering_date} | Type:{$o->offering_type} | Status:{$o->status}\n";
}

echo "\nCOMMUNITY OFFERING ITEMS:\n";
$items = DB::table('community_offering_items')->get();
foreach ($items as $i) {
    echo "ID:{$i->id} | COID:{$i->community_offering_id} | MID:" . ($i->member_id ?? 'NULL') . " | ENV:{$i->envelope_number}\n";
}
