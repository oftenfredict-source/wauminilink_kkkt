<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AhadiPledge;

// ID 8 is the Cash pledge for Member 52 we found earlier
$pledge = AhadiPledge::find(8);

if ($pledge) {
    echo "Found AhadiPledge ID: " . $pledge->id . "\n";
    echo "Type: " . $pledge->item_type . "\n";
    echo "Current Fulfilled: " . $pledge->quantity_fulfilled . "\n";

    // Update to 300,000
    $pledge->quantity_fulfilled = 300000;

    // Recalculate status
    if ($pledge->quantity_fulfilled >= $pledge->quantity_promised) {
        $pledge->status = 'fully_fulfilled';
    } elseif ($pledge->quantity_fulfilled > 0) {
        $pledge->status = 'partially_fulfilled';
    } else {
        $pledge->status = 'promised';
    }

    $pledge->save();

    echo "Updated Fulfilled to: " . number_format($pledge->quantity_fulfilled) . "\n";
    echo "New Status: " . $pledge->status . "\n";
} else {
    echo "AhadiPledge ID 8 not found!\n";
}
