<?php

use App\Models\CommunityOffering;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Community Offerings Split Check ---\n";
$differ = CommunityOffering::all();

foreach ($differ as $offering) {
    echo "ID: {$offering->id} | Type: {$offering->offering_type} | Total: {$offering->amount} | Umoja: {$offering->amount_umoja} | Jengo: {$offering->amount_jengo}\n";
}
