<?php
/**
 * Complete Diagnostic for Profile Picture Display Issue
 * Run: php diagnose_image_display.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use Illuminate\Support\Facades\Storage;

echo "=== PROFILE PICTURE DISPLAY DIAGNOSTIC ===\n\n";

// 1. Check database paths
echo "1. DATABASE PATHS:\n";
echo str_repeat("-", 60) . "\n";
$members = Member::whereNotNull('profile_picture')->take(5)->get();

if ($members->isEmpty()) {
    echo "❌ No members with profile pictures found in database!\n\n";
    exit;
}

foreach ($members as $member) {
    echo "Member ID: {$member->id}\n";
    echo "Name: {$member->full_name}\n";
    echo "Database Path: {$member->profile_picture}\n";
    
    // Check path format
    $issues = [];
    if (strpos($member->profile_picture, 'storage/') === 0) {
        $issues[] = "❌ Has 'storage/' prefix (should be removed)";
    }
    if (strpos($member->profile_picture, 'public/') === 0) {
        $issues[] = "❌ Has 'public/' prefix (should be removed)";
    }
    if (strpos($member->profile_picture, 'members/profile-pictures/') === 0) {
        $issues[] = "❌ Uses plural 'members/' (should be 'member/')";
    }
    if (strpos($member->profile_picture, 'member/profile-pictures/') !== 0) {
        $issues[] = "❌ Path format is wrong (should start with 'member/profile-pictures/')";
    }
    
    if (empty($issues)) {
        echo "✓ Path format is CORRECT\n";
    } else {
        foreach ($issues as $issue) {
            echo "  {$issue}\n";
        }
    }
    
    // Generate URL
    $generatedUrl = asset('storage/' . $member->profile_picture);
    echo "Generated URL: {$generatedUrl}\n";
    
    // Check if file exists in storage
    $storageExists = Storage::disk('public')->exists($member->profile_picture);
    echo "File in Storage: " . ($storageExists ? "✓ EXISTS" : "❌ MISSING") . "\n";
    
    // Check if file exists via public path
    $publicPath = public_path('storage/' . $member->profile_picture);
    $publicExists = file_exists($publicPath);
    echo "File in Public: " . ($publicExists ? "✓ EXISTS" : "❌ MISSING") . "\n";
    echo "Public Path: {$publicPath}\n";
    
    // Check symlink
    $symlinkPath = public_path('storage');
    if (is_link($symlinkPath)) {
        $target = readlink($symlinkPath);
        echo "Symlink: ✓ EXISTS → {$target}\n";
    } elseif (is_dir($symlinkPath)) {
        echo "Symlink: ❌ EXISTS as REGULAR FOLDER (should be symlink)\n";
    } else {
        echo "Symlink: ❌ MISSING\n";
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

// 2. Check symlink status
echo "2. SYMLINK STATUS:\n";
echo str_repeat("-", 60) . "\n";
$symlinkPath = public_path('storage');
$storagePath = storage_path('app/public');

if (is_link($symlinkPath)) {
    $target = readlink($symlinkPath);
    echo "✓ Symlink exists: {$symlinkPath}\n";
    echo "  Target: {$target}\n";
    
    // Check if target is correct
    $expectedTarget = '../storage/app/public';
    $actualTarget = realpath($symlinkPath);
    $expectedRealPath = realpath($storagePath);
    
    if ($actualTarget === $expectedRealPath) {
        echo "✓ Symlink target is CORRECT\n";
    } else {
        echo "❌ Symlink target is WRONG\n";
        echo "  Expected: {$expectedRealPath}\n";
        echo "  Actual: {$actualTarget}\n";
    }
} elseif (is_dir($symlinkPath)) {
    echo "❌ public/storage is a REGULAR FOLDER (should be symlink)\n";
    echo "  Fix: rm -rf public/storage && php artisan storage:link\n";
} else {
    echo "❌ Symlink MISSING\n";
    echo "  Fix: php artisan storage:link\n";
}

// 3. Check file locations
echo "\n3. FILE LOCATIONS:\n";
echo str_repeat("-", 60) . "\n";
$profilePicsPath = storage_path('app/public/member/profile-pictures');
if (is_dir($profilePicsPath)) {
    $files = glob($profilePicsPath . '/*');
    echo "✓ Directory exists: {$profilePicsPath}\n";
    echo "  Files found: " . count($files) . "\n";
    
    if (count($files) > 0) {
        echo "  Sample files:\n";
        foreach (array_slice($files, 0, 3) as $file) {
            $filename = basename($file);
            $size = filesize($file);
            echo "    - {$filename} (" . round($size/1024, 2) . " KB)\n";
        }
    }
} else {
    echo "❌ Directory MISSING: {$profilePicsPath}\n";
    echo "  Fix: mkdir -p storage/app/public/member/profile-pictures\n";
}

// 4. Test URL generation
echo "\n4. URL GENERATION TEST:\n";
echo str_repeat("-", 60) . "\n";
$testMember = $members->first();
if ($testMember) {
    $dbPath = $testMember->profile_picture;
    $generatedUrl = asset('storage/' . $dbPath);
    
    echo "Database Path: {$dbPath}\n";
    echo "Generated URL: {$generatedUrl}\n";
    
    // Extract filename
    $filename = basename($dbPath);
    echo "Filename: {$filename}\n";
    
    // Expected URL format
    $baseUrl = config('app.url', 'http://localhost');
    $expectedUrl = rtrim($baseUrl, '/') . '/storage/member/profile-pictures/' . $filename;
    echo "Expected URL: {$expectedUrl}\n";
    
    if ($generatedUrl === $expectedUrl) {
        echo "✓ URL generation is CORRECT\n";
    } else {
        echo "❌ URL generation might be WRONG\n";
    }
}

// 5. Recommendations
echo "\n5. RECOMMENDATIONS:\n";
echo str_repeat("-", 60) . "\n";

$fixes = [];

// Check database paths
foreach ($members as $member) {
    $path = $member->profile_picture;
    
    if (strpos($path, 'storage/') === 0) {
        $fixes[] = "Remove 'storage/' prefix from database paths";
        break;
    }
    if (strpos($path, 'members/profile-pictures/') === 0) {
        $fixes[] = "Fix plural 'members/' to singular 'member/' in database";
        break;
    }
    if (strpos($path, 'member/profile-pictures/') !== 0) {
        $fixes[] = "Fix database paths to start with 'member/profile-pictures/'";
        break;
    }
}

// Check symlink
if (!is_link($symlinkPath)) {
    if (is_dir($symlinkPath)) {
        $fixes[] = "Remove regular folder and create symlink: rm -rf public/storage && php artisan storage:link";
    } else {
        $fixes[] = "Create symlink: php artisan storage:link";
    }
}

// Check file existence
$testMember = $members->first();
if ($testMember) {
    if (!Storage::disk('public')->exists($testMember->profile_picture)) {
        $fixes[] = "Files are missing in storage/app/public/ - check file upload process";
    }
}

if (empty($fixes)) {
    echo "✓ No obvious issues found!\n";
    echo "\nIf images still don't display, check:\n";
    echo "  1. Web server configuration (.htaccess, nginx config)\n";
    echo "  2. Browser console for errors (F12 → Console)\n";
    echo "  3. Network tab to see actual HTTP response codes\n";
    echo "  4. File permissions: chmod -R 755 storage/app/public public/storage\n";
} else {
    echo "Issues to fix:\n";
    foreach ($fixes as $i => $fix) {
        echo "  " . ($i + 1) . ". {$fix}\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";

