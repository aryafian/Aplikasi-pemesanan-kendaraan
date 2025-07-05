<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Check if user is logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Dashboard';

        // Load models
        $this->load->model('Booking_model');
        $this->load->model('Vehicle_model');

        // --- Data for Stat Cards ---
        $data['total_bookings'] = $this->Booking_model->count_all_bookings();
        $data['total_vehicles'] = $this->Vehicle_model->count_all_vehicles();

        $booking_statuses = $this->Booking_model->get_booking_status_counts();
        $pending_count = 0;
        foreach ($booking_statuses as $status) {
            if ($status->status == 'pending') {
                $pending_count = $status->count;
                break;
            }
        }
        $data['pending_bookings'] = $pending_count;

        $vehicle_statuses = $this->Vehicle_model->get_vehicle_availability_counts();
        $active_vehicles = 0;
        foreach ($vehicle_statuses as $status) {
            if ($status->is_available == 0) { // 0 = In Use / Active
                $active_vehicles = $status->count;
                break;
            }
        }
        $data['active_vehicles'] = $active_vehicles;

        // --- Data for Recent Bookings ---
        $data['recent_bookings'] = $this->Booking_model->get_recent_bookings(5);

        // --- Data for Charts (using placeholders for now) ---
        // Vehicle Usage Chart (Line)
        $data['vehicle_usage_labels'] = json_encode(['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7']);
        $data['vehicle_usage_data'] = json_encode([5, 6, 8, 7, 9, 10, 8]);

        // Vehicle Status Chart (Doughnut)
        $available_vehicles = $data['total_vehicles'] - $data['active_vehicles'];
        $data['vehicle_status_labels'] = json_encode(['Tersedia', 'Dipinjam', 'Maintenance']);
        $data['vehicle_status_data'] = json_encode([$available_vehicles, $data['active_vehicles'], 2]); // Placeholder for maintenance

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard_view', $data);
        $this->load->view('templates/footer');
    }
}
