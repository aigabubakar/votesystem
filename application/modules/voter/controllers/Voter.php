<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Voter extends Voter_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/voter/');
        $this->load->model('Voter_model');
    }

    public function index(): void
    {
        $this->dashboard();
    }

    public function dashboard(): void
    {
        $user      = $this->current_user();
        $token_election_id = $this->session->userdata('token_election_id');
        $active_elections = $this->Voter_model->get_active_elections((int) $user['dept_id'], $token_election_id);

        // Mark which elections the voter has partially/fully voted in
        foreach ($active_elections as &$e) {
            $voted_positions = $this->Voter_model->get_voted_positions($e['id'], $user['id']);
            $e['voted_count'] = count($voted_positions);
            $e['is_fully_voted'] = $e['total_positions'] > 0 && $e['voted_count'] >= $e['total_positions'];
        }

        $student = $this->db->select('users.*, departments.name as dept_name')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->get_where('users', ['users.id' => $user['id']])->row_array();

        $voted_elections = $this->db
            ->select('v.election_id, e.title as election_title, e.status as election_status, MAX(v.voted_at) as voted_at')
            ->from('votes v')
            ->join('elections e', 'e.id = v.election_id')
            ->where('v.voter_id', $user['id'])
            ->group_by('v.election_id, e.title, e.status')
            ->get()->result_array();

        $this->render_voter('voter/dashboard', [
            'page_title'       => 'My Dashboard',
            'active_elections' => $active_elections,
            'student'          => $student,
            'voted_elections'  => $voted_elections,
            'active_nav'       => 'dashboard',
        ]);
    }

    public function profile(): void
    {
        $user = $this->current_user();
        $full = $this->db->get_where('users', ['id' => $user['id']])->row_array();

        if ($this->input->post()) {
            $this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[20]');
            $this->form_validation->set_rules('password', 'New Password', 'min_length[6]');

            if ($this->form_validation->run()) {
                $update = ['phone' => $this->input->post('phone', true)];
                $pw = $this->input->post('password');
                if ($pw) $update['password'] = password_hash($pw, PASSWORD_BCRYPT);

                $this->db->where('id', $user['id'])->update('users', $update);
                $this->flash_success('Profile updated.');
                redirect('voter/profile');
            }
        }

        $this->render_voter('voter/profile', [
            'page_title' => 'My Profile',
            'student'    => $full,
            'active_nav' => 'profile',
        ]);
    }

    public function results(): void
    {
        $user = $this->current_user();
        
        // Get published elections for this voter's department
        $published = $this->db
            ->select('e.*')
            ->from('elections e')
            ->group_start()
                ->where('e.department_id', (int)$user['dept_id'])
                ->or_where('e.department_id IS NULL')
            ->group_end()
            ->where('e.status', 'published')
            ->order_by('e.end_date', 'DESC')
            ->get()->result_array();

        // Format for the view
        foreach ($published as &$e) {
            $raw_results = $this->Voter_model->get_results($e['id']);
            $formatted_results = [];
            foreach ($raw_results as $pos) {
                $formatted_results[$pos['title']] = $pos['candidates'];
            }
            $e['results'] = $formatted_results;
        }

        $this->render_voter('voter/results', [
            'page_title' => 'Election Results',
            'elections'  => $published,
            'active_nav' => 'results'
        ]);
    }

    public function apply(int $election_id): void
    {
        $user     = $this->current_user();
        $election = $this->Voter_model->get_election($election_id);

        if (!$election || $election['status'] !== 'pending') {
            $this->flash_error('Applications are not open for this election.');
            redirect('voter');
        }

        $positions = $this->db
            ->where('election_id', $election_id)
            ->order_by('sort_order')
            ->get('positions')
            ->result_array();

        if ($this->input->post()) {
            $position_id = (int) $this->input->post('position_id');
            $manifesto   = $this->input->post('manifesto', true);

            // Check not already applied
            $existing = $this->db->get_where('candidates', [
                'election_id' => $election_id,
                'position_id' => $position_id,
                'user_id'     => $user['id'],
            ])->row_array();

            if ($existing) {
                $this->flash_error('You have already applied for this position.');
            } else {
                $this->db->insert('candidates', [
                    'election_id' => $election_id,
                    'position_id' => $position_id,
                    'user_id'     => $user['id'],
                    'manifesto'   => $manifesto,
                    'status'      => 'pending',
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
                $this->flash_success('Application submitted! Await admin approval.');
                redirect('voter');
            }
        }

        $this->render_voter('voter/apply', [
            'page_title' => 'Apply as Candidate – ' . $election['title'],
            'election'   => $election,
            'positions'  => $positions,
            'active_nav' => 'dashboard',
        ]);
    }
}
