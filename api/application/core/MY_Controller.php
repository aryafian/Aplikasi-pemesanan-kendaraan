<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Base controller class
class MY_Controller {
    protected $db;
    protected $session;
    protected $user;
    
    public function __construct() {
        // Initialize database
        $this->db = $GLOBALS['db_instance'];
        
        // Initialize session
        $this->init_session();
        
        // Load current user
        $this->load_user();
        
        // Set JSON response headers
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
    
    protected function init_session() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->session = $_SESSION;
    }
    
    protected function load_user() {
        if (isset($_SESSION['user_id'])) {
            $result = $this->db->query(
                "SELECT id, username, full_name, role, department, approval_level 
                 FROM users WHERE id = ?",
                [$_SESSION['user_id']]
            );
            
            if ($result && $result->num_rows > 0) {
                $this->user = $this->db->fetch_array($result);
            }
        }
    }
    
    protected function require_auth() {
        if (!$this->user) {
            http_response_code(401);
            echo json_encode(['message' => 'Authentication required']);
            exit;
        }
    }
    
    protected function require_role($roles) {
        $this->require_auth();
        
        if (!in_array($this->user['role'], $roles)) {
            http_response_code(403);
            echo json_encode(['message' => 'Insufficient permissions']);
            exit;
        }
    }
    
    protected function get_input() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    protected function validate_required($data, $required_fields) {
        $missing = [];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required fields: ' . implode(', ', $missing)]);
            exit;
        }
    }
    
    protected function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    protected function verify_password($password, $hash) {
        return password_verify($password, $hash);
    }
    
    protected function json_response($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
    
    protected function error_response($message, $status = 400) {
        http_response_code($status);
        echo json_encode(['error' => $message]);
        exit;
    }
    
    protected function log_activity($action, $details = null) {
        if ($this->user) {
            $this->db->query(
                "INSERT INTO activity_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())",
                [$this->user['id'], $action, $details]
            );
        }
    }
}