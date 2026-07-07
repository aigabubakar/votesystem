<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Vote extends Voter_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/voter/');
        $this->load->model('Voter_model');
    }

    /**
     * Show the voting ballot for an election.
     */
    public function ballot(int $election_id): void
    {
        $user     = $this->current_user();
        $election = $this->Voter_model->get_election($election_id);

        if (!$election || $election['status'] !== 'active') {
            $this->flash_error('This election is not currently accepting votes.');
            redirect('voter');
        }

        $ballot = $this->Voter_model->get_ballot($election_id, $user['id']);

        $ballot_data = [];
        foreach ($ballot as $pos) {
            $pos['position_title'] = $pos['title'];
            $ballot_data[$pos['id']] = $pos;
        }

        $this->render_voter('voter/ballot', [
            'page_title'  => 'Vote – ' . $election['title'],
            'election'    => $election,
            'ballot_data' => $ballot_data,
            'active_nav'  => 'dashboard',
        ]);
    }

    /**
     * Process a single vote submission (AJAX-friendly; also works as POST).
     */
    public function cast(): void
    {
        $user = $this->current_user();

        $election_id  = (int) $this->input->post('election_id');
        $position_id  = (int) $this->input->post('position_id');
        $candidate_id = (int) $this->input->post('candidate_id');

        if (!$election_id || !$position_id || !$candidate_id) {
            $this->flash_error('Invalid vote submission.');
            redirect('voter');
        }

        $result = $this->Voter_model->cast_vote($election_id, $position_id, $candidate_id, $user['id']);

        if ($result === true) {
            // AJAX request?
            if ($this->input->is_ajax_request()) {
                $this->output
                     ->set_content_type('application/json')
                     ->set_output(json_encode(['success' => true, 'message' => 'Vote recorded!']));
                return;
            }
            $this->flash_success('Your vote has been recorded!');
        } else {
            $msg = is_string($result) ? $result : 'Failed to record vote.';
            if ($this->input->is_ajax_request()) {
                $this->output
                     ->set_content_type('application/json')
                     ->set_output(json_encode(['success' => false, 'message' => $msg]));
                return;
            }
            $this->flash_error($msg);
        }

        redirect('voter/ballot/' . $election_id);
    }

    /**
     * Process full ballot submission.
     */
    public function submit(int $election_id): void
    {
        $user = $this->current_user();
        
        $election = $this->Voter_model->get_election($election_id);
        if (!$election || $election['status'] !== 'active') {
            $this->flash_error('This election is not currently accepting votes.');
            redirect('voter');
        }

        $votes = $this->input->post('votes');
        if (empty($votes) || !is_array($votes)) {
            $this->flash_error('You must make selections before submitting your ballot.');
            redirect("voter/vote/ballot/{$election_id}");
        }

        $success_count = 0;
        $error_count = 0;

        foreach ($votes as $position_id => $selection) {
            $position_id = (int) $position_id;
            
            // Handle abstain
            if ($selection === 'abstain') {
                continue; // skipped
            }

            // Handle multiple selections (checkboxes) or single (radio)
            $candidate_ids = is_array($selection) ? $selection : [$selection];
            
            foreach ($candidate_ids as $candidate_id) {
                if ($candidate_id === 'abstain') continue;

                $candidate_id = (int) $candidate_id;
                if ($candidate_id > 0) {
                    $result = $this->Voter_model->cast_vote($election_id, $position_id, $candidate_id, $user['id']);
                    if ($result === true) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }
        }

        if ($success_count > 0 || in_array('abstain', $votes, true)) {
            $this->flash_success('Your ballot has been successfully submitted!');
            
            // Mark voting token as used!
            $token = $this->session->userdata('voting_token');
            if ($token) {
                $this->db->where('token', $token)->update('tokens', [
                    'status' => 'used',
                    'matric_number' => $user['student_id'],
                    'used_at' => date('Y-m-d H:i:s')
                ]);
                // Remove it from session so they can't use it again if they refresh
                $this->session->unset_userdata('voting_token');
            }
        } else {
            $this->flash_error('Failed to submit ballot or no valid selections made.');
        }

        redirect('voter');
    }

    /**
     * Show published results for an election.
     */
    public function results(int $election_id): void
    {
        $election = $this->Voter_model->get_election($election_id);

        if (!$election || $election['status'] !== 'published') {
            $this->flash_error('Results are not yet available for this election.');
            redirect('voter');
        }

        $positions = $this->Voter_model->get_results($election_id);

        $this->render_voter('voter/vote_results', [
            'page_title' => 'Results – ' . $election['title'],
            'election'   => $election,
            'positions'  => $positions,
            'active_nav' => 'dashboard',
        ]);
    }

    /**
     * Show voting receipt for an election.
     */
    public function receipt(int $election_id): void
    {
        $user = $this->current_user();
        $election = $this->Voter_model->get_election($election_id);

        if (!$election) {
            $this->flash_error('Election not found.');
            redirect('voter');
        }

        // Check if the user actually voted in this election
        $vote_record = $this->db
            ->select('MAX(voted_at) as voted_at')
            ->from('votes')
            ->where('election_id', $election_id)
            ->where('voter_id', $user['id'])
            ->get()
            ->row_array();

        if (empty($vote_record['voted_at'])) {
            $this->flash_error('No voting record found for this election.');
            redirect('voter');
        }

        $student = $this->db->get_where('users', ['id' => $user['id']])->row_array();

        $this->render_voter('voter/receipt', [
            'page_title' => 'Voting Receipt – ' . $election['title'],
            'election'   => $election,
            'student'    => $student,
            'voted_at'   => $vote_record['voted_at'],
            'active_nav' => 'dashboard',
        ]);
    }
}
