<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Authentication routes
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['register'] = 'auth/register';

// Dashboard
$route['dashboard'] = 'dashboard/index';

// Booking routes
$route['bookings'] = 'bookings/index';
$route['bookings/create'] = 'bookings/create';
$route['bookings/view/(:num)'] = 'bookings/view/$1';
$route['bookings/edit/(:num)'] = 'bookings/edit/$1';
$route['bookings/delete/(:num)'] = 'bookings/delete/$1';

// Approval routes
$route['approvals'] = 'approvals/index';
$route['approvals/process/(:num)'] = 'approvals/process/$1';

// Vehicle routes
$route['vehicles'] = 'vehicles/index';
$route['vehicles/create'] = 'vehicles/create';
$route['vehicles/view/(:num)'] = 'vehicles/view/$1';
$route['vehicles/edit/(:num)'] = 'vehicles/edit/$1';
$route['vehicles/delete/(:num)'] = 'vehicles/delete/$1';

// Driver routes  
$route['drivers'] = 'drivers/index';
$route['drivers/create'] = 'drivers/create';
$route['drivers/view/(:num)'] = 'drivers/view/$1';
$route['drivers/edit/(:num)'] = 'drivers/edit/$1';
$route['drivers/delete/(:num)'] = 'drivers/delete/$1';

// Reports
$route['reports'] = 'reports/index';
$route['reports/export'] = 'reports/export';

// API routes
$route['api/dashboard/stats'] = 'api/dashboard_stats';
$route['api/dashboard/usage'] = 'api/dashboard_usage';
$route['api/bookings/(:num)'] = 'api/booking_detail/$1';
$route['api/seed-data'] = 'api/seed_data';