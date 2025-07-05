<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Driver_model extends CI_Model {

    private $table = 'drivers';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all drivers
     *
     * @return array
     */
    public function get_all()
    {
        return $this->db->get($this->table)->result();
    }

    /**
     * Get driver by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    /**
     * Insert new driver data
     *
     * @param array $data
     * @return bool
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update driver data
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
     * Delete driver data
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
    
    /**
     * Get all available drivers
     *
     * @return array
     */
    public function get_available_drivers()
    {
        return $this->db->get_where($this->table, ['is_available' => 1])->result();
    }
}
