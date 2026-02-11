<?php

use App\Models\CommunityOffering;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Checking CommunityOfferings for null dates ===\n\n";

$offerings = CommunityOffering::all();
echo "Total offerings: " . $offerings->count() . "\n\n";

foreach ($offerings as $offering) {
    echo "ID: {$offering->id} (Status: {$offering->status})\n";
    echo "  offering_date: " . ($offering->offering_date ? "OK ({$offering->offering_date})" : "NULL (FATAL ERROR)") . "\n";
    echo "  created_at: " . ($offering->created_at ? "OK ({$offering->created_at})" : "NULL (FATAL ERROR)") . "\n";
    echo "  handover_to_evangelism_at: " . ($offering->handover_to_evangelism_at ? "OK ({$offering->handover_to_evangelism_at})" : "NULL (MAYBE BREAKS VIEW)") . "\n";
    echo "-------------------\n";
}
