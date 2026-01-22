<?php
/**
 * Check Storage Symlink Status
 * Run: php check_storage_symlink.php
 */

$basePath = __DIR__;
$storagePath = $basePath . '/storage/app/public';
$publicStoragePath = $basePath . '/public/storage';
$targetPath = $basePath . '/storage/app/public/member/profile-pictures';

echo "=== Storage Symlink Check ===\n\n";

// Check if storage directory exists
echo "1. Storage Directory:\n";
if (is_dir($storagePath)) {
    echo "   ✓ EXISTS: {$storagePath}\n";
} else {
    echo "   ✗ MISSING: {$storagePath}\n";
    echo "   → Create it: mkdir -p storage/app/public\n";
}

// Check if public/storage exists
echo "\n2. Public Storage Link:\n";
if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        $target = readlink($publicStoragePath);
        echo "   ✓ EXISTS as SYMLINK\n";
        echo "   Link: {$publicStoragePath}\n";
        echo "   Target: {$target}\n";
        
        // Check if target is correct
        $expectedTarget = '../storage/app/public';
        if ($target === $expectedTarget || realpath($publicStoragePath) === realpath($storagePath)) {
            echo "   ✓ Target is CORRECT\n";
        } else {
            echo "   ✗ Target is WRONG (should be: {$expectedTarget})\n";
            echo "   → Fix: rm public/storage && php artisan storage:link\n";
        }
    } else {
        echo "   ✗ EXISTS as REGULAR FOLDER (should be symlink)\n";
        echo "   → Fix: rm -rf public/storage && php artisan storage:link\n";
    }
} else {
    echo "   ✗ MISSING\n";
    echo "   → Fix: php artisan storage:link\n";
}

// Check profile pictures directory
echo "\n3. Profile Pictures Directory:\n";
if (is_dir($targetPath)) {
    echo "   ✓ EXISTS: {$targetPath}\n";
    
    // Count files
    $files = glob($targetPath . '/*');
    $fileCount = count($files);
    echo "   Files found: {$fileCount}\n";
    
    if ($fileCount > 0) {
        echo "   Sample files:\n";
        foreach (array_slice($files, 0, 3) as $file) {
            $filename = basename($file);
            echo "     - {$filename}\n";
        }
    }
} else {
    echo "   ✗ MISSING: {$targetPath}\n";
    echo "   → Create it: mkdir -p storage/app/public/member/profile-pictures\n";
}

// Check permissions
echo "\n4. Permissions:\n";
if (is_dir($storagePath)) {
    $perms = substr(sprintf('%o', fileperms($storagePath)), -4);
    echo "   storage/app/public: {$perms}\n";
    if ($perms < '0755') {
        echo "   ⚠ Should be 0755 or higher\n";
        echo "   → Fix: chmod -R 755 storage/app/public\n";
    }
}

if (file_exists($publicStoragePath)) {
    $perms = substr(sprintf('%o', fileperms($publicStoragePath)), -4);
    echo "   public/storage: {$perms}\n";
}

// Test URL generation
echo "\n5. URL Test:\n";
$testPath = 'member/profile-pictures/test.jpg';
$generatedUrl = 'storage/' . $testPath;
echo "   Database path: {$testPath}\n";
echo "   Generated URL: {$generatedUrl}\n";
echo "   Full URL: https://www.wauminilink.co.tz/demo/{$generatedUrl}\n";

// Recommendations
echo "\n=== Recommendations ===\n";

$issues = [];

if (!file_exists($publicStoragePath)) {
    $issues[] = "Create symlink: php artisan storage:link";
}

if (file_exists($publicStoragePath) && !is_link($publicStoragePath)) {
    $issues[] = "Remove regular folder and create symlink: rm -rf public/storage && php artisan storage:link";
}

if (file_exists($publicStoragePath) && is_link($publicStoragePath)) {
    $target = readlink($publicStoragePath);
    if ($target !== '../storage/app/public' && realpath($publicStoragePath) !== realpath($storagePath)) {
        $issues[] = "Fix symlink target: rm public/storage && php artisan storage:link";
    }
}

if (!is_dir($targetPath)) {
    $issues[] = "Create directory: mkdir -p storage/app/public/member/profile-pictures";
}

if (empty($issues)) {
    echo "✓ Everything looks good!\n";
    echo "\nIf images still don't display, check:\n";
    echo "  1. Database paths (should be 'member/profile-pictures/filename.jpg')\n";
    echo "  2. Web server configuration\n";
    echo "  3. Browser console for 404 errors\n";
} else {
    echo "Issues found:\n";
    foreach ($issues as $issue) {
        echo "  - {$issue}\n";
    }
}

echo "\n";

