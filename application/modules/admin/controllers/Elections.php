<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Elections – admin controller for election management.
 */
#[\AllowDynamicProperties]
class Elections extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/admin/');
        $this->load->model('Admin_model');
    }

    public function index(): void
    {
        $elections = $this->Admin_model->get_elections();
        $this->render('admin/elections/index', [
            'page_title' => 'Manage Elections',
            'elections'  => $elections,
            'active_nav' => 'elections',
        ]);
    }

    public function create(): void
    {
        $departments = $this->Admin_model->get_departments();

        if ($this->input->post()) {
            $this->_set_rules();
            if ($this->form_validation->run()) {
                $data = $this->_form_data();
                $data['created_by'] = $this->current_user()['id'];
                $data['status']     = 'pending';

                if ($this->Admin_model->save_election($data)) {
                    $this->flash_success('Election created successfully.');
                    redirect('admin/elections');
                } else {
                    $this->flash_error('Failed to create election. Please try again.');
                }
            }
        }

        $this->render('admin/elections/form', [
            'page_title'  => 'Create Election',
            'action'      => site_url('admin/elections/create'),
            'election'    => null,
            'departments' => $departments,
            'active_nav'  => 'elections',
        ]);
    }

    public function edit(int $id): void
    {
        $election    = $this->Admin_model->get_election($id);
        $departments = $this->Admin_model->get_departments();

        if (!$election) {
            $this->flash_error('Election not found.');
            redirect('admin/elections');
        }

        if ($this->input->post()) {
            $this->_set_rules();
            if ($this->form_validation->run()) {
                $data = $this->_form_data();
                $this->Admin_model->save_election($data, $id);
                $this->flash_success('Election updated successfully.');
                redirect('admin/elections');
            }
        }

        $this->render('admin/elections/form', [
            'page_title'  => 'Edit Election',
            'action'      => site_url('admin/elections/edit/' . $id),
            'election'    => $election,
            'departments' => $departments,
            'active_nav'  => 'elections',
        ]);
    }

    public function change_status(int $id, string $status): void
    {
        $allowed = ['pending', 'active', 'closed', 'published'];
        if (!in_array($status, $allowed, true)) {
            $this->flash_error('Invalid status.');
            redirect('admin/elections');
        }

        $this->Admin_model->change_election_status($id, $status);
        $this->flash_success('Election status updated to ' . ucfirst($status) . '.');
        redirect('admin/elections');
    }

    public function delete(int $id): void
    {
        if ($this->Admin_model->delete_election($id)) {
            $this->flash_success('Election deleted.');
        } else {
            $this->flash_error('Could not delete election.');
        }
        redirect('admin/elections');
    }

    private function _set_rules(): void
    {
        $this->form_validation->set_rules('title',         'Title',       'required|trim|max_length[200]');
        $this->form_validation->set_rules('description',   'Description', 'trim|max_length[1000]');
        $this->form_validation->set_rules('session',       'Session',     'required|trim|max_length[20]');
        $this->form_validation->set_rules('start_date',    'Start Date',  'required');
        $this->form_validation->set_rules('end_date',      'End Date',    'required|callback_check_end_date');
        $this->form_validation->set_rules('department_id', 'Department',  'integer');
    }

    private function _form_data(): array
    {
        return [
            'title'         => $this->input->post('title', true),
            'description'   => $this->input->post('description', true),
            'session'       => $this->input->post('session', true),
            'start_date'    => date('Y-m-d H:i:s', strtotime($this->input->post('start_date'))),
            'end_date'      => date('Y-m-d H:i:s', strtotime($this->input->post('end_date'))),
            'department_id' => $this->input->post('department_id') ?: null,
        ];
    }

    public function check_end_date(string $end_date): bool
    {
        $start_date = $this->input->post('start_date');
        
        if (strtotime($end_date) <= strtotime($start_date)) {
            $this->form_validation->set_message('check_end_date', 'The {field} must be after the Start Date.');
            return false;
        }
        
        return true;
    }
}
