<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CommunityOffering;
use App\Models\CommunityOfferingItem;
use App\Models\Member;

$outputFile = 'debug_output.txt';
$out = "";

$names = ['Ally Ally', 'Helena Shija'];
foreach ($names as $name) {
    $member = Member::where('full_name', 'LIKE', "%$name%")->first();
    if (!$member) {
        $out .= "Member NOT FOUND: $name\n";
        continue;
    }
    
    $out .= "\n--- Member: {$member->full_name} (ID: {$member->id}) ---\n";
    
    // Check CommunityOfferingItem
    $items = CommunityOfferingItem::where('member_id', $member->id)->with('offering')->get();
    if ($items->isEmpty()) {
        $out .= "No community offering items found.\n";
    } else {
        foreach ($items as $item) {
            $offering = $item->offering;
            $out .= "Item ID: {$item->id}, Amount: {$item->amount}, Offering ID: " . ($offering ? $offering->id : 'N/A') . "\n";
            if ($offering) {
                $out .= "  Offering Status: {$offering->status}, Type: {$offering->offering_type}, Date: {$offering->offering_date}\n";
            }
        }
    }
    
    // Check general Offering
    $offerings = App\Models\Offering::where('member_id', $member->id)->get();
    if ($offerings->isEmpty()) {
        $out .= "No general offerings found.\n";
    } else {
        foreach ($offerings as $o) {
            $out .= "General Offering ID: {$o->id}, Amount: {$o->amount}, Type: {$o->offering_type}, Status: {$o->approval_status}, Date: {$o->offering_date}\n";
        }
    }
}

file_put_contents($outputFile, $out);
echo "Output written to $outputFile\n";
