<?php

use App\Models\Member;
use App\Models\Offering;
use App\Models\CommunityOffering;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$helenaId = 52; // Helena Shija
$helena = Member::find($helenaId);

if (!$helena) {
    echo "Helena not found.\n";
    exit;
}

echo "Helena Shija (ID: $helenaId) | Community ID: {$helena->community_id}\n";

// 1. Check if she's an Elder
$isElder = $helena->activeLeadershipPositions()
    ->where('position', 'elder')
    ->exists();
echo "Is Elder: " . ($isElder ? "Yes" : "No") . "\n";

// 2. Search Offering table for her ID
echo "\n--- Individual Offerings for ID $helenaId ---\n";
$off = Offering::where('member_id', $helenaId)->get();
foreach ($off as $o) {
    echo "ID: {$o->id} | Amount: {$o->amount} | Type: {$o->offering_type} | Status: {$o->approval_status}\n";
}

// 3. Search CommunityOffering table for Helena
echo "\n--- Community Offerings (Elder Collections) for Member $helenaId ---\n";
$comm = CommunityOffering::where('church_elder_id', $helenaId)->get();
foreach ($comm as $c) {
    echo "ID: {$c->id} | Total: {$c->amount} | Jengo: {$c->amount_jengo} | Umoja: {$c->amount_umoja} | Date: " . ($c->offering_date ?: 'N/A') . "\n";
}

// 4. Search for records with specific amounts 20000/40000 anywhere
echo "\n--- Global Search for 20000 / 40000 ---\n";
$off20k = Offering::where('amount', 20000)->get();
foreach ($off20k as $o) {
    echo "Offering ID {$o->id} | Amount 20000 | Member: " . ($o->member ? $o->member->full_name : 'N/A') . " | ID: {$o->member_id}\n";
}

$comm20k = CommunityOffering::where('amount_jengo', 20000)->orWhere('amount_umoja', 20000)->orWhere('amount', 20000)->get();
foreach ($comm20k as $c) {
    echo "CommunityOffering ID {$c->id} | Jengo: {$c->amount_jengo} | Umoja: {$c->amount_umoja} | Total: {$c->amount} | Elder: " . ($c->churchElder ? $c->churchElder->name : 'N/A') . " (Member ID: {$c->church_elder_id})\n";
}

$off40k = Offering::where('amount', 40000)->get();
foreach ($off40k as $o) {
    echo "Offering ID {$o->id} | Amount 40000 | Member: " . ($o->member ? $o->member->full_name : 'N/A') . " | ID: {$o->member_id}\n";
}

$comm40k = CommunityOffering::where('amount_jengo', 40000)->orWhere('amount_umoja', 40000)->orWhere('amount', 40000)->get();
foreach ($comm40k as $c) {
    echo "CommunityOffering ID {$c->id} | Jengo: {$c->amount_jengo} | Umoja: {$c->amount_umoja} | Total: {$c->amount} | Elder: " . ($c->churchElder ? $c->churchElder->name : 'N/A') . " (Member ID: {$c->church_elder_id})\n";
}
