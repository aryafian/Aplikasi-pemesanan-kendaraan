<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bookings extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Booking_model', 'Vehicle_model', 'Driver_model']);

        // Security check
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // For now, only admin can create bookings as per the brief
        if ($this->session->userdata('role') != 'admin') {
            redirect('dashboard');
        }
    }

    /**
     * Display a list of all bookings.
     */
    public function index()
    {
        $data['title'] = 'Manage Bookings';
        $data['bookings'] = $this->Booking_model->get_all_bookings();

        $this->load->view('templates/header', $data);
        $this->load->view('admin/bookings/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Show the form for creating a new booking or process the submission.
     */
    public function create()
    {
        $this->form_validation->set_rules('vehicle_id', 'Vehicle', 'required|integer');
        $this->form_validation->set_rules('driver_id', 'Driver', 'required|integer');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        $this->form_validation->set_rules('destination', 'Destination', 'required');
        $this->form_validation->set_rules('purpose', 'Purpose', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Create New Booking';
            $data['vehicles'] = $this->Vehicle_model->get_all(); // In a real app, filter by availability
            $data['drivers'] = $this->Driver_model->get_available_drivers();

            $this->load->view('templates/header', $data);
            $this->load->view('admin/bookings/create', $data);
            $this->load->view('templates/footer');
        } else {
            $booking_data = [
                'requester_id' => $this->session->userdata('user_id'),
                'vehicle_id' => $this->input->post('vehicle_id'),
                'driver_id' => $this->input->post('driver_id'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'destination' => $this->input->post('destination'),
                'purpose' => $this->input->post('purpose'),
                'status' => 'pending', // Initial status
                'current_approver_level' => 1, // Start with level 1 approver
            ];

            $this->Booking_model->create_booking($booking_data);
            $this->session->set_flashdata('message', 'Booking created successfully and is pending approval.');
            redirect('admin/bookings');
        }
    }
    
    /**
     * View details of a specific booking.
     * @param int $id The booking ID.
     */
    public function view($id)
    {
        $data['title'] = 'Booking Details';
        $data['booking'] = $this->Booking_model->get_booking_by_id($id);
        $data['approvals'] = $this->Booking_model->get_booking_approvals($id);

        if (empty($data['booking'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('admin/bookings/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Export all bookings to a CSV file.
     */
    public function export_csv()
    {
        $filename = 'bookings_export_' . date('Y-m-d') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $bookings = $this->Booking_model->get_all_bookings();

        $file = fopen('php://output', 'w');

        // Set column headers
        $headers = ['ID', 'Requester', 'Vehicle', 'Driver', 'Start Date', 'End Date', 'Destination', 'Purpose', 'Status'];
        fputcsv($file, $headers);

        // Add data rows
        foreach ($bookings as $booking) {
            $row = [
                $booking->id,
                $booking->requester_name,
                $booking->vehicle_name,
                $booking->driver_name,
                $booking->start_date,
                $booking->end_date,
                $booking->destination,
                $booking->purpose,
                $booking->status
            ];
            fputcsv($file, $row);
        }

        fclose($file);
        exit;
    }
}
