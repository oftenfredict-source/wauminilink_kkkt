<?php

use App\Models\Offering;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Offering Dates ===\n";
$offerings = Offering::where('approval_status', 'approved')->get();
foreach($offerings as $offering) {
    echo "ID: {$offering->id}, Amount: {$offering->amount}, Date: {$offering->offering_date}, Year: " . Carbon::parse($offering->offering_date)->year . "\n";
}

echo "\nCurrent Year: " . Carbon::now()->year . "\n";
echo "Default Analytics Year: " . Carbon::now()->year . "\n";
