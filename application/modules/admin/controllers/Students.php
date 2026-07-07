<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Students extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/admin/');
        $this->load->model('Admin_model');
    }

    public function index(): void
    {
        $filters     = ['search' => $this->input->get('q'), 'dept_id' => $this->input->get('dept_id')];
        $students    = $this->Admin_model->get_students($filters);
        $departments = $this->Admin_model->get_departments();

        $this->render('admin/students/index', [
            'page_title'  => 'Manage Students',
            'students'    => $students,
            'departments' => $departments,
            'filters'     => $filters,
            'active_nav'  => 'students',
        ]);
    }

    public function create(): void
    {
        $departments = $this->Admin_model->get_departments();

        if ($this->input->post()) {
            $this->_set_rules(true);
            if ($this->form_validation->run()) {
                $student_id = $this->input->post('student_id', true);
                $email      = $this->input->post('email', true);

                if ($this->Admin_model->student_id_taken($student_id)) {
                    $this->flash_error('Student ID already exists.');
                } elseif ($this->Admin_model->email_taken($email)) {
                    $this->flash_error('Email address already exists.');
                } else {
                    $data = $this->_form_data(true);
                    if ($this->Admin_model->save_student($data)) {
                        $this->flash_success('Student created successfully.');
                        redirect('admin/students');
                    } else {
                        $this->flash_error('Failed to create student.');
                    }
                }
            }
        }

        $this->render('admin/students/form', [
            'page_title'  => 'Add Student',
            'action'      => site_url('admin/students/create'),
            'student'     => null,
            'departments' => $departments,
            'active_nav'  => 'students',
        ]);
    }

    public function edit(int $id): void
    {
        $student     = $this->Admin_model->get_student($id);
        $departments = $this->Admin_model->get_departments();

        if (!$student) { $this->flash_error('Student not found.'); redirect('admin/students'); }

        if ($this->input->post()) {
            $this->_set_rules(false);
            if ($this->form_validation->run()) {
                $email = $this->input->post('email', true);
                if ($this->Admin_model->email_taken($email, $id)) {
                    $this->flash_error('Email address already used.');
                } else {
                    $data = $this->_form_data(false);
                    $this->Admin_model->save_student($data, $id);
                    $this->flash_success('Student updated.');
                    redirect('admin/students');
                }
            }
        }

        $this->render('admin/students/form', [
            'page_title'  => 'Edit Student',
            'action'      => site_url('admin/students/edit/' . $id),
            'student'     => $student,
            'departments' => $departments,
            'active_nav'  => 'students',
        ]);
    }

    public function toggle(int $id): void
    {
        $this->Admin_model->toggle_student_status($id);
        $this->flash_success('Student status updated.');
        redirect('admin/students');
    }

    public function verify_matric(int $id): void
    {
        $this->db->update('users', ['is_matric_verified' => 1], ['id' => $id]);
        $this->flash_success('Student matriculation ID verified.');
        redirect('admin/students');
    }

    public function delete(int $id): void
    {
        if ($this->Admin_model->delete_student($id)) {
            $this->flash_success('Student deleted.');
        } else {
            $this->flash_error('Could not delete student.');
        }
        redirect('admin/students');
    }

    public function import(): void
    {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, "r");
            if ($handle !== FALSE) {
                // Skip header row
                fgetcsv($handle, 1000, ",");
                
                $success_count = 0;
                $error_count = 0;
                
                $departments = $this->Admin_model->get_departments();
                $dept_map = [];
                foreach ($departments as $d) {
                    $dept_map[strtolower(trim($d['name']))] = $d['id'];
                }

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) >= 6) {
                        $first_name = trim($data[0]);
                        $last_name = trim($data[1]);
                        $student_id = trim($data[2]);
                        $email = trim($data[3]);
                        $dept_name = strtolower(trim($data[4]));
                        $level = trim($data[5]);
                        
                        // Map department name to ID, default to first department if not found
                        $dept_id = $dept_map[$dept_name] ?? ($departments[0]['id'] ?? 0);
                        
                        if (!empty($first_name) && !empty($last_name) && !empty($student_id) && $dept_id > 0) {
                            if (!$this->Admin_model->student_id_taken($student_id)) {
                                $student_data = [
                                    'first_name' => $first_name,
                                    'last_name' => $last_name,
                                    'student_id' => $student_id,
                                    'email' => $email,
                                    'department_id' => $dept_id,
                                    'level' => $level,
                                    'gender' => 'Other', // default
                                    'is_active' => 1
                                ];
                                $this->Admin_model->save_student($student_data);
                                $success_count++;
                            } else {
                                $error_count++;
                            }
                        }
                    }
                }
                fclose($handle);
                $this->flash_success("Import complete: $success_count imported, $error_count skipped (already exists).");
            } else {
                $this->flash_error("Could not read CSV file.");
            }
        } else {
            $this->flash_error("Please upload a valid CSV file.");
        }
        redirect('admin/students');
    }

    private function _set_rules(bool $is_create): void
    {
        $this->form_validation->set_rules('first_name',   'First Name',   'required|trim|max_length[80]');
        $this->form_validation->set_rules('last_name',    'Last Name',    'required|trim|max_length[80]');
        $this->form_validation->set_rules('email',        'Email',        'required|trim|valid_email');
        $this->form_validation->set_rules('department_id','Department',   'required|integer');
        $this->form_validation->set_rules('level',        'Level',        'required|trim');
        $this->form_validation->set_rules('gender',       'Gender',       'required');

        if ($is_create) {
            $this->form_validation->set_rules('student_id', 'Student ID', 'required|trim|max_length[50]');
            $this->form_validation->set_rules('password',   'Password',   'required|min_length[6]');
        } else {
            $this->form_validation->set_rules('password',   'Password',   'min_length[6]');
        }
    }

    private function _form_data(bool $is_create): array
    {
        $data = [
            'first_name'    => $this->input->post('first_name', true),
            'last_name'     => $this->input->post('last_name', true),
            'email'         => $this->input->post('email', true),
            'phone'         => $this->input->post('phone', true),
            'department_id' => (int) $this->input->post('department_id'),
            'level'         => $this->input->post('level', true),
            'gender'        => $this->input->post('gender', true),
            'is_active'     => 1,
        ];

        if ($is_create) {
            $data['student_id'] = $this->input->post('student_id', true);
        }

        $pw = $this->input->post('password');
        if ($pw) $data['password'] = $pw;

        return $data;
    }
}
