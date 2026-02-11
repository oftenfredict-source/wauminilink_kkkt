<?php

use App\Models\Member;
use App\Models\Offering;
use App\Models\CommunityOffering;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$helenaMemberId = 52;
$helenaUserId = 59;

$member = Member::find($helenaMemberId);

echo "Simulating Dashboard for Helena Shija (Member $helenaMemberId, User $helenaUserId)\n";

// Individual
$individualJengo = Offering::where('member_id', $helenaMemberId)
    ->whereIn('offering_type', ['Sadaka ya Jengo', 'sadaka_jengo'])
    ->sum('amount');

// Community (Fixed logic uses User ID now)
$communityJengo = CommunityOffering::where('church_elder_id', $helenaUserId)
    ->sum('amount_jengo');

$totalJengo = $individualJengo + $communityJengo;

echo "Individual Jengo: $individualJengo\n";
echo "Community Jengo: $communityJengo\n";
echo "Total Jengo: $totalJengo\n";

// Individual Umoja
$individualUmoja = Offering::where('member_id', $helenaMemberId)
    ->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja'])
    ->sum('amount');

// Community
$communityUmoja = CommunityOffering::where('church_elder_id', $helenaUserId)
    ->sum('amount_umoja');

$totalUmoja = $individualUmoja + $communityUmoja;

echo "Individual Umoja: $individualUmoja\n";
echo "Community Umoja: $communityUmoja\n";
echo "Total Umoja: $totalUmoja\n";
