<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Set up basic CodeIgniter constants
define('CI_VERSION', '3.1.13');
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);
define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb');
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b');
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
define('EXIT_SUCCESS', 0);
define('EXIT_ERROR', 1);
define('EXIT_CONFIG', 3);
define('EXIT_UNKNOWN_FILE', 4);
define('EXIT_UNKNOWN_CLASS', 5);
define('EXIT_UNKNOWN_METHOD', 6);
define('EXIT_USER_INPUT', 7);
define('EXIT_DATABASE', 8);
define('EXIT__AUTO_MIN', 9);
define('EXIT__AUTO_MAX', 125);

// Simple routing and request handling
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string
$path = strtok($request_uri, '?');
$path = trim($path, '/');

// Simple routing
$routes = [
    'api/auth/login' => 'Auth@login',
    'api/auth/logout' => 'Auth@logout',
    'api/auth/me' => 'Auth@me',
    'api/dashboard/stats' => 'Dashboard@stats',
    'api/dashboard/usage-data' => 'Dashboard@usage_data',
    'api/dashboard/vehicle-status' => 'Dashboard@vehicle_status',
    'api/dashboard/recent-bookings' => 'Dashboard@recent_bookings',
    'api/bookings' => 'Bookings@index',
    'api/bookings/create' => 'Bookings@create',
    'api/approvals/pending' => 'Approvals@pending',
    'api/approvals/process' => 'Approvals@process',
    'api/vehicles' => 'Vehicles@index',
    'api/vehicles/create' => 'Vehicles@create',
    'api/drivers' => 'Drivers@index',
    'api/drivers/create' => 'Drivers@create',
    'api/reports/export' => 'Reports@export',
    'api/activity-logs' => 'Activity@logs',
    'api/seed-data' => 'Seeds@run'
];

// Route matching
$controller = 'Welcome';
$method = 'index';

foreach ($routes as $route => $target) {
    if ($path === $route || preg_match("#^$route$#", $path)) {
        list($controller, $method) = explode('@', $target);
        break;
    }
}

// Load the controller
$controller_file = APPPATH . 'controllers/' . $controller . '.php';
if (file_exists($controller_file)) {
    require_once APPPATH . 'config/database_sqlite.php'; // Use SQLite for testing
    require_once APPPATH . 'core/MY_Controller.php';
    require_once $controller_file;
    
    $controller_class = $controller;
    if (class_exists($controller_class)) {
        $instance = new $controller_class();
        if (method_exists($instance, $method)) {
            $instance->$method();
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Method not found']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Controller not found']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Controller file not found']);
}