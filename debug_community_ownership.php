<?php

use App\Models\CommunityOffering;
use App\Models\Member;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Community Offerings Ownership Check ---\n";
$differ = CommunityOffering::all();

foreach ($differ as $offering) {
    echo "ID: {$offering->id} | Elder ID: {$offering->church_elder_id} | Amount: {$offering->amount}\n";
}

// Check who Member 1 (typical test user) is
$m = Member::find(1);
if ($m) {
    echo "Member 1 ID: {$m->id} | Name: {$m->full_name}\n";
}

// Check User 1
$u = User::find(1);
if ($u) {
    echo "User 1 ID: {$u->id} | Member ID: {$u->member_id}\n";
}
