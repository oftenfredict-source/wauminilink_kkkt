<?php

use App\Models\Offering;
use App\Models\Member;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Recent Offerings Dump ---\n";
$recent = Offering::latest()->take(10)->get();

foreach ($recent as $offering) {
    echo "ID: {$offering->id} | Member: {$offering->member_id} | Amount: {$offering->amount} | Type: {$offering->offering_type} | Status: {$offering->approval_status}\n";
}

echo "\n--- Jengo/Umoja Specific Check ---\n";
$special = Offering::whereIn('offering_type', [
    'Sadaka ya Jengo',
    'sadaka_jengo',
    'Sadaka ya Umoja',
    'sadaka_umoja'
])->get();

echo "Found " . $special->count() . " special offerings.\n";
foreach ($special as $offering) {
    echo "ID: {$offering->id} | Member: {$offering->member_id} | Amount: {$offering->amount} | Type: {$offering->offering_type}\n";
}
