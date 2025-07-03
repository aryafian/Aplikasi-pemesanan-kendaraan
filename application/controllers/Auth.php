<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    public function login() {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('username', 'Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run()) {
                $username = $this->input->post('username');
                $password = $this->input->post('password');

                $user = $this->User_model->verify_password($username, $password);

                if ($user) {
                    // Set session data
                    $session_data = array(
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'full_name' => $user['full_name'],
                        'role' => $user['role'],
                        'department' => $user['department'],
                        'approval_level' => $user['approval_level'],
                        'logged_in' => TRUE
                    );

                    $this->session->set_userdata($session_data);

                    // Log activity
                    $this->log_activity($user['id'], 'LOGIN', 'USER', $user['id'], 'User logged in');

                    $this->session->set_flashdata('success', 'Selamat datang, ' . $user['full_name'] . '!');
                    redirect('dashboard');
                } else {
                    $this->session->set_flashdata('error', 'Username atau password tidak valid.');
                }
            }
        }

        $data['page_title'] = 'Login - VehicleFlow';
        $this->load->view('auth/login', $data);
    }

    public function logout() {
        // Log activity before destroying session
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $this->log_activity($user_id, 'LOGOUT', 'USER', $user_id, 'User logged out');
        }

        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'Anda berhasil logout.');
        redirect('auth/login');
    }

    public function register() {
        // Only allow admin to register new users
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        $user_id = $this->session->userdata('user_id');
        if (!$this->User_model->is_admin($user_id)) {
            show_error('Access denied', 403, 'Unauthorized Access');
            return;
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
            $this->form_validation->set_rules('full_name', 'Full Name', 'required|trim');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,approver,requester]');
            $this->form_validation->set_rules('department', 'Department', 'required|trim');

            if ($this->form_validation->run()) {
                $data = array(
                    'username' => $this->input->post('username'),
                    'email' => $this->input->post('email'),
                    'password' => $this->input->post('password'),
                    'full_name' => $this->input->post('full_name'),
                    'role' => $this->input->post('role'),
                    'department' => $this->input->post('department'),
                    'approval_level' => $this->input->post('approval_level'),
                    'is_active' => 1
                );

                $new_user_id = $this->User_model->create_user($data);

                if ($new_user_id) {
                    $this->log_activity($user_id, 'CREATE', 'USER', $new_user_id, 'New user registered: ' . $data['username']);
                    $this->session->set_flashdata('success', 'User baru berhasil didaftarkan.');
                    redirect('users');
                } else {
                    $this->session->set_flashdata('error', 'Gagal mendaftarkan user baru.');
                }
            }
        }

        $data = array(
            'page_title' => 'Register User',
            'user' => $this->User_model->get_user_by_id($user_id)
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('auth/register', $data);
        $this->load->view('templates/footer');
    }

    public function profile() {
        $user_id = $this->session->userdata('user_id');
        
        if (!$user_id) {
            redirect('auth/login');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('full_name', 'Full Name', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            
            if ($this->input->post('password')) {
                $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[password]');
            }

            if ($this->form_validation->run()) {
                $data = array(
                    'full_name' => $this->input->post('full_name'),
                    'email' => $this->input->post('email'),
                    'department' => $this->input->post('department')
                );

                if ($this->input->post('password')) {
                    $data['password'] = $this->input->post('password');
                }

                if ($this->User_model->update_user($user_id, $data)) {
                    // Update session data
                    $this->session->set_userdata('full_name', $data['full_name']);
                    
                    $this->log_activity($user_id, 'UPDATE', 'USER', $user_id, 'Profile updated');
                    $this->session->set_flashdata('success', 'Profile berhasil diperbarui.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui profile.');
                }
            }
        }

        $data = array(
            'page_title' => 'Profile',
            'user' => $this->User_model->get_user_by_id($user_id)
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('auth/profile', $data);
        $this->load->view('templates/footer');
    }

    private function log_activity($user_id, $action, $entity_type, $entity_id, $details) {
        $this->load->model('Activity_model');
        
        $data = array(
            'user_id' => $user_id,
            'action' => $action,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'details' => $details,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        );

        $this->Activity_model->create_activity($data);
    }
}