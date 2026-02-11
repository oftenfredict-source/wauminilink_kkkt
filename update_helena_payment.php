<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pledge;

$memberId = 52;
$paymentAmount = 300000;

// Find the pledge (Ahadi ya Bwana, 400k)
$pledge = Pledge::where('member_id', $memberId)
    ->where('pledge_amount', 400000)
    ->orderBy('created_at', 'desc')
    ->first();

if ($pledge) {
    echo "Found pledge ID: " . $pledge->id . "\n";
    echo "Current Paid: " . number_format($pledge->amount_paid) . "\n";

    // Update Paid Amount
    $pledge->amount_paid = $paymentAmount;

    // Update status if fully paid (optional logic, but here it's partial/substantial)
    if ($pledge->amount_paid >= $pledge->pledge_amount) {
        $pledge->status = 'completed';
    } else {
        $pledge->status = 'active';
    }

    $pledge->save();

    echo "Updated payment to: " . number_format($pledge->amount_paid) . "\n";
    echo "Status: " . $pledge->status . "\n";
} else {
    echo "Pledge not found!\n";
}
