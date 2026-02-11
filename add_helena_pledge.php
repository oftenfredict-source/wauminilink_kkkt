<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pledge;
use Carbon\Carbon;

$memberId = 52;
$pledgeAmount = 400000;
$amountPaid = 400000; // Since user said "cash 400000" and wants it shown as paid
$description = "Ng'ombe and cash";

try {
    $pledge = new Pledge();
    $pledge->member_id = $memberId;
    $pledge->pledge_amount = $pledgeAmount;
    $pledge->amount_paid = $amountPaid;
    $pledge->pledge_date = Carbon::now();
    $pledge->due_date = Carbon::now()->addYear();
    $pledge->pledge_type = 'Ahadi ya Bwana'; // Standard type
    $pledge->purpose = 'Ahadi ya Bwana';
    $pledge->notes = $description;
    $pledge->status = 'completed'; // Fully paid
    $pledge->approval_status = 'approved';
    $pledge->recorded_by = 'System Fix';

    $pledge->save();

    echo "Successfully added pledge for Member ID $memberId.\n";
    echo "Amount: " . number_format($pledgeAmount) . "\n";
    echo "Paid: " . number_format($amountPaid) . "\n";
    echo "Notes: $description\n";

} catch (\Exception $e) {
    echo "Error adding pledge: " . $e->getMessage() . "\n";
}
