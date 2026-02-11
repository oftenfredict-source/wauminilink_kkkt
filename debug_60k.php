<?php

use App\Models\Offering;
use App\Models\CommunityOffering;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Searching for 60000 ---\n";
echo "Individual:\n";
$off = Offering::where('amount', 60000)->get();
foreach ($off as $o) {
    echo "ID {$o->id} | Member {$o->member_id}\n";
}

echo "Community:\n";
$comm = CommunityOffering::where('amount', 60000)->get();
foreach ($comm as $c) {
    echo "ID {$c->id} | Notes {$c->notes}\n";
}
