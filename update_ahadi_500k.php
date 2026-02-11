<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AhadiPledge;

$memberId = 52;
$pledges = AhadiPledge::where('member_id', $memberId)->get();

echo "Member $memberId has " . $pledges->count() . " pledges.\n";

$cashPledge = null;
foreach ($pledges as $p) {
    echo "Check ID: $p->id, Type: $p->item_type\n";
    if (str_contains(strtolower($p->item_type), 'cash') || str_contains(strtolower($p->item_type), 'fedha')) {
        $cashPledge = $p;
        break;
    }
}

if ($cashPledge) {
    echo "Found Cash Pledge ID: " . $cashPledge->id . "\n";

    // Update
    $cashPledge->quantity_promised = 500000;
    $cashPledge->quantity_fulfilled = 450000;

    if ($cashPledge->quantity_fulfilled >= $cashPledge->quantity_promised) {
        $cashPledge->status = 'fully_fulfilled';
    } elseif ($cashPledge->quantity_fulfilled > 0) {
        $cashPledge->status = 'partially_fulfilled';
    }

    $cashPledge->save();
    echo "Updated successfully to 500k / 450k.\n";
} else {
    echo "No Cash Pledge found. Creating new one...\n";
    AhadiPledge::create([
        'member_id' => $memberId,
        'year' => date('Y'),
        'item_type' => 'Fedha (Cash)',
        'quantity_promised' => 500000,
        'quantity_fulfilled' => 450000,
        'estimated_value' => 500000,
        'status' => 'partially_fulfilled',
        'notes' => 'Created via update script'
    ]);
    echo "Created new pledge.\n";
}
