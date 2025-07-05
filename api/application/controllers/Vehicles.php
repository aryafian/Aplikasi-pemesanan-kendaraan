<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicles extends MY_Controller {
    
    public function index() {
        $this->require_auth();
        
        $result = $this->db->query(
            "SELECT * FROM vehicles ORDER BY created_at DESC"
        );
        
        $vehicles = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $vehicles[] = [
                    'id' => (int)$row['id'],
                    'plateNumber' => $row['plate_number'],
                    'brand' => $row['brand'],
                    'model' => $row['model'],
                    'year' => (int)$row['year'],
                    'color' => $row['color'],
                    'fuelType' => $row['fuel_type'],
                    'status' => $row['status'],
                    'nextMaintenance' => $row['next_maintenance'],
                    'createdAt' => $row['created_at']
                ];
            }
        }
        
        $this->json_response($vehicles);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->error_response('Method not allowed', 405);
        }
        
        $this->require_role(['admin']);
        
        $input = $this->get_input();
        $required_fields = ['plateNumber', 'brand', 'model', 'year', 'color', 'fuelType'];
        $this->validate_required($input, $required_fields);
        
        // Check if plate number already exists
        $check_result = $this->db->query(
            "SELECT id FROM vehicles WHERE plate_number = ?",
            [$input['plateNumber']]
        );
        
        if ($check_result && $check_result->num_rows > 0) {
            $this->error_response('Plate number already exists', 409);
        }
        
        // Insert vehicle
        $result = $this->db->query(
            "INSERT INTO vehicles (plate_number, brand, model, year, color, fuel_type, status, next_maintenance) 
             VALUES (?, ?, ?, ?, ?, ?, 'available', ?)",
            [
                $input['plateNumber'],
                $input['brand'],
                $input['model'],
                (int)$input['year'],
                $input['color'],
                $input['fuelType'],
                isset($input['nextMaintenance']) ? $input['nextMaintenance'] : null
            ]
        );
        
        if ($result) {
            $vehicle_id = $this->db->insert_id();
            
            // Log activity
            $this->log_activity('vehicle_created', "Vehicle {$input['plateNumber']} created");
            
            $this->json_response([
                'id' => $vehicle_id,
                'message' => 'Vehicle created successfully'
            ], 201);
        } else {
            $this->error_response('Failed to create vehicle', 500);
        }
    }
}