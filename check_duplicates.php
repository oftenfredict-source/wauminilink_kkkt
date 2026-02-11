<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pledge;
use App\Models\AhadiPledge;

$memberId = 52;

echo "--- PLEDGES TABLE ---\n";
$pledges = Pledge::where('member_id', $memberId)->get();
foreach ($pledges as $p) {
    echo "ID: $p->id, Amount: $p->pledge_amount, Paid: $p->amount_paid, Desc: $p->notes\n";
}

echo "\n--- AHADI_PLEDGES TABLE ---\n";
$ahadi = AhadiPledge::where('member_id', $memberId)->get();
foreach ($ahadi as $p) {
    echo "ID: $p->id, Type: $p->item_type, Promised: $p->quantity_promised, Fulfilled: $p->quantity_fulfilled\n";
}
