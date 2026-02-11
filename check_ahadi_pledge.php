<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\AhadiPledge;
use App\Models\Member;

$columns = DB::select('DESCRIBE ahadi_pledges');
foreach ($columns as $column) {
    echo $column->Field . " (" . $column->Type . ")\n";
}

echo "\n--- Checking AhadiPledge for Member 52 ---\n";
$pledges = AhadiPledge::where('member_id', 52)->get();
echo "Found: " . $pledges->count() . "\n";
foreach ($pledges as $p) {
    echo "ID: $p->id, Type: $p->item_type, Promised: $p->quantity_promised, Fulfilled: $p->quantity_fulfilled, Status: $p->status\n";
}
