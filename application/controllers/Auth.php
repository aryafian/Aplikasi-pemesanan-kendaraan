<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        // The form_validation, session, and database libraries are autoloaded.
    }

    public function index()
    {
        // If user is already logged in, redirect to dashboard
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        $this->load->view('auth/login_view');
    }

    public function process_login()
    {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            // If validation fails, show the login page again with errors
            $this->load->view('auth/login_view');
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->User_model->get_user_by_username($username);

            if ($user) {
                if ($this->User_model->verify_password($password, $user->password)) {
                    
                    $session_data = array(
                        'user_id'   => $user->id,
                        'username'  => $user->username,
                        'full_name' => $user->full_name,
                        'role'      => $user->role,
                        'approval_level' => $user->approval_level,
                        'logged_in' => TRUE
                    );

                    $this->session->set_userdata($session_data);
                    redirect('dashboard');
                } else {
                    $data['error'] = 'Invalid username or password.';
                    $this->load->view('auth/login_view', $data);
                }
            } else {
                $data['error'] = 'Invalid username or password.';
                $this->load->view('auth/login_view', $data);
            }
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}
