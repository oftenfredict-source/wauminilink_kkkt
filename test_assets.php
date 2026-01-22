<?php
/**
 * Quick test to verify asset URLs are working correctly
 * Access this file directly: http://localhost/WauminiLink/public/test_assets.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Asset URL Test</h2>";
echo "<p><strong>APP_URL:</strong> " . config('app.url') . "</p>";
echo "<p><strong>Asset URL for CSS:</strong> " . asset('css/styles.css') . "</p>";
echo "<p><strong>Asset URL for Bootstrap:</strong> " . asset('assets/css/bootstrap.min.css') . "</p>";

echo "<h3>Test Links (click to verify files exist):</h3>";
echo "<ul>";
echo "<li><a href='" . asset('css/styles.css') . "' target='_blank'>CSS Styles</a></li>";
echo "<li><a href='" . asset('assets/css/bootstrap.min.css') . "' target='_blank'>Bootstrap CSS</a></li>";
echo "<li><a href='" . asset('assets/css/datatables.min.css') . "' target='_blank'>DataTables CSS</a></li>";
echo "</ul>";

echo "<h3>Expected URLs:</h3>";
echo "<ul>";
echo "<li>CSS: http://localhost/WauminiLink/public/css/styles.css</li>";
echo "<li>Bootstrap: http://localhost/WauminiLink/public/assets/css/bootstrap.min.css</li>";
echo "</ul>";

















