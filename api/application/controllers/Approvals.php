<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approvals extends MY_Controller {
    
    public function pending() {
        $this->require_role(['approver', 'admin']);
        
        $user_id = $this->user['id'];
        $approval_level = $this->user['approval_level'];
        
        // Get pending approvals for this user's level
        $sql = "SELECT b.*, 
                       u.full_name as requester_name,
                       u.department as requester_department,
                       v.plate_number, v.brand, v.model,
                       d.full_name as driver_name
                FROM bookings b
                JOIN approvals a ON b.id = a.booking_id
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN vehicles v ON b.vehicle_id = v.id
                LEFT JOIN drivers d ON b.driver_id = d.id
                WHERE a.approver_id = ? AND a.status = 'pending'
                ORDER BY b.created_at DESC";
        
        $result = $this->db->query($sql, [$user_id]);
        
        $approvals = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $approvals[] = [
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
        
        $this->json_response($approvals);
    }
    
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->error_response('Method not allowed', 405);
        }
        
        $this->require_role(['approver', 'admin']);
        
        $input = $this->get_input();
        $this->validate_required($input, ['bookingId', 'action']);
        
        $booking_id = (int)$input['bookingId'];
        $action = $input['action']; // 'approve' or 'reject'
        $comments = isset($input['comments']) ? $input['comments'] : '';
        
        if (!in_array($action, ['approve', 'reject'])) {
            $this->error_response('Invalid action. Must be approve or reject');
        }
        
        // Get the approval record
        $approval_result = $this->db->query(
            "SELECT * FROM approvals WHERE booking_id = ? AND approver_id = ? AND status = 'pending'",
            [$booking_id, $this->user['id']]
        );
        
        if (!$approval_result || $approval_result->num_rows === 0) {
            $this->error_response('Approval not found or already processed', 404);
        }
        
        $approval = $this->db->fetch_array($approval_result);
        
        // Update approval status
        $approval_status = $action === 'approve' ? 'approved' : 'rejected';
        $this->db->query(
            "UPDATE approvals SET status = ?, comments = ?, updated_at = NOW() WHERE id = ?",
            [$approval_status, $comments, $approval['id']]
        );
        
        // Update booking status based on approval level and action
        if ($action === 'reject') {
            $this->db->query(
                "UPDATE bookings SET status = 'rejected', updated_at = NOW() WHERE id = ?",
                [$booking_id]
            );
            
            $this->log_activity('approval_rejected', "Booking {$booking_id} rejected");
        } else {
            // If approved, check if this is level 1 or level 2
            if ($approval['level'] === 'level1') {
                // Create level 2 approval
                $level2_approvers = $this->db->query("SELECT id FROM users WHERE role = 'approver' AND approval_level = 'level2' LIMIT 1");
                if ($level2_approvers && $level2_approvers->num_rows > 0) {
                    $approver = $this->db->fetch_array($level2_approvers);
                    $this->db->query(
                        "INSERT INTO approvals (booking_id, approver_id, level, status) VALUES (?, ?, 'level2', 'pending')",
                        [$booking_id, $approver['id']]
                    );
                }
                
                $this->db->query(
                    "UPDATE bookings SET status = 'approved_level1', updated_at = NOW() WHERE id = ?",
                    [$booking_id]
                );
            } else {
                // Final approval - assign vehicle and driver
                $this->assign_vehicle_and_driver($booking_id);
                
                $this->db->query(
                    "UPDATE bookings SET status = 'approved', updated_at = NOW() WHERE id = ?",
                    [$booking_id]
                );
            }
            
            $this->log_activity('approval_approved', "Booking {$booking_id} approved at level {$approval['level']}");
        }
        
        $this->json_response(['message' => 'Approval processed successfully']);
    }
    
    private function assign_vehicle_and_driver($booking_id) {
        // Get booking details
        $booking_result = $this->db->query(
            "SELECT departure_date, departure_time, return_time FROM bookings WHERE id = ?",
            [$booking_id]
        );
        
        if ($booking_result && $booking_result->num_rows > 0) {
            $booking = $this->db->fetch_array($booking_result);
            
            // Find available vehicle
            $vehicle_result = $this->db->query(
                "SELECT id FROM vehicles WHERE status = 'available' LIMIT 1"
            );
            
            // Find available driver
            $driver_result = $this->db->query(
                "SELECT id FROM drivers WHERE status = 'available' LIMIT 1"
            );
            
            $vehicle_id = null;
            $driver_id = null;
            
            if ($vehicle_result && $vehicle_result->num_rows > 0) {
                $vehicle = $this->db->fetch_array($vehicle_result);
                $vehicle_id = $vehicle['id'];
                
                // Mark vehicle as in use
                $this->db->query(
                    "UPDATE vehicles SET status = 'in_use' WHERE id = ?",
                    [$vehicle_id]
                );
            }
            
            if ($driver_result && $driver_result->num_rows > 0) {
                $driver = $this->db->fetch_array($driver_result);
                $driver_id = $driver['id'];
                
                // Mark driver as on duty
                $this->db->query(
                    "UPDATE drivers SET status = 'on_duty' WHERE id = ?",
                    [$driver_id]
                );
            }
            
            // Update booking with assigned vehicle and driver
            $this->db->query(
                "UPDATE bookings SET vehicle_id = ?, driver_id = ? WHERE id = ?",
                [$vehicle_id, $driver_id, $booking_id]
            );
        }
    }
}