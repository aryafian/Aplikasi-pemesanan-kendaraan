<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {

    private $table = 'bookings';

    public function __construct() {
        parent::__construct();
    }

    public function get_all_bookings() {
        $this->db->select('b.*, u.full_name as requester_name, u.department, v.plate_number, v.brand, v.model, d.full_name as driver_name');
        $this->db->from($this->table . ' b');
        $this->db->join('users u', 'b.requester_id = u.id', 'left');
        $this->db->join('vehicles v', 'b.vehicle_id = v.id', 'left');
        $this->db->join('drivers d', 'b.driver_id = d.id', 'left');
        $this->db->order_by('b.created_at', 'DESC');
        
        return $this->db->get()->result_array();
    }

    public function get_booking_by_id($id) {
        $this->db->select('b.*, u.full_name as requester_name, u.department, u.email as requester_email, v.plate_number, v.brand, v.model, d.full_name as driver_name, d.phone as driver_phone');
        $this->db->from($this->table . ' b');
        $this->db->join('users u', 'b.requester_id = u.id', 'left');
        $this->db->join('vehicles v', 'b.vehicle_id = v.id', 'left');
        $this->db->join('drivers d', 'b.driver_id = d.id', 'left');
        $this->db->where('b.id', $id);
        
        return $this->db->get()->row_array();
    }

    public function get_bookings_by_user($user_id) {
        $this->db->select('b.*, v.plate_number, v.brand, v.model, d.full_name as driver_name');
        $this->db->from($this->table . ' b');
        $this->db->join('vehicles v', 'b.vehicle_id = v.id', 'left');
        $this->db->join('drivers d', 'b.driver_id = d.id', 'left');
        $this->db->where('b.requester_id', $user_id);
        $this->db->order_by('b.created_at', 'DESC');
        
        return $this->db->get()->result_array();
    }

    public function create_booking($data) {
        $data['booking_number'] = $this->generate_booking_number();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'pending';
        
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_booking($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_booking($id) {
        return $this->db->delete($this->table, array('id' => $id));
    }

    public function get_pending_approvals($approver_id = null) {
        $this->db->select('b.*, u.full_name as requester_name, u.department, a.level as approval_level');
        $this->db->from($this->table . ' b');
        $this->db->join('users u', 'b.requester_id = u.id', 'left');
        $this->db->join('approvals a', 'b.id = a.booking_id AND a.status = "pending"', 'inner');
        
        if ($approver_id) {
            $this->db->where('a.approver_id', $approver_id);
        }
        
        $this->db->order_by('b.created_at', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_booking_statistics() {
        $stats = array();
        
        // Total bookings
        $stats['total'] = $this->db->count_all($this->table);
        
        // Pending approval
        $this->db->where('status', 'pending');
        $this->db->or_where('status', 'approved_level1');
        $this->db->or_where('status', 'approved_level2');
        $stats['pending_approval'] = $this->db->count_all_results($this->table);
        
        // Approved bookings
        $this->db->where('status', 'approved');
        $stats['approved'] = $this->db->count_all_results($this->table);
        
        // Completed bookings
        $this->db->where('status', 'completed');
        $stats['completed'] = $this->db->count_all_results($this->table);
        
        // Rejected bookings
        $this->db->where('status', 'rejected');
        $stats['rejected'] = $this->db->count_all_results($this->table);
        
        return $stats;
    }

    public function get_recent_bookings($limit = 10) {
        $this->db->select('b.*, u.full_name as requester_name, v.plate_number');
        $this->db->from($this->table . ' b');
        $this->db->join('users u', 'b.requester_id = u.id', 'left');
        $this->db->join('vehicles v', 'b.vehicle_id = v.id', 'left');
        $this->db->order_by('b.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

    public function get_usage_data($days = 30) {
        $this->db->select('DATE(created_at) as date, COUNT(*) as count');
        $this->db->from($this->table);
        $this->db->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")));
        $this->db->group_by('DATE(created_at)');
        $this->db->order_by('date', 'ASC');
        
        return $this->db->get()->result_array();
    }

    public function assign_vehicle_and_driver($booking_id, $vehicle_id, $driver_id) {
        $data = array(
            'vehicle_id' => $vehicle_id,
            'driver_id' => $driver_id,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('id', $booking_id);
        return $this->db->update($this->table, $data);
    }

    private function generate_booking_number() {
        $prefix = 'BK-' . date('Y') . '-';
        
        // Get the last booking number for this year
        $this->db->select('booking_number');
        $this->db->from($this->table);
        $this->db->like('booking_number', $prefix, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $last_number = $query->row()->booking_number;
            $number = intval(substr($last_number, -3)) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function get_bookings_for_export($start_date = null, $end_date = null) {
        $this->db->select('b.*, u.full_name as requester_name, u.department, v.plate_number, v.brand, v.model, d.full_name as driver_name');
        $this->db->from($this->table . ' b');
        $this->db->join('users u', 'b.requester_id = u.id', 'left');
        $this->db->join('vehicles v', 'b.vehicle_id = v.id', 'left');
        $this->db->join('drivers d', 'b.driver_id = d.id', 'left');
        
        if ($start_date) {
            $this->db->where('b.departure_date >=', $start_date);
        }
        
        if ($end_date) {
            $this->db->where('b.departure_date <=', $end_date);
        }
        
        $this->db->order_by('b.departure_date', 'DESC');
        return $this->db->get()->result_array();
    }
}