<?php

use App\Models\CommunityOffering;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

try {
    echo "Testing CommunityOffering query...\n";
    $count = CommunityOffering::count();
    echo "Total count: {$count}\n";
    
    $approved = CommunityOffering::where('approval_status', 'approved')->count();
    echo "Approved count: {$approved}\n";
    
    $sum = CommunityOffering::where('approval_status', 'approved')->sum('total_amount');
    echo "Approved sum: {$sum}\n";
    
    echo "\n✓ Query works fine!\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
