<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {

    public $table = 'bookings';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all bookings with details
     * @return array
     */
    public function get_all_bookings()
    {
        $this->db->select(
            'bookings.id, '.
            'bookings.start_date, '.
            'bookings.end_date, '.
            'bookings.destination, '.
            'bookings.status, '.
            'vehicles.name as vehicle_name, '.
            'drivers.name as driver_name, '.
            'users.full_name as requester_name'
        );
        $this->db->from('bookings');
        $this->db->join('users', 'users.id = bookings.requester_id');
        $this->db->join('vehicles', 'vehicles.id = bookings.vehicle_id');
        $this->db->join('drivers', 'drivers.id = bookings.driver_id');
        $this->db->order_by('bookings.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get a single booking by its ID with full details
     * @param int $id
     * @return object|null
     */
    public function get_booking_by_id($id)
    {
        $this->db->select('bookings.*, vehicles.name as vehicle_name, drivers.name as driver_name, users.full_name as requester_name');
        $this->db->from('bookings');
        $this->db->join('vehicles', 'bookings.vehicle_id = vehicles.id');
        $this->db->join('drivers', 'bookings.driver_id = drivers.id');
        $this->db->join('users', 'bookings.requester_id = users.id');
        $this->db->where('bookings.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Insert a new booking
     * @param array $data
     * @return int|bool The ID of the new booking or false on failure
     */
    public function create_booking($data)
    {
        if ($this->db->insert('bookings', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Get the approval history for a specific booking
     * @param int $booking_id
     * @return array
     */
    public function get_booking_approvals($booking_id)
    {
        $this->db->select('booking_approvals.*, users.full_name as approver_name');
        $this->db->from('booking_approvals');
        $this->db->join('users', 'booking_approvals.approver_id = users.id');
        $this->db->where('booking_id', $booking_id);
        $this->db->order_by('approval_level', 'ASC');
        return $this->db->get()->result();
    }
    
    /**
     * Get bookings that are waiting for approval from a specific user/level
     * @param int $level
     * @return array
     */
    public function get_pending_approvals_by_level($level)
    {
        $this->db->select('b.id, v.name as vehicle_name, d.name as driver_name, u.full_name as requester_name, b.start_date, b.end_date, b.destination');
        $this->db->from('bookings as b');
        $this->db->join('vehicles as v', 'b.vehicle_id = v.id');
        $this->db->join('drivers as d', 'b.driver_id = d.id');
        $this->db->join('users as u', 'b.requester_id = u.id');
        $this->db->where('b.status', 'pending');
        $this->db->where('b.current_approver_level', $level);
        return $this->db->get()->result();
    }

    /**
     * Update the status of a booking
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_booking_status($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('bookings', $data);
    }

    /**
     * Add a record to the booking_approvals table
     * @param array $data
     * @return bool
     */
    public function add_approval($data)
    {
        return $this->db->insert('booking_approvals', $data);
    }

    /**
     * Get counts of bookings grouped by status
     * @return array
     */
    public function get_booking_status_counts()
    {
        $this->db->select('status, COUNT(id) as count');
        $this->db->from('bookings');
        $this->db->group_by('status');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Count all bookings
     * @return int
     */
    public function count_all_bookings()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Get recent bookings
     * @param int $limit
     * @return array
     */
    public function get_recent_bookings($limit = 5)
    {
        $this->db->select('bookings.*, users.full_name as requester_name, vehicles.name as vehicle_name, drivers.name as driver_name');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = bookings.requester_id');
        $this->db->join('vehicles', 'vehicles.id = bookings.vehicle_id');
        $this->db->join('drivers', 'drivers.id = bookings.driver_id');
        $this->db->order_by('bookings.id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }
}
