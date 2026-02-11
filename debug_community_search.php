<?php

use App\Models\CommunityOffering;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Searching Community Offerings for 20000 or 40000 ---\n";

$matches = CommunityOffering::whereIn('amount', [20000, 40000])
    ->orWhereIn('amount_jengo', [20000, 40000])
    ->orWhereIn('amount_umoja', [20000, 40000])
    ->get();

if ($matches->isEmpty()) {
    echo "No matching community offerings found.\n";
}

foreach ($matches as $c) {
    echo "Found ID {$c->id}: Total {$c->amount} | Jengo {$c->amount_jengo} | Umoja {$c->amount_umoja} | Type {$c->offering_type} | Notes: {$c->notes}\n";
}
