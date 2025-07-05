<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bookings extends MY_Controller {
    
    public function index() {
        $this->require_auth();
        
        $user_id = $this->user['id'];
        $role = $this->user['role'];
        
        // Admin and approvers can see all bookings, requesters only see their own
        if ($role === 'admin' || $role === 'approver') {
            $sql = "SELECT b.*, 
                           u.full_name as requester_name,
                           u.department as requester_department,
                           v.plate_number, v.brand, v.model,
                           d.full_name as driver_name
                    FROM bookings b
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN vehicles v ON b.vehicle_id = v.id
                    LEFT JOIN drivers d ON b.driver_id = d.id
                    ORDER BY b.created_at DESC";
            $result = $this->db->query($sql);
        } else {
            $sql = "SELECT b.*, 
                           u.full_name as requester_name,
                           u.department as requester_department,
                           v.plate_number, v.brand, v.model,
                           d.full_name as driver_name
                    FROM bookings b
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN vehicles v ON b.vehicle_id = v.id
                    LEFT JOIN drivers d ON b.driver_id = d.id
                    WHERE b.user_id = ?
                    ORDER BY b.created_at DESC";
            $result = $this->db->query($sql, [$user_id]);
        }
        
        $bookings = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $bookings[] = [
                    'id' => (int)$row['id'],
                    'bookingNumber' => $row['booking_number'],
                    'purpose' => $row['purpose'],
                    'destination' => $row['destination'],
                    'departureDate' => $row['departure_date'],
                    'departureTime' => $row['departure_time'],
                    'returnTime' => $row['return_time'],
                    'status' => $row['status'],
                    'requester' => [
                        'fullName' => $row['requester_name'],
                        'department' => $row['requester_department']
                    ],
                    'vehicle' => $row['vehicle_id'] ? [
                        'plateNumber' => $row['plate_number'],
                        'brand' => $row['brand'],
                        'model' => $row['model']
                    ] : null,
                    'driver' => $row['driver_id'] ? [
                        'fullName' => $row['driver_name']
                    ] : null,
                    'createdAt' => $row['created_at']
                ];
            }
        }
        
        $this->json_response($bookings);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->error_response('Method not allowed', 405);
        }
        
        $this->require_auth();
        
        $input = $this->get_input();
        $required_fields = ['purpose', 'destination', 'departureDate', 'departureTime', 'returnTime'];
        $this->validate_required($input, $required_fields);
        
        // Generate booking number
        $booking_number = 'BK' . date('Ymd') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        // Insert booking
        $sql = "INSERT INTO bookings (booking_number, user_id, purpose, destination, departure_date, departure_time, return_time, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $result = $this->db->query($sql, [
            $booking_number,
            $this->user['id'],
            $input['purpose'],
            $input['destination'],
            $input['departureDate'],
            $input['departureTime'],
            $input['returnTime']
        ]);
        
        if ($result) {
            $booking_id = $this->db->insert_id();
            
            // Create level 1 approval record
            $level1_approvers = $this->db->query("SELECT id FROM users WHERE role = 'approver' AND approval_level = 'level1' LIMIT 1");
            if ($level1_approvers && $level1_approvers->num_rows > 0) {
                $approver = $this->db->fetch_array($level1_approvers);
                $this->db->query(
                    "INSERT INTO approvals (booking_id, approver_id, level, status) VALUES (?, ?, 'level1', 'pending')",
                    [$booking_id, $approver['id']]
                );
            }
            
            // Log activity
            $this->log_activity('booking_created', "Booking $booking_number created");
            
            $this->json_response([
                'id' => $booking_id,
                'bookingNumber' => $booking_number,
                'message' => 'Booking created successfully'
            ], 201);
        } else {
            $this->error_response('Failed to create booking', 500);
        }
    }
}