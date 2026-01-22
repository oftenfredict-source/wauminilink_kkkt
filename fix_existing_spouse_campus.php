<?php
/**
 * Script to fix existing spouse members that don't have campus_id
 * Run: php fix_existing_spouse_campus.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;

echo "=== Fixing Spouse Members Without Campus ID ===\n\n";

// Find all members that have a spouse_member_id (they are the main member)
$mainMembers = Member::whereNotNull('spouse_member_id')->get();

$fixed = 0;
$alreadyFixed = 0;
$notFound = 0;

foreach ($mainMembers as $mainMember) {
    $spouseMember = Member::find($mainMember->spouse_member_id);
    
    if (!$spouseMember) {
        $notFound++;
        echo "⚠️  Spouse member not found for main member: {$mainMember->full_name} (ID: {$mainMember->id})\n";
        continue;
    }
    
    // Check if spouse already has campus_id
    if ($spouseMember->campus_id) {
        $alreadyFixed++;
        continue;
    }
    
    // If main member has campus_id, assign it to spouse
    if ($mainMember->campus_id) {
        $spouseMember->campus_id = $mainMember->campus_id;
        $spouseMember->save();
        
        // Also update spouse user account if exists
        if ($spouseMember->user) {
            $spouseMember->user->campus_id = $mainMember->campus_id;
            $spouseMember->user->save();
        }
        
        $fixed++;
        echo "✓ Fixed: {$spouseMember->full_name} → Campus ID: {$mainMember->campus_id}\n";
    } else {
        echo "⚠️  Main member {$mainMember->full_name} also doesn't have campus_id\n";
    }
}

echo "\n=== Summary ===\n";
echo "Fixed: {$fixed} spouse members\n";
echo "Already had campus_id: {$alreadyFixed}\n";
echo "Spouse not found: {$notFound}\n";
echo "\n✅ Done!\n";














