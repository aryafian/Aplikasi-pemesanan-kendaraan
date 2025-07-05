<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    
    public function stats() {
        $this->require_auth();
        
        // Get dashboard statistics
        $stats = [
            'totalBookings' => 0,
            'pendingApproval' => 0,
            'activeVehicles' => 0,
            'efficiency' => 0
        ];
        
        // Total bookings
        $result = $this->db->query("SELECT COUNT(*) as count FROM bookings");
        if ($result && $result->num_rows > 0) {
            $data = $this->db->fetch_array($result);
            $stats['totalBookings'] = (int)$data['count'];
        }
        
        // Pending approvals
        $result = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
        if ($result && $result->num_rows > 0) {
            $data = $this->db->fetch_array($result);
            $stats['pendingApproval'] = (int)$data['count'];
        }
        
        // Active vehicles
        $result = $this->db->query("SELECT COUNT(*) as count FROM vehicles WHERE status = 'available'");
        if ($result && $result->num_rows > 0) {
            $data = $this->db->fetch_array($result);
            $stats['activeVehicles'] = (int)$data['count'];
        }
        
        // Calculate efficiency (approved vs total)
        if ($stats['totalBookings'] > 0) {
            $result = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'approved'");
            if ($result && $result->num_rows > 0) {
                $data = $this->db->fetch_array($result);
                $stats['efficiency'] = round(($data['count'] / $stats['totalBookings']) * 100, 1);
            }
        }
        
        $this->json_response($stats);
    }
    
    public function usage_data() {
        $this->require_auth();
        
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
        
        // Get usage data for the last N days
        $result = $this->db->query(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM bookings 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [$days]
        );
        
        $usage_data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $usage_data[] = [
                    'date' => $row['date'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        $this->json_response($usage_data);
    }
    
    public function vehicle_status() {
        $this->require_auth();
        
        // Get vehicle status distribution
        $result = $this->db->query(
            "SELECT status, COUNT(*) as count 
             FROM vehicles 
             GROUP BY status"
        );
        
        $status_data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $status_data[] = [
                    'status' => $row['status'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        $this->json_response($status_data);
    }
    
    public function recent_bookings() {
        $this->require_auth();
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        // Get recent bookings with user and vehicle info
        $result = $this->db->query(
            "SELECT b.*, 
                    u.full_name as requester_name,
                    v.plate_number, v.brand, v.model,
                    d.full_name as driver_name
             FROM bookings b
             LEFT JOIN users u ON b.user_id = u.id
             LEFT JOIN vehicles v ON b.vehicle_id = v.id
             LEFT JOIN drivers d ON b.driver_id = d.id
             ORDER BY b.created_at DESC
             LIMIT ?",
            [$limit]
        );
        
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
                        'fullName' => $row['requester_name']
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
}