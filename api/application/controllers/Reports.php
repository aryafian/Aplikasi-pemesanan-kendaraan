<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {
    
    public function export() {
        $this->require_role(['admin', 'approver']);
        
        $start_date = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-01');
        $end_date = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-t');
        
        // Get booking data for the date range
        $result = $this->db->query(
            "SELECT b.*, 
                    u.full_name as requester_name,
                    u.department as requester_department,
                    v.plate_number, v.brand, v.model,
                    d.full_name as driver_name
             FROM bookings b
             LEFT JOIN users u ON b.user_id = u.id
             LEFT JOIN vehicles v ON b.vehicle_id = v.id
             LEFT JOIN drivers d ON b.driver_id = d.id
             WHERE b.departure_date BETWEEN ? AND ?
             ORDER BY b.departure_date DESC",
            [$start_date, $end_date]
        );
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="booking_report_' . $start_date . '_to_' . $end_date . '.csv"');
        
        // Output CSV headers
        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Booking Number',
            'Requester',
            'Department',
            'Purpose',
            'Destination',
            'Departure Date',
            'Departure Time',
            'Return Time',
            'Status',
            'Vehicle',
            'Driver',
            'Created At'
        ]);
        
        // Output data rows
        if ($result && $result->num_rows > 0) {
            while ($row = $this->db->fetch_array($result)) {
                fputcsv($output, [
                    $row['booking_number'],
                    $row['requester_name'],
                    $row['requester_department'],
                    $row['purpose'],
                    $row['destination'],
                    $row['departure_date'],
                    $row['departure_time'],
                    $row['return_time'],
                    $row['status'],
                    $row['plate_number'] ? $row['plate_number'] . ' - ' . $row['brand'] . ' ' . $row['model'] : '',
                    $row['driver_name'] ?: '',
                    $row['created_at']
                ]);
            }
        }
        
        fclose($output);
        exit;
    }
}