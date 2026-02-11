<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select("SHOW TABLES");
$dbName = config('database.connections.mysql.database');
$key = "Tables_in_" . $dbName;

echo "Table Row Counts in $dbName:\n";
foreach ($tables as $table) {
    $tableName = $table->$key;
    $count = DB::table($tableName)->count();
    echo "- $tableName: $count\n";
}
