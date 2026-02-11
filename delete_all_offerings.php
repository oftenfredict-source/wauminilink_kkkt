<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'branch_offerings',
    'community_offering_items',
    'community_offerings',
    'offering_collection_items',
    'offering_collection_sessions',
    'offerings',
    'tithes'
];

echo "Deleting data from offering related tables...\n";

Schema::disableForeignKeyConstraints();

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "- Clearing {$table}...\n";
        DB::table($table)->truncate();
    }
}

Schema::enableForeignKeyConstraints();

echo "\nDone! All offering and tithe data has been deleted.\n";
