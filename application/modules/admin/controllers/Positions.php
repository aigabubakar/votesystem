<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Positions extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/admin/');
        $this->load->model('Admin_model');
    }

    public function index(int $election_id): void
    {
        $election  = $this->Admin_model->get_election($election_id);
        if (!$election) { $this->flash_error('Election not found.'); redirect('admin/elections'); }

        $positions = $this->Admin_model->get_positions($election_id);
        $this->render('admin/positions/index', [
            'page_title' => 'Positions – ' . $election['title'],
            'election'   => $election,
            'positions'  => $positions,
            'active_nav' => 'elections',
        ]);
    }

    public function create(int $election_id): void
    {
        $election = $this->Admin_model->get_election($election_id);
        if (!$election) { $this->flash_error('Election not found.'); redirect('admin/elections'); }

        if ($this->input->post()) {
            $this->form_validation->set_rules('title',     'Title',     'required|trim|max_length[150]');
            $this->form_validation->set_rules('max_votes', 'Max Votes', 'required|integer|greater_than[0]');
            if ($this->form_validation->run()) {
                $data = [
                    'election_id' => $election_id,
                    'title'       => $this->input->post('title', true),
                    'description' => $this->input->post('description', true),
                    'max_votes'   => (int) $this->input->post('max_votes'),
                    'sort_order'  => (int) $this->input->post('sort_order', true) ?: 0,
                ];
                if ($this->Admin_model->save_position($data)) {
                    $this->flash_success('Position added.');
                    redirect('admin/positions/' . $election_id);
                }
            }
        }

        $this->render('admin/positions/form', [
            'page_title' => 'Add Position',
            'action'     => site_url('admin/positions/create/' . $election_id),
            'election'   => $election,
            'position'   => null,
            'active_nav' => 'elections',
        ]);
    }

    public function edit(int $id): void
    {
        $position = $this->Admin_model->get_position($id);
        if (!$position) { $this->flash_error('Position not found.'); redirect('admin/elections'); }
        $election = $this->Admin_model->get_election($position['election_id']);

        if ($this->input->post()) {
            $this->form_validation->set_rules('title',     'Title',     'required|trim|max_length[150]');
            $this->form_validation->set_rules('max_votes', 'Max Votes', 'required|integer|greater_than[0]');
            if ($this->form_validation->run()) {
                $data = [
                    'title'       => $this->input->post('title', true),
                    'description' => $this->input->post('description', true),
                    'max_votes'   => (int) $this->input->post('max_votes'),
                    'sort_order'  => (int) $this->input->post('sort_order', true) ?: 0,
                ];
                $this->Admin_model->save_position($data, $id);
                $this->flash_success('Position updated.');
                redirect('admin/positions/' . $position['election_id']);
            }
        }

        $this->render('admin/positions/form', [
            'page_title' => 'Edit Position',
            'action'     => site_url('admin/positions/edit/' . $id),
            'election'   => $election,
            'position'   => $position,
            'active_nav' => 'elections',
        ]);
    }

    public function delete(int $id): void
    {
        $position = $this->Admin_model->get_position($id);
        $eid = $position['election_id'] ?? null;
        $this->Admin_model->delete_position($id);
        $this->flash_success('Position deleted.');
        redirect($eid ? 'admin/positions/' . $eid : 'admin/elections');
    }
}
