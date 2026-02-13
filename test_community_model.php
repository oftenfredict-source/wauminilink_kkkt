<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    echo "Testing Member model...\n";
    $memberCount = \App\Models\Member::count();
    echo "Member count: " . $memberCount . "\n";

    echo "Testing Community model...\n";
    $count = \App\Models\Community::count();
    echo "Community count: " . $count . "\n";

    $communities = \App\Models\Community::orderBy('name')->get();
    echo "Communities loaded: " . $communities->count() . "\n";

    echo "Success!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    // echo $e->getTraceAsString();
}
