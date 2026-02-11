<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CommunityOfferingItem;
use App\Models\Member;

$ids = [41, 52];
foreach ($ids as $id) {
    $m = Member::find($id);
    echo "\nMember: " . ($m ? $m->full_name : "UNKNOWN") . " (ID: $id)\n";
    $items = CommunityOfferingItem::where('member_id', $id)->with('offering')->get();
    foreach ($items as $item) {
        $o = $item->offering;
        echo "- Item ID: {$item->id}, Amt: {$item->amount}\n";
        echo "  Offering ID: " . ($o ? $o->id : "N/A") . ", Status: " . ($o ? $o->status : "N/A") . ", Type: " . ($o ? $o->offering_type : "N/A") . ", Date: " . ($o ? $o->offering_date : "N/A") . "\n";
    }
}

echo "\n--- ALL COMMUNITY OFFERINGS TODAY ---\n";
$today = \Carbon\Carbon::now()->toDateString();
$allToday = App\Models\CommunityOffering::whereDate('offering_date', $today)->with('items.member')->get();
foreach ($allToday as $ot) {
    echo "Offering ID: {$ot->id}, Status: {$ot->status}, Total: {$ot->amount}, Type: {$ot->offering_type}\n";
    foreach ($ot->items as $item) {
        echo "  - Item: {$item->id}, Amt: {$item->amount}, Member: " . ($item->member ? $item->member->full_name : "NULL") . " (Env: {$item->envelope_number})\n";
    }
}
