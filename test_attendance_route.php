<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test if route exists
try {
    $route = route('attendance.statistics');
    echo "✓ Route exists: " . $route . "\n";
} catch (Exception $e) {
    echo "✗ Route error: " . $e->getMessage() . "\n";
}

// Test route list
echo "\nChecking route registration...\n";
$routes = Artisan::call('route:list', ['--name' => 'attendance.statistics']);
$output = Artisan::output();
echo $output;

// Try to make a request
echo "\n\nTesting HTTP request...\n";
$request = Illuminate\Http\Request::create('/attendance/statistics', 'GET');
$response = $kernel->handle($request);
echo "Response status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() === 404) {
    echo "✗ Route returns 404 - Route not found!\n";
} else {
    echo "✓ Route is accessible (Status: " . $response->getStatusCode() . ")\n";
}






