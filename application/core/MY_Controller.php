<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller – Base controller for all NUSSA Vote controllers.
 * Provides auth checks, flash messaging, and data helpers.
 */
require_once APPPATH . 'third_party/MX/Controller.php';

#[\AllowDynamicProperties]
class MY_Controller extends MX_Controller
{
    protected array $data = [];

    public function __construct()
    {
        parent::__construct();
        // Expose site config to all views
        $this->data['site_name']        = $this->config->item('site_name');
        $this->data['site_tagline']     = $this->config->item('site_tagline');
        $this->data['institution_name'] = $this->config->item('institution_name');
    }

    // ----------------------------------------------------------------
    // Auth Helpers
    // ----------------------------------------------------------------

    protected function is_logged_in(): bool
    {
        return (bool) $this->session->userdata('user_id');
    }

    protected function current_user(): ?array
    {
        if (!$this->is_logged_in()) {
            return null;
        }
        return [
            'id'         => $this->session->userdata('user_id'),
            'name'       => $this->session->userdata('user_name'),
            'student_id' => $this->session->userdata('student_id'),
            'role'       => $this->session->userdata('user_role'),
            'email'      => $this->session->userdata('user_email'),
            'dept_id'    => $this->session->userdata('dept_id'),
            'photo'      => $this->session->userdata('user_photo'),
        ];
    }

    protected function require_login(string $redirect = 'login'): void
    {
        if (!$this->is_logged_in()) {
            $this->session->set_flashdata('error', 'Please log in to access that page.');
            redirect($redirect);
        }

        // Concurrent login check
        $user_id = $this->session->userdata('user_id');
        $current_session_id = session_id();
        
        $db_session = $this->db->select('last_session_id')
                               ->where('id', $user_id)
                               ->get('users')
                               ->row('last_session_id');

        if ($db_session && $db_session !== $current_session_id) {
            $this->session->sess_destroy();
            // Start a new session so we can flash an error message
            session_start();
            $this->session->set_flashdata('error', 'You have been logged out because your account was accessed from another device.');
            redirect($redirect);
        }
    }

    protected function require_role(string $role): void
    {
        $this->require_login();
        if ($this->session->userdata('user_role') !== $role) {
            $this->session->set_flashdata('error', 'You do not have permission to access that page.');
            redirect($this->session->userdata('user_role') === 'admin' ? 'admin' : 'voter');
        }
    }

    // ----------------------------------------------------------------
    // Flash Message Helpers
    // ----------------------------------------------------------------

    protected function flash_success(string $msg): void
    {
        $this->session->set_flashdata('success', $msg);
    }

    protected function flash_error(string $msg): void
    {
        $this->session->set_flashdata('error', $msg);
    }

    protected function flash_info(string $msg): void
    {
        $this->session->set_flashdata('info', $msg);
    }

    protected function flash_warning(string $msg): void
    {
        $this->session->set_flashdata('warning', $msg);
    }

    // ----------------------------------------------------------------
    // View Render Helper
    // ----------------------------------------------------------------

    protected function render(string $view, array $data = [], string $layout = 'templates/admin_layout'): void
    {
        $data = array_merge($this->data, $data);
        $data['content_view'] = $view;
        $this->load->view($layout, $data);
    }

    protected function render_voter(string $view, array $data = []): void
    {
        $this->render($view, $data, 'templates/voter_layout');
    }

    protected function render_auth(string $view, array $data = []): void
    {
        $this->render($view, $data, 'templates/auth_layout');
    }
}


/**
 * Admin_Controller – requires admin role
 */
#[\AllowDynamicProperties]
class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role('admin');
        $this->data['current_user'] = $this->current_user();
    }
}


/**
 * Voter_Controller – requires voter role
 */
#[\AllowDynamicProperties]
class Voter_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role('voter');
        $this->data['current_user'] = $this->current_user();
    }
}
