<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$ids = DB::table('community_offering_items')->pluck('id');
echo "IDS: " . implode(',', $ids->toArray()) . "\n";
echo "COUNT: " . count($ids) . "\n";
