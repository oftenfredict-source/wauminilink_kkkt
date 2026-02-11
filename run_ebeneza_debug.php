<?php

use App\Models\Community;
use App\Models\CommunityOffering;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$output = "=== Comprehensive Ebeneza Debug ===\n\n";

$ebeneza = Community::where('name', 'LIKE', '%Ebeneza%')->first();
if ($ebeneza) {
    $output .= "Community: ID: {$ebeneza->id}, Name: {$ebeneza->name}, Campus ID: {$ebeneza->campus_id}\n";
    
    $offerings = CommunityOffering::withTrashed()->where('community_id', $ebeneza->id)->get();
    $output .= "Offerings count (withTrashed): " . $offerings->count() . "\n";
    foreach ($offerings as $o) {
        $output .= "- ID: {$o->id}, Amount: {$o->amount}, Status: {$o->status}, Deleted: " . ($o->deleted_at ?? 'NO') . "\n";
    }
} else {
    $output .= "Ebeneza community NOT FOUND.\n";
}

file_put_contents('ebeneza_debug.txt', $output);
echo "Debug info written to ebeneza_debug.txt\n";
