<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drivers extends MY_Controller {
    
    public function index() {
        $this->require_auth();
        
        $result = $this->db->query(
            "SELECT * FROM drivers ORDER BY created_at DESC"
        );
        
        $drivers = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $drivers[] = [
                    'id' => (int)$row['id'],
                    'fullName' => $row['full_name'],
                    'licenseNumber' => $row['license_number'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'status' => $row['status'],
                    'createdAt' => $row['created_at']
                ];
            }
        }
        
        $this->json_response($drivers);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->error_response('Method not allowed', 405);
        }
        
        $this->require_role(['admin']);
        
        $input = $this->get_input();
        $required_fields = ['fullName', 'licenseNumber', 'phone'];
        $this->validate_required($input, $required_fields);
        
        // Check if license number already exists
        $check_result = $this->db->query(
            "SELECT id FROM drivers WHERE license_number = ?",
            [$input['licenseNumber']]
        );
        
        if ($check_result && $check_result->num_rows > 0) {
            $this->error_response('License number already exists', 409);
        }
        
        // Insert driver
        $result = $this->db->query(
            "INSERT INTO drivers (full_name, license_number, phone, email, status) 
             VALUES (?, ?, ?, ?, 'available')",
            [
                $input['fullName'],
                $input['licenseNumber'],
                $input['phone'],
                isset($input['email']) ? $input['email'] : null
            ]
        );
        
        if ($result) {
            $driver_id = $this->db->insert_id();
            
            // Log activity
            $this->log_activity('driver_created', "Driver {$input['fullName']} created");
            
            $this->json_response([
                'id' => $driver_id,
                'message' => 'Driver created successfully'
            ], 201);
        } else {
            $this->error_response('Failed to create driver', 500);
        }
    }
}