<?php

use App\Models\Tithe;
use App\Models\Offering;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Financial Data Check ===\n";
echo "Approved Tithes Count: " . Tithe::where('approval_status', 'approved')->count() . "\n";
echo "Approved Offerings Count: " . Offering::where('approval_status', 'approved')->count() . "\n";
echo "Tithe Total Amount: " . Tithe::where('approval_status', 'approved')->sum('amount') . "\n";
echo "Offering Total Amount: " . Offering::where('approval_status', 'approved')->sum('amount') . "\n";
echo "\nAll Tithes (any status): " . Tithe::count() . "\n";
echo "All Offerings (any status): " . Offering::count() . "\n";
