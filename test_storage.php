<?php
/**
 * Test Storage Access
 * Place this file in: /home/wauminilink/demo/public/test_storage.php
 * Access: https://www.wauminilink.co.tz/demo/test_storage.php
 */

$baseDir = __DIR__;
$storageDir = $baseDir . '/storage';
$testFile = $storageDir . '/member/profile-pictures/HH62rOrdtYWFBPcIyzTGnlaHcx2k57s3DvMC7KD0.jpg';

echo "<h2>Storage Access Test</h2>";
echo "<pre>";

echo "Base Directory: {$baseDir}\n";
echo "Storage Directory: {$storageDir}\n";
echo "Storage exists: " . (file_exists($storageDir) ? 'YES' : 'NO') . "\n";
echo "Is symlink: " . (is_link($storageDir) ? 'YES' : 'NO') . "\n";

if (is_link($storageDir)) {
    echo "Symlink target: " . readlink($storageDir) . "\n";
    echo "Real path: " . realpath($storageDir) . "\n";
}

echo "\nTest File: {$testFile}\n";
echo "File exists: " . (file_exists($testFile) ? 'YES' : 'NO') . "\n";
echo "Is readable: " . (is_readable($testFile) ? 'YES' : 'NO') . "\n";

if (file_exists($testFile)) {
    $size = filesize($testFile);
    echo "File size: " . round($size/1024, 2) . " KB\n";
    
    // Try to display the image
    echo "\n--- Attempting to display image ---\n";
    header('Content-Type: image/jpeg');
    readfile($testFile);
    exit;
} else {
    echo "\n--- File not found! ---\n";
    echo "Checking directory contents:\n";
    $picsDir = $storageDir . '/member/profile-pictures';
    if (is_dir($picsDir)) {
        $files = scandir($picsDir);
        echo "Files in directory:\n";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "  - {$file}\n";
            }
        }
    } else {
        echo "Directory does not exist: {$picsDir}\n";
    }
}

echo "</pre>";

