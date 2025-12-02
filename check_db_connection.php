<?php
/**
 * Database Connection Diagnostic Script
 * Run this from command line: php check_db_connection.php
 */

echo "=== Database Connection Diagnostic ===\n\n";

// Check if .env file exists
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "✓ .env file exists\n";
    $envContents = file_get_contents($envPath);
    
    // Extract DB settings
    preg_match('/DB_CONNECTION=(.+)/', $envContents, $connectionMatch);
    preg_match('/DB_HOST=(.+)/', $envContents, $hostMatch);
    preg_match('/DB_PORT=(.+)/', $envContents, $portMatch);
    preg_match('/DB_DATABASE=(.+)/', $envContents, $databaseMatch);
    preg_match('/DB_USERNAME=(.+)/', $envContents, $usernameMatch);
    
    $connection = isset($connectionMatch[1]) ? trim($connectionMatch[1]) : 'not set';
    $host = isset($hostMatch[1]) ? trim($hostMatch[1]) : '127.0.0.1';
    $port = isset($portMatch[1]) ? trim($portMatch[1]) : '3306';
    $database = isset($databaseMatch[1]) ? trim($databaseMatch[1]) : 'not set';
    $username = isset($usernameMatch[1]) ? trim($usernameMatch[1]) : 'root';
    
    echo "  DB_CONNECTION: {$connection}\n";
    echo "  DB_HOST: {$host}\n";
    echo "  DB_PORT: {$port}\n";
    echo "  DB_DATABASE: {$database}\n";
    echo "  DB_USERNAME: {$username}\n\n";
} else {
    echo "✗ .env file NOT found!\n";
    echo "  You need to create a .env file with your database configuration.\n\n";
}

// Test MySQL connection
echo "Testing MySQL connection...\n";
$host = isset($host) ? $host : '127.0.0.1';
$port = isset($port) ? $port : '3306';
$username = isset($username) ? $username : 'root';
$password = ''; // Default XAMPP password

try {
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "✓ Successfully connected to MySQL server!\n";
    echo "  MySQL Version: " . $pdo->query('SELECT VERSION()')->fetchColumn() . "\n";
    
    // Check if database exists
    if (isset($database) && $database !== 'not set') {
        $stmt = $pdo->query("SHOW DATABASES LIKE '{$database}'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Database '{$database}' exists\n";
        } else {
            echo "✗ Database '{$database}' does NOT exist\n";
            echo "  You may need to create it manually or run migrations.\n";
        }
    }
    
} catch (PDOException $e) {
    echo "✗ Failed to connect to MySQL!\n";
    echo "  Error: " . $e->getMessage() . "\n\n";
    echo "=== Troubleshooting Steps ===\n";
    echo "1. Open XAMPP Control Panel\n";
    echo "2. Make sure MySQL service is running (click 'Start' if it's stopped)\n";
    echo "3. Check if MySQL is running on port {$port}\n";
    echo "4. Verify your .env file has correct database credentials\n";
    echo "5. If using a different port, update DB_PORT in .env\n";
}

echo "\n=== End of Diagnostic ===\n";

