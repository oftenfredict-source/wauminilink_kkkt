<?php

use App\Models\CommunityOffering;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== ALL COMMUNITY OFFERINGS ===\n\n";

$offerings = CommunityOffering::withTrashed()->get();
foreach ($offerings as $o) {
    echo "ID: {$o->id} | CommID: {$o->community_id} | Amount: {$o->amount} | Status: {$o->status} | Deleted: " . ($o->deleted_at ?? 'NO') . "\n";
}
