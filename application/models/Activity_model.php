<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_model extends CI_Model {

    private $table = 'activity_logs';

    public function __construct() {
        parent::__construct();
    }

    public function create_activity($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function get_activities($limit = 50) {
        $this->db->select('a.*, u.full_name as user_name');
        $this->db->from($this->table . ' a');
        $this->db->join('users u', 'a.user_id = u.id', 'left');
        $this->db->order_by('a.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

    public function get_activities_by_user($user_id, $limit = 20) {
        $this->db->select('a.*, u.full_name as user_name');
        $this->db->from($this->table . ' a');
        $this->db->join('users u', 'a.user_id = u.id', 'left');
        $this->db->where('a.user_id', $user_id);
        $this->db->order_by('a.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

    public function get_activities_by_entity($entity_type, $entity_id, $limit = 10) {
        $this->db->select('a.*, u.full_name as user_name');
        $this->db->from($this->table . ' a');
        $this->db->join('users u', 'a.user_id = u.id', 'left');
        $this->db->where('a.entity_type', $entity_type);
        $this->db->where('a.entity_id', $entity_id);
        $this->db->order_by('a.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

    public function get_activity_statistics($days = 30) {
        $this->db->select('action, COUNT(*) as count');
        $this->db->from($this->table);
        $this->db->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")));
        $this->db->group_by('action');
        $this->db->order_by('count', 'DESC');
        
        return $this->db->get()->result_array();
    }

    public function cleanup_old_logs($days = 90) {
        $this->db->where('created_at <', date('Y-m-d', strtotime("-{$days} days")));
        return $this->db->delete($this->table);
    }
}