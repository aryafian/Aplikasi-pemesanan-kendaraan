<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_model extends CI_Model {

    public $table = 'vehicles';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all vehicles
     *
     * @return array
     */
    public function get_all()
    {
        return $this->db->get($this->table)->result();
    }

    /**
     * Get vehicle by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    /**
     * Get available vehicles
     *
     * @return array
     */
    public function get_available_vehicles()
    {
        return $this->db->get_where($this->table, ['is_available' => 1])->result();
    }

    /**
     * Get counts of vehicles grouped by availability
     * @return array
     */
    public function get_vehicle_availability_counts()
    {
        $this->db->select('is_available, COUNT(id) as count');
        $this->db->from($this->table);
        $this->db->group_by('is_available');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Count all vehicles
     * @return int
     */
    public function count_all_vehicles()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Insert new vehicle data
     *
     * @param array $data
     * @return bool
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update vehicle data
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete vehicle data
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}
