<?php
/**
 * Database Connection Test Script
 * 
 * This file tests database connectivity with your MariaDB/MySQL database
 * Place this file in your Laravel project root and run: php db_test.php
 */

echo "=== Database Connection Test ===\n\n";

// Database configuration (from your .env)
$config = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'dev_tms',
    'username' => 'tms3',
    'password' => 'Nd357h#g6',
    'charset' => 'utf8mb4'
];

echo "Testing connection with the following settings:\n";
echo "Host: " . $config['host'] . "\n";
echo "Port: " . $config['port'] . "\n";
echo "Database: " . $config['database'] . "\n";
echo "Username: " . $config['username'] . "\n";
echo "Password: " . str_repeat('*', strlen($config['password'])) . "\n\n";

// Test 1: Basic PDO Connection
echo "=== Test 1: Basic PDO Connection ===\n";
try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ PDO Connection successful!\n";
    echo "Server Info: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . "\n";
    echo "Server Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
    
} catch (PDOException $e) {
    echo "❌ PDO Connection failed: " . $e->getMessage() . "\n\n";
    $pdo = null;
}

// Test 2: Connection with localhost instead of 127.0.0.1
echo "=== Test 2: Connection with localhost ===\n";
try {
    $dsn_localhost = "mysql:host=localhost;port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo_localhost = new PDO($dsn_localhost, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ localhost Connection successful!\n\n";
    $pdo_localhost = null;
    
} catch (PDOException $e) {
    echo "❌ localhost Connection failed: " . $e->getMessage() . "\n\n";
}

// Test 3: Connection without specifying port
echo "=== Test 3: Connection without port ===\n";
try {
    $dsn_no_port = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
    $pdo_no_port = new PDO($dsn_no_port, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ Connection without port successful!\n\n";
    $pdo_no_port = null;
    
} catch (PDOException $e) {
    echo "❌ Connection without port failed: " . $e->getMessage() . "\n\n";
}

// Test 4: MySQL Extension test
echo "=== Test 4: MySQL Extension Check ===\n";
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL extension is loaded\n";
} else {
    echo "❌ PDO MySQL extension is NOT loaded\n";
}

if (extension_loaded('mysqli')) {
    echo "✅ MySQLi extension is loaded\n";
} else {
    echo "❌ MySQLi extension is NOT loaded\n";
}
echo "\n";

// Test 5: Simple query test (if PDO connection was successful)
if (isset($pdo) && $pdo !== null) {
    echo "=== Test 5: Simple Query Test ===\n";
    try {
        // Test basic query
        $stmt = $pdo->query("SELECT 1 as test_connection, NOW() as current_time");
        $result = $stmt->fetch();
        
        echo "✅ Query test successful!\n";
        echo "Test connection value: " . $result['test_connection'] . "\n";
        echo "Current time: " . $result['current_time'] . "\n\n";
        
        // Test if users table exists
        echo "=== Test 6: Users Table Check ===\n";
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            echo "✅ 'users' table exists\n";
            
            // Count users
            $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM users");
            $result = $stmt->fetch();
            echo "Users in table: " . $result['user_count'] . "\n\n";
        } else {
            echo "❌ 'users' table does not exist\n\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ Query test failed: " . $e->getMessage() . "\n\n";
    }
}

// Test 7: Laravel specific test (if Laravel is available)
echo "=== Test 7: Laravel Connection Test ===\n";
if (file_exists('artisan')) {
    try {
        // Try to load Laravel bootstrap
        require_once 'vendor/autoload.php';
        $app = require_once 'bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
        // Test Laravel database connection
        $connection = DB::connection();
        $pdo = $connection->getPdo();
        
        echo "✅ Laravel database connection successful!\n";
        echo "Laravel DB driver: " . $connection->getDriverName() . "\n";
        echo "Laravel DB name: " . $connection->getDatabaseName() . "\n\n";
        
        // Test a simple Laravel query
        $result = DB::select('SELECT 1 as laravel_test');
        echo "✅ Laravel query test successful!\n";
        echo "Result: " . json_encode($result) . "\n\n";
        
    } catch (Exception $e) {
        echo "❌ Laravel connection failed: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "ℹ️  Laravel not detected in current directory\n\n";
}

// Summary
echo "=== Connection Test Summary ===\n";
echo "If PDO connection worked but Laravel doesn't:\n";
echo "1. Clear Laravel config cache: php artisan config:clear\n";
echo "2. Check your .env file settings\n";
echo "3. Make sure .env is in your Laravel root directory\n\n";

echo "If no connections worked:\n";
echo "1. Check if MariaDB/MySQL is running: sudo systemctl status mariadb\n";
echo "2. Check if the database exists: SHOW DATABASES;\n";
echo "3. Check user permissions: SHOW GRANTS FOR 'tms3'@'localhost';\n";
echo "4. Try connecting via command line: mariadb -u tms3 -p dev_tms\n\n";

echo "Test completed!\n";
?>