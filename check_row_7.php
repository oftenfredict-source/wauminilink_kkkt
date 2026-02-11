<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$row = DB::table('community_offering_items')->where('id', 7)->first();
var_dump($row);

$row8 = DB::table('community_offering_items')->where('id', 8)->first();
var_dump($row8);
