<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$membersCols = Schema::getColumnListing('members');
$childrenCols = Schema::getColumnListing('children');

file_put_contents('db_schema.txt', "Members columns:\n" . implode("\n", $membersCols) . "\n\nChildren columns:\n" . implode("\n", $childrenCols));
echo "Schema written to db_schema.txt\n";
