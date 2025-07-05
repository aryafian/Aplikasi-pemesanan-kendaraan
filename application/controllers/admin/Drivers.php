<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drivers extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Driver_model');

        // Security check
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') != 'admin') {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Manage Drivers';
        $data['drivers'] = $this->Driver_model->get_all();

        $this->load->view('templates/header', $data);
        $this->load->view('admin/drivers/index', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        $this->form_validation->set_rules('name', 'Driver Name', 'required');
        $this->form_validation->set_rules('license_number', 'License Number', 'required|is_unique[drivers.license_number]');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Add New Driver';
            $this->load->view('templates/header', $data);
            $this->load->view('admin/drivers/create');
            $this->load->view('templates/footer');
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'license_number' => $this->input->post('license_number'),
                'phone_number' => $this->input->post('phone_number'),
            ];
            $this->Driver_model->insert($data);
            $this->session->set_flashdata('message', 'Driver added successfully!');
            redirect('admin/drivers');
        }
    }

    public function edit($id)
    {
        $driver = $this->Driver_model->get_by_id($id);
        if (!$driver) {
            show_404();
        }

        $original_license = $driver->license_number;
        $is_unique = ($this->input->post('license_number') != $original_license) ? '|is_unique[drivers.license_number]' : '';
        $this->form_validation->set_rules('license_number', 'License Number', 'required' . $is_unique);
        $this->form_validation->set_rules('name', 'Driver Name', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Driver';
            $data['driver'] = $driver;
            $this->load->view('templates/header', $data);
            $this->load->view('admin/drivers/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $update_data = [
                'name' => $this->input->post('name'),
                'license_number' => $this->input->post('license_number'),
                'phone_number' => $this->input->post('phone_number'),
                'is_available' => $this->input->post('is_available'),
            ];
            $this->Driver_model->update($id, $update_data);
            $this->session->set_flashdata('message', 'Driver updated successfully!');
            redirect('admin/drivers');
        }
    }

    public function delete($id)
    {
        $this->Driver_model->delete($id);
        $this->session->set_flashdata('message', 'Driver deleted successfully!');
        redirect('admin/drivers');
    }
}
