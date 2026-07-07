<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Profile extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $session_user = $this->current_user();
        $user = $this->db->where('id', $session_user['id'])->get('users')->row_array();
        
        if ($this->input->post()) {
            $first_name = trim((string)$this->input->post('first_name'));
            $last_name = trim((string)$this->input->post('last_name'));
            $email = trim((string)$this->input->post('email'));
            $password = trim((string)$this->input->post('password'));

            if (empty($first_name) || empty($last_name)) {
                $this->flash_error("First Name and Last Name are required.");
                redirect('admin/profile');
            }

            $update_data = [
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'email'      => $email
            ];

            if (!empty($password)) {
                $update_data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $this->db->where('id', $user['id'])->update('users', $update_data);
            
            // Update session data
            $this->session->set_userdata('user_name', $first_name . ' ' . $last_name);
            $this->session->set_userdata('user_email', $email);

            $this->flash_success("Profile updated successfully.");
            redirect('admin/profile');
        }

        $this->render('admin/profile/index', [
            'page_title' => 'My Profile',
            'user'       => $user,
            'active_nav' => 'profile',
        ]);
    }
}
