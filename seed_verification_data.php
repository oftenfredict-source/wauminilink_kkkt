<?php

use App\Models\Member;
use App\Models\Offering;
use App\Models\Pledge;
use App\Models\PledgePayment;
use Carbon\Carbon;
use Illuminate\Support\Str;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Seeding/Verifying data for Member 51...\n";

// 1. Ensure Member 51 exists
$member = Member::find(51);
if (!$member) {
    echo "Member 51 not found. Creating...\n";
    $member = Member::create([
        'id' => 51,
        'member_id' => 'MB-0051',
        'full_name' => 'Verification User 51',
        'phone_number' => '+255700000051',
        'member_type' => 'independent',
        'membership_type' => 'permanent',
        'date_of_birth' => '1990-01-01',
        'gender' => 'male',
        'created_at' => now(),
        'updated_at' => now()
    ]);
} else {
    echo "Member 51 found: " . $member->full_name . "\n";
}

// 2. Seed 'Ahadi ya Bwana' (Offering + Pledge Payment)
// Offering
Offering::create([
    'member_id' => $member->id,
    'campus_id' => 1,
    'amount' => 50000,
    'offering_date' => Carbon::now()->subMonths(1), // Last month
    'offering_type' => 'Ahadi ya Bwana',
    'service_type' => 'Sunday Service',
    'payment_method' => 'Cash',
    'approval_status' => 'approved',
    'approved_by' => 1,
    'is_verified' => true
]);
echo "Created 'Ahadi ya Bwana' Offering: 50,000\n";

// Pledge & Payment
$pledge = Pledge::create([
    'member_id' => $member->id,
    'campus_id' => 1,
    'pledge_amount' => 1000000,
    'pledge_date' => Carbon::now()->startOfYear(),
    'pledge_type' => 'general',
    'status' => 'active',
    'recorded_by' => 1
]);
PledgePayment::create([
    'pledge_id' => $pledge->id,
    'amount' => 150000, // This should add to Ahadi total
    'payment_date' => Carbon::now()->subMonths(1),
    'payment_method' => 'Mobile Money',
    'recorded_by' => 1
]);
echo "Created Pledge Payment: 150,000 (Total Ahadi for last month should be 200,000)\n";


// 3. Seed 'Sadaka ya Umoja'
Offering::create([
    'member_id' => $member->id,
    'campus_id' => 1,
    'amount' => 20000,
    'offering_date' => Carbon::now()->subMonths(1),
    'offering_type' => 'Sadaka ya Umoja',
    'service_type' => 'Sunday Service',
    'payment_method' => 'Cash',
    'approval_status' => 'approved',
    'approved_by' => 1,
    'is_verified' => true
]);
echo "Created 'Sadaka ya Umoja' Offering: 20,000\n";


// 4. Seed 'Sadaka ya Jengo'
Offering::create([
    'member_id' => $member->id,
    'campus_id' => 1,
    'amount' => 100000,
    'offering_date' => Carbon::now()->subMonths(1),
    'offering_type' => 'Sadaka ya Jengo',
    'service_type' => 'Sunday Service',
    'payment_method' => 'Cash',
    'approval_status' => 'approved',
    'approved_by' => 1,
    'is_verified' => true
]);
echo "Created 'Sadaka ya Jengo' Offering: 100,000\n";
echo "Done.\n";
