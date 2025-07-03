<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    private $table = 'users';

    public function __construct() {
        parent::__construct();
    }

    public function get_all_users() {
        return $this->db->get($this->table)->result_array();
    }

    public function get_user_by_id($id) {
        return $this->db->get_where($this->table, array('id' => $id))->row_array();
    }

    public function get_user_by_username($username) {
        return $this->db->get_where($this->table, array('username' => $username))->row_array();
    }

    public function get_user_by_email($email) {
        return $this->db->get_where($this->table, array('email' => $email))->row_array();
    }

    public function create_user($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_user($id, $data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_user($id) {
        return $this->db->delete($this->table, array('id' => $id));
    }

    public function verify_password($username, $password) {
        $user = $this->get_user_by_username($username);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function get_users_by_role($role) {
        return $this->db->get_where($this->table, array('role' => $role))->result_array();
    }

    public function get_approvers($level = null) {
        $this->db->where('role', 'approver');
        if ($level) {
            $this->db->where('approval_level', $level);
        }
        return $this->db->get($this->table)->result_array();
    }

    public function is_admin($user_id) {
        $user = $this->get_user_by_id($user_id);
        return $user && $user['role'] === 'admin';
    }

    public function is_approver($user_id) {
        $user = $this->get_user_by_id($user_id);
        return $user && $user['role'] === 'approver';
    }

    public function can_approve($user_id, $level = null) {
        $user = $this->get_user_by_id($user_id);
        
        if (!$user || $user['role'] !== 'approver') {
            return false;
        }
        
        if ($level) {
            return $user['approval_level'] === $level;
        }
        
        return true;
    }
}