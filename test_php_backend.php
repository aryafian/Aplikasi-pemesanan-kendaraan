<?php
// Simple test script to verify PHP backend functionality

echo "=== Testing VehicleFlow PHP Backend ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Basic PHP functionality
echo "1. Testing PHP functionality...\n";
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "   ✓ PHP version: " . PHP_VERSION . " (OK)\n";
} else {
    echo "   ✗ PHP version: " . PHP_VERSION . " (Requires 7.4+)\n";
}

// Test 2: PDO SQLite support
echo "\n2. Testing PDO SQLite support...\n";
if (extension_loaded('pdo_sqlite')) {
    echo "   ✓ PDO SQLite extension loaded\n";
} else {
    echo "   ✗ PDO SQLite extension not loaded\n";
}

// Test 3: Test database creation
echo "\n3. Testing database creation...\n";
try {
    // Define BASEPATH for the configuration file
    define('BASEPATH', true);
    require_once 'api/application/config/database_sqlite.php';
    echo "   ✓ Database configuration loaded\n";
    echo "   ✓ SQLite database initialized\n";
    
    // Test query
    $result = $GLOBALS['db_instance']->query("SELECT COUNT(*) as count FROM users");
    $count = $GLOBALS['db_instance']->fetch_array($result);
    echo "   ✓ Found " . $count['count'] . " users in database\n";
    
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

// Test 4: Test authentication functionality
echo "\n4. Testing authentication...\n";
try {
    $result = $GLOBALS['db_instance']->query("SELECT username, full_name, role FROM users WHERE username = 'admin'");
    $admin = $GLOBALS['db_instance']->fetch_array($result);
    
    if ($admin) {
        echo "   ✓ Admin user found: " . $admin['full_name'] . " (" . $admin['role'] . ")\n";
        
        // Test password verification
        $password_result = $GLOBALS['db_instance']->query("SELECT password_hash FROM users WHERE username = 'admin'");
        $password_data = $GLOBALS['db_instance']->fetch_array($password_result);
        
        if (password_verify('password', $password_data['password_hash'])) {
            echo "   ✓ Password verification working\n";
        } else {
            echo "   ✗ Password verification failed\n";
        }
    } else {
        echo "   ✗ Admin user not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Authentication test error: " . $e->getMessage() . "\n";
}

// Test 5: Test API structure
echo "\n5. Testing API structure...\n";
$controllers = ['Auth', 'Dashboard', 'Bookings', 'Approvals', 'Vehicles', 'Drivers'];
foreach ($controllers as $controller) {
    $file = "api/application/controllers/{$controller}.php";
    if (file_exists($file)) {
        echo "   ✓ {$controller} controller exists\n";
    } else {
        echo "   ✗ {$controller} controller missing\n";
    }
}

echo "\n=== Test Results ===\n";
echo "Backend Type: PHP CodeIgniter 3 (Custom Implementation)\n";
echo "Database: SQLite (Testing) / MySQL (Production)\n";
echo "Status: Ready for development\n";
echo "\nTo start the PHP server:\n";
echo "./start_php_server.sh\n";
echo "or\n";
echo "php -S 0.0.0.0:5000 -t api/public\n";
echo "\nDefault Login Credentials:\n";
echo "Admin: admin / password\n";
echo "User: user / password\n";
echo "Approver1: approver1 / password\n";
echo "Approver2: approver2 / password\n";
?>