<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seeds extends MY_Controller {
    
    public function run() {
        $this->require_role(['admin']);
        
        // Create additional sample data
        $this->create_sample_bookings();
        
        $this->json_response(['message' => 'Sample data created successfully']);
    }
    
    private function create_sample_bookings() {
        // Get sample user IDs
        $users = $this->db->query("SELECT id FROM users WHERE role = 'requester' LIMIT 3");
        $user_ids = [];
        if ($users && $users->num_rows > 0) {
            while ($user = $this->db->fetch_array($users)) {
                $user_ids[] = $user['id'];
            }
        }
        
        if (empty($user_ids)) {
            return;
        }
        
        // Create sample bookings
        $sample_bookings = [
            [
                'booking_number' => 'BK' . date('Ymd') . '001',
                'user_id' => $user_ids[0],
                'purpose' => 'Client meeting at downtown office',
                'destination' => 'Jakarta Business District',
                'departure_date' => date('Y-m-d', strtotime('+1 day')),
                'departure_time' => '09:00:00',
                'return_time' => '17:00:00',
                'status' => 'pending'
            ],
            [
                'booking_number' => 'BK' . date('Ymd') . '002',
                'user_id' => $user_ids[0],
                'purpose' => 'Supply delivery to warehouse',
                'destination' => 'Bekasi Industrial Area',
                'departure_date' => date('Y-m-d', strtotime('+2 days')),
                'departure_time' => '08:00:00',
                'return_time' => '16:00:00',
                'status' => 'approved'
            ],
            [
                'booking_number' => 'BK' . date('Ymd') . '003',
                'user_id' => count($user_ids) > 1 ? $user_ids[1] : $user_ids[0],
                'purpose' => 'Training session at partner company',
                'destination' => 'Tangerang Business Park',
                'departure_date' => date('Y-m-d', strtotime('+3 days')),
                'departure_time' => '10:00:00',
                'return_time' => '15:00:00',
                'status' => 'pending'
            ]
        ];
        
        foreach ($sample_bookings as $booking) {
            // Check if booking already exists
            $exists = $this->db->query(
                "SELECT id FROM bookings WHERE booking_number = ?",
                [$booking['booking_number']]
            );
            
            if (!$exists || $exists->num_rows === 0) {
                $this->db->query(
                    "INSERT INTO bookings (booking_number, user_id, purpose, destination, departure_date, departure_time, return_time, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $booking['booking_number'],
                        $booking['user_id'],
                        $booking['purpose'],
                        $booking['destination'],
                        $booking['departure_date'],
                        $booking['departure_time'],
                        $booking['return_time'],
                        $booking['status']
                    ]
                );
            }
        }
    }
}