<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Driver_model extends CI_Model {

    private $table = 'drivers';

    public function __construct() {
        parent::__construct();
    }

    public function get_all_drivers() {
        return $this->db->get($this->table)->result_array();
    }

    public function get_driver_by_id($id) {
        return $this->db->get_where($this->table, array('id' => $id))->row_array();
    }

    public function get_driver_by_employee_id($employee_id) {
        return $this->db->get_where($this->table, array('employee_id' => $employee_id))->row_array();
    }

    public function create_driver($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_driver($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_driver($id) {
        return $this->db->delete($this->table, array('id' => $id));
    }

    public function get_available_drivers($start_date = null, $end_date = null) {
        $this->db->where('is_available', 1);
        
        if ($start_date && $end_date) {
            // Check for overlapping bookings
            $this->db->where_not_in('id', $this->get_booked_driver_ids($start_date, $end_date));
        }
        
        return $this->db->get($this->table)->result_array();
    }

    public function get_driver_statistics() {
        $stats = array();
        
        // Total drivers
        $stats['total'] = $this->db->count_all($this->table);
        
        // Available drivers
        $this->db->where('is_available', 1);
        $stats['available'] = $this->db->count_all_results($this->table);
        
        // Unavailable drivers
        $this->db->where('is_available', 0);
        $stats['unavailable'] = $this->db->count_all_results($this->table);
        
        return $stats;
    }

    private function get_booked_driver_ids($start_date, $end_date) {
        $this->db->select('driver_id');
        $this->db->from('bookings');
        $this->db->where('status !=', 'rejected');
        $this->db->where('status !=', 'completed');
        $this->db->group_start();
        $this->db->where('departure_date <=', $end_date);
        $this->db->where('return_date >=', $start_date);
        $this->db->group_end();
        
        $query = $this->db->get();
        $result = array();
        
        foreach ($query->result_array() as $row) {
            if ($row['driver_id']) {
                $result[] = $row['driver_id'];
            }
        }
        
        return $result;
    }

    public function update_driver_availability($id, $is_available) {
        $data = array(
            'is_available' => $is_available,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function get_driver_workload($days = 30) {
        $this->db->select('d.full_name, COUNT(b.id) as total_bookings');
        $this->db->from($this->table . ' d');
        $this->db->join('bookings b', 'd.id = b.driver_id', 'left');
        $this->db->where('b.departure_date >=', date('Y-m-d', strtotime("-{$days} days")));
        $this->db->group_by('d.id');
        $this->db->order_by('total_bookings', 'DESC');
        
        return $this->db->get()->result_array();
    }
}