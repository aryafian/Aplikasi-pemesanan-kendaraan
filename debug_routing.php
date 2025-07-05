<?php
// Debug script to test PHP backend routing

echo "=== VehicleFlow PHP Backend Debug ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check if we can access the API structure
echo "1. Testing API file structure...\n";
$files_to_check = [
    'api/public/index.php',
    'api/application/config/bootstrap.php',
    'api/application/controllers/Auth.php',
    'api/application/core/MY_Controller.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file exists\n";
    } else {
        echo "   ✗ $file missing\n";
    }
}

// Test 2: Simulate routing
echo "\n2. Testing routing logic...\n";

// Simulate different paths
$test_paths = [
    '/',
    'api/auth/login',
    'api/auth/me',
    'api/dashboard/stats'
];

foreach ($test_paths as $test_path) {
    echo "   Testing path: '$test_path'\n";
    
    // Copy routing logic from bootstrap
    $path = trim($test_path, '/');
    
    $routes = [
        'api/auth/login' => 'Auth@login',
        'api/auth/logout' => 'Auth@logout',
        'api/auth/me' => 'Auth@me',
        'api/dashboard/stats' => 'Dashboard@stats'
    ];
    
    $controller = 'Welcome';
    $method = 'index';
    
    foreach ($routes as $route => $target) {
        if ($path === $route || preg_match("#^$route$#", $path)) {
            list($controller, $method) = explode('@', $target);
            break;
        }
    }
    
    echo "     → Controller: $controller, Method: $method\n";
}

// Test 3: Test database connection
echo "\n3. Testing database connection...\n";
try {
    define('BASEPATH', true);
    require_once 'api/application/config/database_sqlite.php';
    
    $result = $GLOBALS['db_instance']->query("SELECT COUNT(*) as count FROM users");
    $count = $GLOBALS['db_instance']->fetch_array($result);
    echo "   ✓ Database connected, found " . $count['count'] . " users\n";
    
    // Test login credentials
    $result = $GLOBALS['db_instance']->query("SELECT username, full_name FROM users LIMIT 3");
    echo "   Available users:\n";
    while ($user = $GLOBALS['db_instance']->fetch_array($result)) {
        echo "     - " . $user['username'] . " (" . $user['full_name'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

// Test 4: Test server configuration
echo "\n4. Server Configuration:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Not set' . "\n";
echo "   Current Directory: " . getcwd() . "\n";

echo "\n=== Debugging Complete ===\n";
echo "To test endpoints:\n";
echo "1. Start PHP server: php -S localhost:8080 -t api/public\n";
echo "2. Test endpoints with curl:\n";
echo "   curl -X GET http://localhost:8080/\n";
echo "   curl -X GET http://localhost:8080/api/auth/me\n";
echo "   curl -X POST http://localhost:8080/api/auth/login -H \"Content-Type: application/json\" -d '{\"username\":\"admin\",\"password\":\"password\"}'\n";
?>