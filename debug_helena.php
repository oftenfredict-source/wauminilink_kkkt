<?php

use App\Models\Member;
use App\Models\Offering;
use App\Models\CommunityOffering;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Searching for Helena ---\n";
$helenas = Member::where('full_name', 'LIKE', '%Helena%')->get();

if ($helenas->isEmpty()) {
    echo "No member found with name 'Helena'.\n";
    exit;
}

foreach ($helenas as $helena) {
    echo "Found Member: {$helena->full_name} (ID: {$helena->id})\n";

    echo "  > Offerings (Individual):\n";
    $offerings = Offering::where('member_id', $helena->id)->get();

    if ($offerings->isEmpty()) {
        echo "    No individual offerings found.\n";
    } else {
        foreach ($offerings as $off) {
            echo "    - ID: {$off->id} | Type: {$off->offering_type} | Amount: {$off->amount} | Date: {$off->offering_date} | Status: {$off->approval_status}\n";
        }
    }

    echo "  > Checking for Jengo (20000) or Umoja (40000) specifically:\n";
    // Check if maybe recorded under a different user but with notes mentioning Helena?
    // Or just look for any offering with those amounts to trace them.
}

echo "\n--- Global Search for amounts 20000 and 40000 ---\n";
$amounts = Offering::whereIn('amount', [20000, 40000])->latest()->take(5)->get();
foreach ($amounts as $off) {
    $m = $off->member;
    echo "Found Amount {$off->amount}: ID {$off->id} | Member: " . ($m ? $m->full_name : 'None') . " | Type: {$off->offering_type} | Status: {$off->approval_status}\n";
}
