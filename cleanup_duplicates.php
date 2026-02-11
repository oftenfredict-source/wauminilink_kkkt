<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pledge;
use App\Models\AhadiPledge;

// 1. Delete duplicate from Pledges table
$pledge = Pledge::find(2);
if ($pledge) {
    echo "Deleting Pledge ID 2 (Amount: " . $pledge->pledge_amount . ")...\n";
    $pledge->delete();
    echo "Deleted.\n";
} else {
    echo "Pledge ID 2 not found.\n";
}

// 2. Correct AhadiPledge to 300k (if it's 400k)
$ahadi = AhadiPledge::find(8);
if ($ahadi) {
    echo "AhadiPledge ID 8 Fulfilled: " . $ahadi->quantity_fulfilled . "\n";
    if ($ahadi->quantity_fulfilled != 300000) {
        $ahadi->quantity_fulfilled = 300000;
        $ahadi->status = 'partially_fulfilled';
        $ahadi->save();
        echo "Updated AhadiPledge ID 8 to 300,000.\n";
    }
}
