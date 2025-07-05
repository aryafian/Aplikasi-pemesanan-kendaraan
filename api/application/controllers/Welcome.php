<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {
    
    public function index() {
        $this->json_response([
            'message' => 'VehicleFlow API - PHP CodeIgniter 3',
            'version' => '1.0.0',
            'endpoints' => [
                'auth' => [
                    'POST /api/auth/login',
                    'POST /api/auth/logout', 
                    'GET /api/auth/me'
                ],
                'dashboard' => [
                    'GET /api/dashboard/stats',
                    'GET /api/dashboard/usage-data',
                    'GET /api/dashboard/vehicle-status',
                    'GET /api/dashboard/recent-bookings'
                ],
                'bookings' => [
                    'GET /api/bookings',
                    'POST /api/bookings'
                ],
                'approvals' => [
                    'GET /api/approvals/pending',
                    'POST /api/approvals/process'
                ],
                'vehicles' => [
                    'GET /api/vehicles',
                    'POST /api/vehicles'
                ],
                'drivers' => [
                    'GET /api/drivers',
                    'POST /api/drivers'
                ]
            ]
        ]);
    }
}