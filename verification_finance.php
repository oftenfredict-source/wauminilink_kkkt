<?php

use App\Models\Offering;
use App\Models\CommunityOffering;
use App\Models\Member;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate Member Dashboard Logic for Member ID 6 (or whoever the user is)
// Assuming User 1 is Member 59 based on previous debug
$memberId = 59;
$churchElderId = 59;

echo "--- Simulating Finance Totals for ID $memberId ---\n";

$individualJengo = Offering::where('member_id', $memberId)
    ->where('approval_status', 'approved')
    ->whereIn('offering_type', ['Sadaka ya Jengo', 'sadaka_jengo'])
    ->sum('amount');

$communityJengo = CommunityOffering::where('church_elder_id', $churchElderId)
    ->sum('amount_jengo');

echo "Jengo: Individual ($individualJengo) + Community ($communityJengo) = " . ($individualJengo + $communityJengo) . "\n";

$individualUmoja = Offering::where('member_id', $memberId)
    ->where('approval_status', 'approved')
    ->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja'])
    ->sum('amount');

$communityUmoja = CommunityOffering::where('church_elder_id', $churchElderId)
    ->sum('amount_umoja');

echo "Umoja: Individual ($individualUmoja) + Community ($communityUmoja) = " . ($individualUmoja + $communityUmoja) . "\n";
