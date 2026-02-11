<?php
/**
 * Quick test to verify asset URLs are working correctly
 * Access this file directly: http://localhost/WauminiLink/public/test_assets.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Asset URL Test</h2>";
echo "<p><strong>APP_ENV:</strong> " . env('APP_ENV') . "</p>";
echo "<p><strong>APP_URL (from config):</strong> " . config('app.url') . "</p>";
echo "<p><strong>Current Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Detected Subdirectory:</strong> " . (isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : 'N/A') . "</p>";
echo "<p><strong>Asset URL for CSS:</strong> " . asset('css/styles.css') . "</p>";
echo "<p><strong>Asset URL for Bootstrap:</strong> " . asset('assets/css/bootstrap.min.css') . "</p>";

echo "<h3>Test Links (click to verify files exist):</h3>";
echo "<ul>";
echo "<li><a href='" . asset('css/styles.css') . "' target='_blank'>CSS Styles</a></li>";
echo "<li><a href='" . asset('assets/css/bootstrap.min.css') . "' target='_blank'>Bootstrap CSS</a></li>";
echo "<li><a href='" . asset('assets/images/waumini_link_logo.png') . "' target='_blank'>Logo Image</a></li>";
echo "</ul>";

echo "<h3>Debugging Tips:</h3>";
echo "<ul>";
echo "<li>If the links above don't include your subdirectory (e.g. /demo), update <code>APP_URL</code> in <code>.env</code>.</li>";
echo "<li>If you get a 404, check if the files exist in <code>public/css/</code> and <code>public/assets/</code>.</li>";
echo "<li>If you see <code>http://</code> but your site is <code>https://</code>, ensure <code>APP_URL</code> starts with <code>https://</code>.</li>";
echo "</ul>";

















