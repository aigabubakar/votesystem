<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller – handles login, logout, and registration.
 */
#[\AllowDynamicProperties]
class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/auth/');
        $this->load->model('Auth_model');
    }

    // ----------------------------------------------------------------
    // Index – redirect to login
    // ----------------------------------------------------------------
    public function index(): void
    {
        redirect('login');
    }

    // ----------------------------------------------------------------
    // Login
    // ----------------------------------------------------------------
    public function login(): void
    {
        // Already logged in?
        if ($this->is_logged_in()) {
            $this->_redirect_by_role();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('student_id', 'Matric Number', 'required|trim');
            $this->form_validation->set_rules('password',   'Voting Token / Password',   'required');

            if ($this->form_validation->run()) {
                $student_id = $this->input->post('student_id', true);
                $credential = trim($this->input->post('password'));

                $user = $this->Auth_model->attempt_login($student_id, $credential);

                if ($user) {
                    $this->_set_session($user, clone_token: $credential);
                    $this->flash_success('Welcome back, ' . $user['first_name'] . '!');
                    $this->_redirect_by_role();
                } else {
                    $this->flash_error('Invalid Matric Number or Token. Please try again.');
                }
            }
        }

        $this->render_auth('login', [
            'page_title' => 'Login – NUNSA Vote',
        ]);
    }

    // ----------------------------------------------------------------
    // Logout
    // ----------------------------------------------------------------
    public function logout(): void
    {
        $this->session->sess_destroy();
        $this->flash_info('You have been logged out successfully.');
        redirect('login');
    }

    // ----------------------------------------------------------------
    // Private Helpers
    // ----------------------------------------------------------------

    private function _set_session(array $user, string $clone_token = ''): void
    {
        $session_data = [
            'user_id'    => $user['id'],
            'user_name'  => $user['first_name'] . ' ' . $user['last_name'],
            'student_id' => $user['student_id'],
            'user_role'  => $user['role'],
            'user_email' => $user['email'],
            'dept_id'    => $user['department_id'],
            'user_photo' => $user['profile_photo'],
        ];

        if ($user['role'] === 'voter' && !empty($clone_token)) {
            $session_data['voting_token'] = $clone_token;
            if (isset($user['token_election_id'])) {
                $session_data['token_election_id'] = $user['token_election_id'];
            }
        }

        $this->session->set_userdata($session_data);
    }

    private function _redirect_by_role(): void
    {
        $role = $this->session->userdata('user_role');
        redirect($role === 'admin' ? 'admin' : 'voter');
    }
}
