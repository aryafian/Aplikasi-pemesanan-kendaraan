<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Booking_model');
        $this->load->model('Vehicle_model');
        $this->load->model('Driver_model');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);
        
        $data = array(
            'page_title' => 'Dashboard',
            'user' => $user,
            'stats' => $this->get_dashboard_stats(),
            'recent_bookings' => $this->Booking_model->get_recent_bookings(5),
            'usage_data' => $this->Booking_model->get_usage_data(30),
            'vehicle_status_data' => $this->get_vehicle_status_data()
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer');
    }

    private function get_dashboard_stats() {
        $booking_stats = $this->Booking_model->get_booking_statistics();
        $vehicle_stats = $this->Vehicle_model->get_vehicle_statistics();
        
        // Calculate efficiency (approved bookings / total bookings)
        $efficiency = 0;
        if ($booking_stats['total'] > 0) {
            $efficiency = round(($booking_stats['approved'] / $booking_stats['total']) * 100, 1);
        }
        
        return array(
            'total_bookings' => $booking_stats['total'],
            'pending_approval' => $booking_stats['pending_approval'],
            'active_vehicles' => $vehicle_stats['available'] + $vehicle_stats['in_use'],
            'efficiency' => $efficiency
        );
    }

    private function get_vehicle_status_data() {
        $vehicle_stats = $this->Vehicle_model->get_vehicle_statistics();
        
        return array(
            array('status' => 'Available', 'count' => $vehicle_stats['available']),
            array('status' => 'In Use', 'count' => $vehicle_stats['in_use']),
            array('status' => 'Maintenance', 'count' => $vehicle_stats['maintenance'])
        );
    }

    public function seed_data() {
        // Check if user is admin
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);
        
        if (!$user || $user['role'] !== 'admin') {
            show_error('Access denied', 403, 'Unauthorized Access');
            return;
        }

        try {
            $this->load->library('seed_library');
            $this->seed_library->run_seed();
            
            $this->session->set_flashdata('success', 'Data sample berhasil dibuat!');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Gagal membuat data sample: ' . $e->getMessage());
        }
        
        redirect('dashboard');
    }

    // AJAX endpoints for dashboard data
    public function ajax_stats() {
        header('Content-Type: application/json');
        echo json_encode($this->get_dashboard_stats());
    }

    public function ajax_usage_data() {
        header('Content-Type: application/json');
        $days = $this->input->get('days') ?: 30;
        echo json_encode($this->Booking_model->get_usage_data($days));
    }

    public function ajax_vehicle_status() {
        header('Content-Type: application/json');
        echo json_encode($this->get_vehicle_status_data());
    }

    public function ajax_recent_bookings() {
        header('Content-Type: application/json');
        $limit = $this->input->get('limit') ?: 10;
        echo json_encode($this->Booking_model->get_recent_bookings($limit));
    }
}