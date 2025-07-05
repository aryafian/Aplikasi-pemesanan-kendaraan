<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->error_response('Method not allowed', 405);
        }
        
        $input = $this->get_input();
        $this->validate_required($input, ['username', 'password']);
        
        $username = $input['username'];
        $password = $input['password'];
        
        // Get user from database
        $result = $this->db->query(
            "SELECT id, username, full_name, role, department, approval_level, password_hash 
             FROM users WHERE username = ?",
            [$username]
        );
        
        if ($result && $result->num_rows > 0) {
            $user = $this->db->fetch_array($result);
            
            if ($this->verify_password($password, $user['password_hash'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Remove password from response
                unset($user['password_hash']);
                
                // Log successful login
                $this->log_activity('login', 'User logged in successfully');
                
                $this->json_response($user);
            } else {
                $this->error_response('Invalid credentials', 401);
            }
        } else {
            $this->error_response('Invalid credentials', 401);
        }
    }
    
    public function logout() {
        $this->require_auth();
        
        // Log logout activity
        $this->log_activity('logout', 'User logged out');
        
        // Destroy session
        session_destroy();
        
        $this->json_response(['message' => 'Logged out successfully']);
    }
    
    public function me() {
        $this->require_auth();
        
        // Return current user data
        $user = $this->user;
        unset($user['password_hash']); // Safety check
        
        $this->json_response($user);
    }
}