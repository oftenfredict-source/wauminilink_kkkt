<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offering;
use App\Models\CommunityOffering;

echo "--- GENERAL OFFERINGS (Offering Model) ---\n";
$offeringSummary = Offering::whereIn('offering_type', ['Sadaka ya Umoja', 'Sadaka ya Jengo'])
    ->select('offering_type', \DB::raw('SUM(amount) as total'), \DB::raw('COUNT(*) as count'))
    ->groupBy('offering_type')
    ->get();

foreach ($offeringSummary as $row) {
    echo "Type: {$row->offering_type} | Total: {$row->total} | Count: {$row->count}\n";
}

echo "\n--- COMMUNITY OFFERINGS (CommunityOffering Model) ---\n";
$communitySummary = CommunityOffering::whereIn('offering_type', ['Sadaka ya Umoja', 'Sadaka ya Jengo'])
    ->select('offering_type', \DB::raw('SUM(amount) as total'), \DB::raw('COUNT(*) as count'))
    ->groupBy('offering_type')
    ->get();

foreach ($communitySummary as $row) {
    echo "Type: {$row->offering_type} | Total: {$row->total} | Count: {$row->count}\n";
}

echo "\n--- PLEDGE PAYMENTS ---\n";
$pledgePayments = \App\Models\PledgePayment::sum('amount');
echo "Total Pledge Payments: $pledgePayments\n";
