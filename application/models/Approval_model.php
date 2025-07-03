<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_model extends CI_Model {

    private $table = 'approvals';

    public function __construct() {
        parent::__construct();
    }

    public function get_approvals_by_booking($booking_id) {
        $this->db->select('a.*, u.full_name as approver_name');
        $this->db->from($this->table . ' a');
        $this->db->join('users u', 'a.approver_id = u.id', 'left');
        $this->db->where('a.booking_id', $booking_id);
        $this->db->order_by('a.level', 'ASC');
        
        return $this->db->get()->result_array();
    }

    public function create_approval($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_approval($id, $data) {
        if (in_array($data['status'], ['approved', 'rejected'])) {
            $data['approved_at'] = date('Y-m-d H:i:s');
        }
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function process_approval($booking_id, $approver_id, $status, $comments = null) {
        // Get current user's approval level
        $this->db->select('approval_level');
        $this->db->from('users');
        $this->db->where('id', $approver_id);
        $user = $this->db->get()->row_array();
        
        if (!$user || !$user['approval_level']) {
            return false;
        }
        
        $level = $user['approval_level'];
        
        // Find the approval record
        $this->db->where('booking_id', $booking_id);
        $this->db->where('level', $level);
        $this->db->where('status', 'pending');
        $approval = $this->db->get($this->table)->row_array();
        
        if (!$approval) {
            return false;
        }
        
        // Update approval
        $update_data = array(
            'status' => $status,
            'comments' => $comments,
            'approved_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('id', $approval['id']);
        $result = $this->db->update($this->table, $update_data);
        
        if ($result) {
            // Update booking status based on approval workflow
            $this->update_booking_status($booking_id, $level, $status);
        }
        
        return $result;
    }

    private function update_booking_status($booking_id, $approval_level, $approval_status) {
        $this->load->model('Booking_model');
        
        if ($approval_status === 'rejected') {
            $this->Booking_model->update_booking($booking_id, array('status' => 'rejected'));
            return;
        }
        
        if ($approval_status === 'approved') {
            if ($approval_level === 'level1') {
                // Check if we need level 2 approval
                $this->db->where('booking_id', $booking_id);
                $this->db->where('level', 'level2');
                $level2_exists = $this->db->get($this->table)->num_rows() > 0;
                
                if ($level2_exists) {
                    $this->Booking_model->update_booking($booking_id, array('status' => 'approved_level1'));
                } else {
                    $this->Booking_model->update_booking($booking_id, array('status' => 'approved'));
                    $this->auto_assign_resources($booking_id);
                }
            } elseif ($approval_level === 'level2') {
                $this->Booking_model->update_booking($booking_id, array('status' => 'approved'));
                $this->auto_assign_resources($booking_id);
            }
        }
    }

    private function auto_assign_resources($booking_id) {
        $this->load->model('Vehicle_model');
        $this->load->model('Driver_model');
        
        // Get booking details
        $booking = $this->Booking_model->get_booking_by_id($booking_id);
        
        if (!$booking) {
            return;
        }
        
        // Auto-assign vehicle if not already assigned
        if (!$booking['vehicle_id']) {
            $available_vehicles = $this->Vehicle_model->get_available_vehicles(
                $booking['departure_date'], 
                $booking['return_date']
            );
            
            if (!empty($available_vehicles)) {
                $vehicle_id = $available_vehicles[0]['id'];
                $this->Vehicle_model->update_vehicle_status($vehicle_id, 'in_use');
            }
        }
        
        // Auto-assign driver if not already assigned
        if (!$booking['driver_id']) {
            $available_drivers = $this->Driver_model->get_available_drivers(
                $booking['departure_date'], 
                $booking['return_date']
            );
            
            if (!empty($available_drivers)) {
                $driver_id = $available_drivers[0]['id'];
            }
        }
        
        // Update booking with assignments
        if (isset($vehicle_id) || isset($driver_id)) {
            $update_data = array();
            if (isset($vehicle_id)) $update_data['vehicle_id'] = $vehicle_id;
            if (isset($driver_id)) $update_data['driver_id'] = $driver_id;
            
            $this->Booking_model->update_booking($booking_id, $update_data);
        }
    }

    public function create_approval_workflow($booking_id) {
        // Create level 1 approval
        $level1_approvers = $this->get_approvers_by_level('level1');
        if (!empty($level1_approvers)) {
            $this->create_approval(array(
                'booking_id' => $booking_id,
                'approver_id' => $level1_approvers[0]['id'], // Assign to first available
                'level' => 'level1',
                'status' => 'pending'
            ));
        }
        
        // Create level 2 approval
        $level2_approvers = $this->get_approvers_by_level('level2');
        if (!empty($level2_approvers)) {
            $this->create_approval(array(
                'booking_id' => $booking_id,
                'approver_id' => $level2_approvers[0]['id'], // Assign to first available
                'level' => 'level2',
                'status' => 'pending'
            ));
        }
    }

    private function get_approvers_by_level($level) {
        $this->db->where('role', 'approver');
        $this->db->where('approval_level', $level);
        $this->db->where('is_active', 1);
        return $this->db->get('users')->result_array();
    }

    public function get_pending_approvals_for_user($user_id) {
        $this->db->select('a.*, b.booking_number, b.purpose, b.departure_date, u.full_name as requester_name');
        $this->db->from($this->table . ' a');
        $this->db->join('bookings b', 'a.booking_id = b.id', 'inner');
        $this->db->join('users u', 'b.requester_id = u.id', 'left');
        $this->db->where('a.approver_id', $user_id);
        $this->db->where('a.status', 'pending');
        $this->db->order_by('a.created_at', 'ASC');
        
        return $this->db->get()->result_array();
    }
}