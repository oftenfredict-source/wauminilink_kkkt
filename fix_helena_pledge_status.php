<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pledge;

$memberId = 52;

// Find the pledge we just added (recorded_by 'System Fix' or matches amount)
$pledge = Pledge::where('member_id', $memberId)
    ->where('pledge_amount', 400000)
    // ->where('amount_paid', 400000) // It might be this
    ->orderBy('created_at', 'desc')
    ->first();

if ($pledge) {
    echo "Found pledge ID: " . $pledge->id . "\n";
    echo "Current Status: " . $pledge->status . "\n";
    echo "Current Paid: " . $pledge->amount_paid . "\n";

    // Update to Unpaid
    $pledge->amount_paid = 0;
    $pledge->status = 'active'; // Not completed
    $pledge->save();

    echo "Updated pledge to Unpaid (active).\n";
    echo "New Paid Amount: " . $pledge->amount_paid . "\n";
} else {
    echo "Pledge not found!\n";
}
