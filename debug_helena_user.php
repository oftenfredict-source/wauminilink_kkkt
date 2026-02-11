<?php

use App\Models\Member;
use App\Models\User;
use App\Models\CommunityOffering;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- User Search for 'Helena' ---\n";
$users = User::where('name', 'LIKE', '%Helena%')->get();
foreach ($users as $u) {
    echo "User: {$u->name} | ID: {$u->id} | Email: {$u->email}\n";

    // Try to find matching member
    $m = Member::where('full_name', $u->name)->first();
    if ($m) {
        echo "  > Matches Member: {$m->full_name} | ID: {$m->id}\n";
    }
}

echo "\n--- Member 52 Check ---\n";
$m52 = Member::find(52);
if ($m52) {
    echo "Member 52: {$m52->full_name}\n";
    // Many systems link by email or a specific user_id column
    // Let's check for a user_id column in members table
    // I'll check the schema or common columns
}

echo "\n--- Specific Collection Check for User 59 ---\n";
$collections = CommunityOffering::where('church_elder_id', 59)->get();
foreach ($collections as $c) {
    echo "Coll ID {$c->id} | Total {$c->amount} | Jengo {$c->amount_jengo} | Umoja {$c->amount_umoja} | Notes: {$c->notes}\n";
}
