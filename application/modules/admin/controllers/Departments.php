<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Departments extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/admin/');
        $this->load->model('Admin_model');
    }

    public function index(): void
    {
        $this->render('admin/departments/index', [
            'page_title'  => 'Manage Departments',
            'departments' => $this->Admin_model->get_departments(),
            'active_nav'  => 'departments',
        ]);
    }

    public function create(): void
    {
        if ($this->input->post()) {
            $this->_set_rules();
            if ($this->form_validation->run()) {
                $this->Admin_model->save_department($this->_form_data());
                $this->flash_success('Department created.');
                redirect('admin/departments');
            }
        }
        $this->render('admin/departments/form', [
            'page_title' => 'Add Department',
            'action'     => site_url('admin/departments/create'),
            'dept'       => null,
            'active_nav' => 'departments',
        ]);
    }

    public function edit(int $id): void
    {
        $dept = $this->Admin_model->get_department($id);
        if (!$dept) { $this->flash_error('Department not found.'); redirect('admin/departments'); }

        if ($this->input->post()) {
            $this->_set_rules();
            if ($this->form_validation->run()) {
                $this->Admin_model->save_department($this->_form_data(), $id);
                $this->flash_success('Department updated.');
                redirect('admin/departments');
            }
        }
        $this->render('admin/departments/form', [
            'page_title' => 'Edit Department',
            'action'     => site_url('admin/departments/edit/' . $id),
            'dept'       => $dept,
            'active_nav' => 'departments',
        ]);
    }

    public function delete(int $id): void
    {
        $this->Admin_model->delete_department($id);
        $this->flash_success('Department deleted.');
        redirect('admin/departments');
    }

    private function _set_rules(): void
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('code', 'Code', 'required|trim|max_length[20]');
    }

    private function _form_data(): array
    {
        return [
            'name'        => $this->input->post('name', true),
            'code'        => strtoupper($this->input->post('code', true)),
            'description' => $this->input->post('description', true),
        ];
    }
}
