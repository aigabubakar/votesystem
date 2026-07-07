<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin – Dashboard controller.
 */
#[\AllowDynamicProperties]
class Admin extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/admin/');
        $this->load->model('Admin_model');
    }

    public function index(): void
    {
        $this->dashboard();
    }

    public function dashboard(): void
    {
        $stats           = $this->Admin_model->get_stats();
        $recent_elections = $this->Admin_model->get_recent_elections(5);

        $this->render('admin/dashboard', [
            'page_title'       => 'Dashboard – Admin',
            'stats'            => $stats,
            'recent_elections' => $recent_elections,
            'active_nav'       => 'dashboard',
        ]);
    }
}
