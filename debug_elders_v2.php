<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Leader;
use App\Models\User;

echo "--- DEBUG START ---\n";
$elders = Leader::where('position', 'elder')->get();
echo "Total Elders found: " . $elders->count() . "\n";

$membersWithUsers = User::whereNotNull('member_id')
    ->whereIn('role', ['admin', 'pastor', 'secretary', 'treasurer'])
    ->pluck('member_id')
    ->toArray();

$membersWithMemberAccounts = User::whereNotNull('member_id')
    ->where('role', 'member')
    ->pluck('member_id')
    ->toArray();

foreach ($elders as $elder) {
    echo "Elder ID: " . $elder->id . " | Name: " . ($elder->member ? $elder->member->full_name : 'No Member') . "\n";
    echo "  Is Active: " . ($elder->is_active ? 'YES' : 'NO') . "\n";
    echo "  End Date: " . ($elder->end_date ? $elder->end_date->format('Y-m-d') : 'None') . "\n";
    
    $hasAdminRole = in_array($elder->member_id, $membersWithUsers);
    $hasMemberRole = in_array($elder->member_id, $membersWithMemberAccounts);
    
    echo "  Has Admin/Leader Account: " . ($hasAdminRole ? 'YES' : 'NO') . "\n";
    echo "  Has Member Account: " . ($hasMemberRole ? 'YES' : 'NO') . "\n";
    
    if ($elder->is_active && !$hasAdminRole && !$hasMemberRole) {
        echo "  Result: SHOULD APPEAR\n";
    } elseif (!$elder->is_active) {
        echo "  Result: HIDDEN (Not Active)\n";
    } elseif ($hasMemberRole) {
        echo "  Result: HIDDEN (Has Member Account)\n";
    } else {
        echo "  Result: HIDDEN (Other reason)\n";
    }
    echo "-------------------\n";
}
echo "--- DEBUG END ---\n";
