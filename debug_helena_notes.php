<?php

use App\Models\CommunityOffering;
use App\Models\Offering;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Searching Notes for 'Helena' ---\n";

$community = CommunityOffering::where('notes', 'LIKE', '%Helena%')
    ->orWhere('elder_notes', 'LIKE', '%Helena%')
    ->get();

foreach ($community as $c) {
    echo "Found CommunityOffering ID {$c->id}: {$c->amount} | Notes: {$c->notes} | Elder Notes: {$c->elder_notes}\n";
}

$individual = Offering::where('notes', 'LIKE', '%Helena%')->get();
foreach ($individual as $i) {
    echo "Found Offering ID {$i->id}: {$i->amount} | Notes: {$i->notes}\n";
}
