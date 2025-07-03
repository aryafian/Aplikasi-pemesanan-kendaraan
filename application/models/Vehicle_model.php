<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_model extends CI_Model {

    private $table = 'vehicles';

    public function __construct() {
        parent::__construct();
    }

    public function get_all_vehicles() {
        return $this->db->get($this->table)->result_array();
    }

    public function get_vehicle_by_id($id) {
        return $this->db->get_where($this->table, array('id' => $id))->row_array();
    }

    public function get_vehicle_by_plate($plate_number) {
        return $this->db->get_where($this->table, array('plate_number' => $plate_number))->row_array();
    }

    public function create_vehicle($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_vehicle($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_vehicle($id) {
        return $this->db->delete($this->table, array('id' => $id));
    }

    public function get_available_vehicles($start_date = null, $end_date = null) {
        $this->db->where('status', 'available');
        
        if ($start_date && $end_date) {
            // Check for overlapping bookings
            $this->db->where_not_in('id', $this->get_booked_vehicle_ids($start_date, $end_date));
        }
        
        return $this->db->get($this->table)->result_array();
    }

    public function get_vehicles_by_status($status) {
        return $this->db->get_where($this->table, array('status' => $status))->result_array();
    }

    public function get_vehicle_statistics() {
        $stats = array();
        
        // Total vehicles
        $stats['total'] = $this->db->count_all($this->table);
        
        // Available vehicles
        $this->db->where('status', 'available');
        $stats['available'] = $this->db->count_all_results($this->table);
        
        // In use vehicles
        $this->db->where('status', 'in_use');
        $stats['in_use'] = $this->db->count_all_results($this->table);
        
        // Maintenance vehicles
        $this->db->where('status', 'maintenance');
        $stats['maintenance'] = $this->db->count_all_results($this->table);
        
        return $stats;
    }

    public function get_vehicles_needing_maintenance() {
        $this->db->where('next_maintenance <=', date('Y-m-d'));
        $this->db->where('status !=', 'maintenance');
        return $this->db->get($this->table)->result_array();
    }

    private function get_booked_vehicle_ids($start_date, $end_date) {
        $this->db->select('vehicle_id');
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
            if ($row['vehicle_id']) {
                $result[] = $row['vehicle_id'];
            }
        }
        
        return $result;
    }

    public function update_vehicle_status($id, $status) {
        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
}