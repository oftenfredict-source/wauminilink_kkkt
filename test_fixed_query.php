<?php

use App\Models\CommunityOffering;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

try {
    echo "Testing fixed CommunityOffering query...\n";
    
    $completed = CommunityOffering::where('status', 'completed')->count();
    echo "Completed count: {$completed}\n";
    
    $sum = CommunityOffering::where('status', 'completed')->sum('amount');
    echo "Completed sum: {$sum}\n";
    
    $year2026 = CommunityOffering::whereYear('offering_date', 2026)
        ->where('status', 'completed')
        ->sum('amount');
    echo "2026 Completed sum: {$year2026}\n";
    
    echo "\n✓ Query works perfectly!\n";
    echo "\nThe analytics page should now load without errors.\n";
    echo "However, your offering needs to have status='completed' to show in analytics.\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
