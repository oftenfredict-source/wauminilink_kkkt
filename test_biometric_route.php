<?php
/**
 * Test script to verify the biometric sync route is accessible
 * Run this from command line: php test_biometric_route.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test route registration
echo "Testing route registration...\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$route = $routes->getByName('attendance.biometric.sync');

if ($route) {
    echo "✓ Route 'attendance.biometric.sync' is registered\n";
    echo "  Method: " . implode('|', $route->methods()) . "\n";
    echo "  URI: " . $route->uri() . "\n";
    echo "  Action: " . $route->getActionName() . "\n";
} else {
    echo "✗ Route 'attendance.biometric.sync' NOT found\n";
}

// Test if route is accessible
echo "\nTesting route accessibility...\n";
$request = \Illuminate\Http\Request::create('/attendance/biometric-sync', 'POST');
$response = $kernel->handle($request);

echo "Response Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() === 404) {
    echo "✗ Route returns 404 - Not accessible\n";
} else {
    echo "✓ Route is accessible (status: " . $response->getStatusCode() . ")\n";
}












