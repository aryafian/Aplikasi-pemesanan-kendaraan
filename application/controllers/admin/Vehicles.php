<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicles extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Vehicle_model');

        // Security check: ensure user is logged in and is an admin
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        if ($this->session->userdata('role') != 'admin') {
            // Or show a 'permission denied' page
            redirect('dashboard'); 
        }
    }

    /**
     * Display a list of all vehicles.
     */
    public function index()
    {
        $data['title'] = 'Manage Vehicles';
        $data['vehicles'] = $this->Vehicle_model->get_all();

        $this->load->view('templates/header', $data);
        $this->load->view('admin/vehicles/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Show the form for creating a new vehicle or process the form submission.
     */
    public function create()
    {
        $this->form_validation->set_rules('name', 'Vehicle Name', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required');
        $this->form_validation->set_rules('ownership', 'Ownership', 'required');
        $this->form_validation->set_rules('license_plate', 'License Plate', 'required|is_unique[vehicles.license_plate]');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Add New Vehicle';
            $this->load->view('templates/header', $data);
            $this->load->view('admin/vehicles/create');
            $this->load->view('templates/footer');
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'type' => $this->input->post('type'),
                'ownership' => $this->input->post('ownership'),
                'license_plate' => $this->input->post('license_plate'),
                'service_schedule' => $this->input->post('service_schedule'),
            ];
            $this->Vehicle_model->insert($data);
            $this->session->set_flashdata('message', 'Vehicle added successfully!');
            redirect('admin/vehicles');
        }
    }

    /**
     * Show the form for editing a vehicle or process the form submission.
     * @param int $id The vehicle ID.
     */
    public function edit($id)
    {
        $vehicle = $this->Vehicle_model->get_by_id($id);
        if (!$vehicle) {
            show_404();
        }

        // Check if the license plate is being changed and if the new one is unique
        $original_plate = $vehicle->license_plate;
        $is_unique = '';
        if ($this->input->post('license_plate') != $original_plate) {
            $is_unique = '|is_unique[vehicles.license_plate]';
        }
        $this->form_validation->set_rules('license_plate', 'License Plate', 'required' . $is_unique);
        $this->form_validation->set_rules('name', 'Vehicle Name', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Vehicle';
            $data['vehicle'] = $vehicle;
            $this->load->view('templates/header', $data);
            $this->load->view('admin/vehicles/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $update_data = [
                'name' => $this->input->post('name'),
                'type' => $this->input->post('type'),
                'ownership' => $this->input->post('ownership'),
                'license_plate' => $this->input->post('license_plate'),
                'service_schedule' => $this->input->post('service_schedule'),
                'is_available' => $this->input->post('is_available'),
            ];
            $this->Vehicle_model->update($id, $update_data);
            $this->session->set_flashdata('message', 'Vehicle updated successfully!');
            redirect('admin/vehicles');
        }
    }

    /**
     * Delete a vehicle.
     * @param int $id The vehicle ID.
     */
    public function delete($id)
    {
        $this->Vehicle_model->delete($id);
        $this->session->set_flashdata('message', 'Vehicle deleted successfully!');
        redirect('admin/vehicles');
    }
}
