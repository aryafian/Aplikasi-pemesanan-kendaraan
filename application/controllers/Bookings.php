<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bookings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Booking_model');
        $this->load->model('Vehicle_model');
        $this->load->model('Driver_model');
        $this->load->model('Approval_model');
        $this->load->library('form_validation');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);
        
        // Get bookings based on user role
        if ($user['role'] === 'admin' || $user['role'] === 'approver') {
            $bookings = $this->Booking_model->get_all_bookings();
        } else {
            $bookings = $this->Booking_model->get_bookings_by_user($user_id);
        }
        
        $data = array(
            'page_title' => 'Pemesanan Kendaraan',
            'user' => $user,
            'bookings' => $bookings
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('bookings/index', $data);
        $this->load->view('templates/footer');
    }

    public function create() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);

        if ($this->input->post()) {
            $this->form_validation->set_rules('purpose', 'Tujuan', 'required|trim');
            $this->form_validation->set_rules('destination', 'Destinasi', 'required|trim');
            $this->form_validation->set_rules('departure_date', 'Tanggal Berangkat', 'required');
            $this->form_validation->set_rules('return_date', 'Tanggal Kembali', 'required');
            $this->form_validation->set_rules('departure_time', 'Jam Berangkat', 'required');
            $this->form_validation->set_rules('return_time', 'Jam Kembali', 'required');
            $this->form_validation->set_rules('passengers', 'Jumlah Penumpang', 'required|integer|greater_than[0]');

            if ($this->form_validation->run()) {
                $data = array(
                    'requester_id' => $user_id,
                    'purpose' => $this->input->post('purpose'),
                    'destination' => $this->input->post('destination'),
                    'departure_date' => $this->input->post('departure_date'),
                    'return_date' => $this->input->post('return_date'),
                    'departure_time' => $this->input->post('departure_time'),
                    'return_time' => $this->input->post('return_time'),
                    'passengers' => $this->input->post('passengers'),
                    'notes' => $this->input->post('notes')
                );

                $booking_id = $this->Booking_model->create_booking($data);

                if ($booking_id) {
                    // Create approval workflow
                    $this->Approval_model->create_approval_workflow($booking_id);
                    
                    $this->log_activity($user_id, 'CREATE', 'BOOKING', $booking_id, 'New booking created');
                    $this->session->set_flashdata('success', 'Pemesanan berhasil dibuat dan menunggu persetujuan.');
                    redirect('bookings');
                } else {
                    $this->session->set_flashdata('error', 'Gagal membuat pemesanan.');
                }
            }
        }

        $data = array(
            'page_title' => 'Buat Pemesanan Baru',
            'user' => $user,
            'vehicles' => $this->Vehicle_model->get_available_vehicles(),
            'drivers' => $this->Driver_model->get_available_drivers()
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('bookings/create', $data);
        $this->load->view('templates/footer');
    }

    public function view($id) {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);
        
        $booking = $this->Booking_model->get_booking_by_id($id);
        
        if (!$booking) {
            show_404();
        }

        // Check permission
        if ($user['role'] === 'requester' && $booking['requester_id'] != $user_id) {
            show_error('Access denied', 403, 'Unauthorized Access');
            return;
        }

        $approvals = $this->Approval_model->get_approvals_by_booking($id);

        $data = array(
            'page_title' => 'Detail Pemesanan',
            'user' => $user,
            'booking' => $booking,
            'approvals' => $approvals
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('bookings/view', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);
        
        $booking = $this->Booking_model->get_booking_by_id($id);
        
        if (!$booking) {
            show_404();
        }

        // Only allow editing if booking is pending and user is the requester or admin
        if ($booking['status'] !== 'pending' || 
            ($user['role'] === 'requester' && $booking['requester_id'] != $user_id)) {
            $this->session->set_flashdata('error', 'Pemesanan tidak dapat diedit.');
            redirect('bookings');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('purpose', 'Tujuan', 'required|trim');
            $this->form_validation->set_rules('destination', 'Destinasi', 'required|trim');
            $this->form_validation->set_rules('departure_date', 'Tanggal Berangkat', 'required');
            $this->form_validation->set_rules('return_date', 'Tanggal Kembali', 'required');
            $this->form_validation->set_rules('departure_time', 'Jam Berangkat', 'required');
            $this->form_validation->set_rules('return_time', 'Jam Kembali', 'required');
            $this->form_validation->set_rules('passengers', 'Jumlah Penumpang', 'required|integer|greater_than[0]');

            if ($this->form_validation->run()) {
                $data = array(
                    'purpose' => $this->input->post('purpose'),
                    'destination' => $this->input->post('destination'),
                    'departure_date' => $this->input->post('departure_date'),
                    'return_date' => $this->input->post('return_date'),
                    'departure_time' => $this->input->post('departure_time'),
                    'return_time' => $this->input->post('return_time'),
                    'passengers' => $this->input->post('passengers'),
                    'notes' => $this->input->post('notes')
                );

                if ($this->Booking_model->update_booking($id, $data)) {
                    $this->log_activity($user_id, 'UPDATE', 'BOOKING', $id, 'Booking updated');
                    $this->session->set_flashdata('success', 'Pemesanan berhasil diperbarui.');
                    redirect('bookings/view/' . $id);
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui pemesanan.');
                }
            }
        }

        $data = array(
            'page_title' => 'Edit Pemesanan',
            'user' => $user,
            'booking' => $booking,
            'vehicles' => $this->Vehicle_model->get_available_vehicles(),
            'drivers' => $this->Driver_model->get_available_drivers()
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('bookings/edit', $data);
        $this->load->view('templates/footer');
    }

    public function delete($id) {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);
        
        $booking = $this->Booking_model->get_booking_by_id($id);
        
        if (!$booking) {
            show_404();
        }

        // Only allow deletion by admin or requester (if pending)
        if ($user['role'] !== 'admin' && 
            ($user['role'] === 'requester' && ($booking['requester_id'] != $user_id || $booking['status'] !== 'pending'))) {
            show_error('Access denied', 403, 'Unauthorized Access');
            return;
        }

        if ($this->Booking_model->delete_booking($id)) {
            $this->log_activity($user_id, 'DELETE', 'BOOKING', $id, 'Booking deleted');
            $this->session->set_flashdata('success', 'Pemesanan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pemesanan.');
        }

        redirect('bookings');
    }

    public function assign_resources($id) {
        $user_id = $this->session->userdata('user_id');
        
        if (!$this->User_model->is_admin($user_id)) {
            show_error('Access denied', 403, 'Unauthorized Access');
            return;
        }

        $booking = $this->Booking_model->get_booking_by_id($id);
        
        if (!$booking || $booking['status'] !== 'approved') {
            $this->session->set_flashdata('error', 'Pemesanan tidak dapat diassign resources.');
            redirect('bookings');
        }

        if ($this->input->post()) {
            $vehicle_id = $this->input->post('vehicle_id');
            $driver_id = $this->input->post('driver_id');

            if ($this->Booking_model->assign_vehicle_and_driver($id, $vehicle_id, $driver_id)) {
                // Update vehicle status
                if ($vehicle_id) {
                    $this->Vehicle_model->update_vehicle_status($vehicle_id, 'in_use');
                }

                $this->log_activity($user_id, 'ASSIGN', 'BOOKING', $id, 'Resources assigned to booking');
                $this->session->set_flashdata('success', 'Kendaraan dan driver berhasil diassign.');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengassign resources.');
            }
        }

        redirect('bookings/view/' . $id);
    }

    private function log_activity($user_id, $action, $entity_type, $entity_id, $details) {
        $this->load->model('Activity_model');
        
        $data = array(
            'user_id' => $user_id,
            'action' => $action,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'details' => $details,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        );

        $this->Activity_model->create_activity($data);
    }
}