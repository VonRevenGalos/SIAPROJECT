<?php
// Test Hostinger database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Hostinger database connection...\n";

// Try different connection methods for Hostinger
$connections = [
    // Method 1: Current settings
    [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'u585057361_shoe',
        'username' => 'u585057361_rizz',
        'password' => 'Astron_202'
    ],
    // Method 2: localhost instead of 127.0.0.1
    [
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'u585057361_shoe',
        'username' => 'u585057361_rizz',
        'password' => 'Astron_202'
    ],
    // Method 3: Hostinger typical format
    [
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'u585057361_shoe',
        'username' => 'u585057361_rizz',
        'password' => 'Astron_202'
    ]
];

foreach ($connections as $i => $config) {
    echo "\n--- Testing Method " . ($i + 1) . " ---\n";
    echo "Host: {$config['host']}\n";
    echo "Port: {$config['port']}\n";
    echo "Database: {$config['dbname']}\n";
    echo "Username: {$config['username']}\n";
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ Connection successful!\n";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Products table accessible. Total products: " . $result['count'] . "\n";
        
        // If we get here, this connection works
        echo "🎉 This connection method works! Use these settings.\n";
        break;
        
    } catch (PDOException $e) {
        echo "❌ Connection failed: " . $e->getMessage() . "\n";
    }
}
?>
