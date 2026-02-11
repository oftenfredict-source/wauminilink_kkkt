<?php

use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Member;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Seeding 2026 Financial Data ===\n";

// Get a valid member ID
$member = Member::first();
if (!$member) {
    echo "ERROR: No members found in database.\n";
    exit(1);
}

echo "Using Member ID: {$member->id} ({$member->full_name})\n\n";

// Create some 2026 tithes
for ($i = 1; $i <= 3; $i++) {
    $tithe = Tithe::create([
        'member_id' => $member->id,
        'campus_id' => 1,
        'amount' => rand(50000, 150000),
        'tithe_date' => Carbon::create(2026, $i, rand(1, 28)),
        'payment_method' => 'Cash',
        'approval_status' => 'approved',
        'approved_by' => 1,
        'is_verified' => true
    ]);
    echo "Created Tithe: {$tithe->amount} on {$tithe->tithe_date}\n";
}

// Create some 2026 offerings
for ($i = 1; $i <= 3; $i++) {
    $offering = Offering::create([
        'member_id' => $member->id,
        'campus_id' => 1,
        'amount' => rand(100000, 300000),
        'offering_date' => Carbon::create(2026, $i, rand(1, 28)),
        'offering_type' => 'Sadaka ya Kawaida',
        'service_type' => 'Sunday Service',
        'payment_method' => 'Cash',
        'approval_status' => 'approved',
        'approved_by' => 1,
        'is_verified' => true
    ]);
    echo "Created Offering: {$offering->amount} on {$offering->offering_date}\n";
}

echo "\n=== Summary ===\n";
echo "2026 Tithes: " . Tithe::whereYear('tithe_date', 2026)->where('approval_status', 'approved')->count() . "\n";
echo "2026 Offerings: " . Offering::whereYear('offering_date', 2026)->where('approval_status', 'approved')->count() . "\n";
echo "2026 Total Income: " . (Tithe::whereYear('tithe_date', 2026)->where('approval_status', 'approved')->sum('amount') + Offering::whereYear('offering_date', 2026)->where('approval_status', 'approved')->sum('amount')) . "\n";
echo "\nDone! Refresh the analytics page to see the data.\n";
