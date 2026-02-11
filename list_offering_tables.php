<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $table_array = (array) $table;
    $table_name = reset($table_array);
    if (strpos($table_name, 'offering') !== false || strpos($table_name, 'tithe') !== false) {
        echo $table_name . PHP_EOL;
    }
}
