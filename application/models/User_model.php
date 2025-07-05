<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get user by username
     *
     * @param string $username
     * @return object|null
     */
    public function get_user_by_username($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('users');
        return $query->row();
    }

    /**
     * Verify user password
     *
     * @param string $password The plain-text password from user input
     * @param string $hashed_password The hashed password from the database
     * @return bool
     */
    public function verify_password($password, $hashed_password)
    {
        return password_verify($password, $hashed_password);
    }
}
