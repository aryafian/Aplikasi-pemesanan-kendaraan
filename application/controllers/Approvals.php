<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approvals extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model');

        // Security check: must be logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        // Security check: must be an approver
        $role = $this->session->userdata('role');
        if ($role != 'approver1' && $role != 'approver2') {
            // Redirect if not an approver, maybe show an unauthorized message
            $this->session->set_flashdata('error', 'You are not authorized to view this page.');
            redirect('dashboard');
        }
    }

    /**
     * Display a list of bookings pending approval for the current user.
     */
    public function index()
    {
        $data['title'] = 'Pending Approvals';
        $approval_level = $this->session->userdata('approval_level');
        $data['pending_bookings'] = $this->Booking_model->get_pending_approvals_by_level($approval_level);

        $this->load->view('templates/header', $data);
        $this->load->view('approvals/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Process the approval/rejection of a booking.
     * @param int $booking_id
     */
    public function process($booking_id)
    {
        $action = $this->input->post('action');
        $comments = $this->input->post('comments');
        $approver_id = $this->session->userdata('user_id');
        $approval_level = $this->session->userdata('approval_level');

        $this->db->trans_start();

        // 1. Add to approval history
        $approval_data = [
            'booking_id' => $booking_id,
            'approver_id' => $approver_id,
            'approval_level' => $approval_level,
            'status' => $action, // 'approved' or 'rejected'
            'comments' => $comments
        ];
        $this->Booking_model->add_approval($approval_data);

        // 2. Update the booking status
        if ($action == 'approved') {
            // Check if there is a next level of approval required
            $next_level = $approval_level + 1;
            // For this project, let's assume max level is 2
            $max_approval_level = 2;

            if ($next_level > $max_approval_level) {
                // Final approval
                $this->Booking_model->update_booking_status($booking_id, ['status' => 'approved', 'current_approver_level' => 0]);
            } else {
                // Move to next approver
                $this->Booking_model->update_booking_status($booking_id, ['current_approver_level' => $next_level]);
            }
        } else { // Rejected
            $this->Booking_model->update_booking_status($booking_id, ['status' => 'rejected', 'current_approver_level' => 0]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Failed to process the request.');
        } else {
            $this->session->set_flashdata('message', 'The booking has been successfully ' . $action . '.');
        }

        redirect('approvals');
    }
}
