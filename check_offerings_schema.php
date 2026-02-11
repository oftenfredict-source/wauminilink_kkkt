<?php
// Check community offerings table structure
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Community Offerings Table Structure ===\n\n";

$columns = DB::select('SHOW COLUMNS FROM community_offerings');

echo "Columns:\n";
echo str_repeat("-", 80) . "\n";
printf("%-30s %-15s %-10s\n", "Field", "Type", "Null");
echo str_repeat("-", 80) . "\n";

foreach ($columns as $col) {
    printf("%-30s %-15s %-10s\n", $col->Field, $col->Type, $col->Null);
}

echo "\n\n=== Sample Data ===\n\n";

$sample = DB::table('community_offerings')->first();

if ($sample) {
    echo "Sample record:\n";
    foreach ($sample as $key => $value) {
        echo "  {$key}: " . ($value ?? 'NULL') . "\n";
    }
}
