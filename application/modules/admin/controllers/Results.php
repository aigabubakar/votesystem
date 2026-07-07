<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Results extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/admin/');
        $this->load->model('Admin_model');
    }

    public function index(): void
    {
        $elections = $this->Admin_model->get_elections(['status' => 'closed']);
        $published = $this->Admin_model->get_elections(['status' => 'published']);

        $this->render('admin/results/index', [
            'page_title' => 'Election Results',
            'elections'  => array_merge($elections, $published),
            'active_nav' => 'results',
        ]);
    }

    public function view(int $election_id): void
    {
        $election     = $this->Admin_model->get_election($election_id);
        if (!$election) { $this->flash_error('Election not found.'); redirect('admin/results'); }

        $results      = $this->Admin_model->get_results($election_id);
        $statistics   = $this->Admin_model->get_election_statistics($election_id);

        // Group by position
        $grouped = [];
        foreach ($results as $row) {
            $pid = $row['position_id'];
            if (!isset($grouped[$pid])) {
                $grouped[$pid] = [
                    'position_title'    => $row['position_title'],
                    'total_votes_cast'  => $row['total_votes_cast'],
                    'candidates'        => [],
                ];
            }
            if ($row['candidate_id']) {
                $grouped[$pid]['candidates'][] = $row;
            }
        }

        $this->render('admin/results/view', [
            'page_title'   => 'Results – ' . $election['title'],
            'election'     => $election,
            'grouped'      => $grouped,
            'statistics'   => $statistics,
            'active_nav'   => 'results',
        ]);
    }

    public function publish(int $election_id): void
    {
        $election = $this->Admin_model->get_election($election_id);
        if (!$election) { $this->flash_error('Election not found.'); redirect('admin/results'); }
        
        // Ensure all positions have a declared winner before publishing
        $results = $this->Admin_model->get_results($election_id);
        $positions = [];
        $declared = [];
        foreach ($results as $row) {
            // Only require a winner if the position actually has approved candidates
            if (!empty($row['candidate_id'])) {
                $positions[$row['position_id']] = true;
                if ($row['is_winner']) {
                    $declared[$row['position_id']] = true;
                }
            }
        }
        
        if (count($positions) > count($declared)) {
            $this->flash_error('You cannot publish the results until a winner is declared for all positions.');
            redirect('admin/results/view/' . $election_id);
        }

        $this->Admin_model->change_election_status($election_id, 'published');
        $this->flash_success('Results published successfully. Students can now view the results.');
        redirect('admin/results/view/' . $election_id);
    }
    
    public function declare_winner(int $election_id, int $position_id, int $candidate_id): void
    {
        $election = $this->Admin_model->get_election($election_id);
        if (!$election || $election['status'] !== 'closed') {
            $this->flash_error('Invalid election or election not yet closed.');
            redirect('admin/results');
        }
        
        if ($this->Admin_model->declare_winner($position_id, $candidate_id)) {
            $this->flash_success('Winner declared successfully.');
        } else {
            $this->flash_error('Failed to declare winner.');
        }
        redirect('admin/results/view/' . $election_id);
    }
}
