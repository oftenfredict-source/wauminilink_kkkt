<?php
/**
 * Debug script to check profile picture paths in database
 * Run this from command line: php debug_profile_picture_paths.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use Illuminate\Support\Facades\Storage;

echo "=== Profile Picture Path Debug ===\n\n";

// Get members with profile pictures
$members = Member::whereNotNull('profile_picture')
    ->take(10)
    ->get();

if ($members->isEmpty()) {
    echo "No members with profile pictures found.\n";
    exit;
}

echo "Found " . $members->count() . " members with profile pictures:\n\n";

foreach ($members as $member) {
    echo "Member ID: {$member->id}\n";
    echo "Name: {$member->full_name}\n";
    echo "Database Path: {$member->profile_picture}\n";
    
    // Check what the view would generate
    $viewPath = 'storage/' . $member->profile_picture;
    echo "View URL: {$viewPath}\n";
    
    // Check if file exists in storage
    $storageExists = Storage::disk('public')->exists($member->profile_picture);
    echo "Storage Exists: " . ($storageExists ? 'YES' : 'NO') . "\n";
    
    // Check if file exists in public/storage (via symlink)
    $publicPath = public_path('storage/' . $member->profile_picture);
    $publicExists = file_exists($publicPath);
    echo "Public Path Exists: " . ($publicExists ? 'YES' : 'NO') . "\n";
    echo "Full Public Path: {$publicPath}\n";
    
    // Check if old path exists
    if (strpos($member->profile_picture, 'assets/images/') === 0) {
        $oldPath = public_path($member->profile_picture);
        $oldExists = file_exists($oldPath);
        echo "Old Path Exists: " . ($oldExists ? 'YES' : 'NO') . "\n";
    }
    
    echo "\n" . str_repeat('-', 60) . "\n\n";
}

// Check storage symlink
$symlinkPath = public_path('storage');
$symlinkExists = is_link($symlinkPath) || (is_dir($symlinkPath) && file_exists($symlinkPath));
echo "Storage Symlink Status:\n";
echo "Path: {$symlinkPath}\n";
echo "Exists: " . ($symlinkExists ? 'YES' : 'NO') . "\n";

if (is_link($symlinkPath)) {
    echo "Is Symlink: YES\n";
    echo "Symlink Target: " . readlink($symlinkPath) . "\n";
} else {
    echo "Is Symlink: NO\n";
}

echo "\n=== Recommendations ===\n";

// Check for common issues
$issues = [];

foreach ($members as $member) {
    $storageExists = Storage::disk('public')->exists($member->profile_picture);
    $publicPath = public_path('storage/' . $member->profile_picture);
    $publicExists = file_exists($publicPath);
    
    if (!$storageExists && !$publicExists) {
        $issues[] = "Member {$member->id} ({$member->full_name}): File not found in storage or public";
    }
    
    // Check if path has wrong prefix
    if (strpos($member->profile_picture, 'storage/') === 0) {
        $issues[] = "Member {$member->id}: Path has 'storage/' prefix (should be removed)";
    }
    
    if (strpos($member->profile_picture, 'assets/images/') === 0) {
        $issues[] = "Member {$member->id}: Using old path format (assets/images/)";
    }
}

if (empty($issues)) {
    echo "✓ No issues found!\n";
} else {
    echo "Issues found:\n";
    foreach ($issues as $issue) {
        echo "  - {$issue}\n";
    }
}

