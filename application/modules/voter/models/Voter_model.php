<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Voter_model extends MY_Model
{
    protected string $table   = 'votes';
    protected string $primary = 'id';

    // ----------------------------------------------------------------
    // Elections visible to a voter
    // ----------------------------------------------------------------

    public function get_active_elections(int $dept_id, ?int $token_election_id = null): array
    {
        $this->db
            ->select('e.*, d.name AS dept_name,
                     (SELECT COUNT(DISTINCT voter_id) FROM votes v WHERE v.election_id = e.id) AS total_voters,
                     (SELECT COUNT(*) FROM positions p WHERE p.election_id = e.id) AS total_positions')
            ->from('elections e')
            ->join('departments d', 'd.id = e.department_id', 'left')
            ->group_start()
                ->where('e.department_id', $dept_id)
                ->or_where('e.department_id IS NULL')
            ->group_end()
            ->where_in('e.status', ['active', 'published']);
            
        if ($token_election_id) {
            $this->db->where('e.id', $token_election_id);
        }

        return $this->db->order_by('e.start_date', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_election(int $id): ?array
    {
        $row = $this->db
            ->select('e.*, d.name AS dept_name')
            ->from('elections e')
            ->join('departments d', 'd.id = e.department_id', 'left')
            ->where('e.id', $id)
            ->get()->row_array();
        return $row ?: null;
    }

    // ----------------------------------------------------------------
    // Positions + Candidates for the ballot
    // ----------------------------------------------------------------

    public function get_ballot(int $election_id, int $voter_id): array
    {
        $positions = $this->db
            ->where('election_id', $election_id)
            ->order_by('sort_order')
            ->get('positions')
            ->result_array();

        foreach ($positions as &$pos) {
            // Get approved candidates for this position
            $pos['candidates'] = $this->db
                ->select('c.id, c.manifesto, c.photo, c.vote_count,
                          u.first_name, u.last_name, u.student_id, u.level, u.profile_photo,
                          d.name AS dept_name')
                ->from('candidates c')
                ->join('users u',       'u.id = c.user_id')
                ->join('departments d', 'd.id = u.department_id', 'left')
                ->where('c.election_id', $election_id)
                ->where('c.position_id', $pos['id'])
                ->where('c.status', 'approved')
                ->get()
                ->result_array();

            // Check if voter already voted for this position
            $pos['already_voted'] = (bool) $this->db
                ->where('election_id', $election_id)
                ->where('position_id', $pos['id'])
                ->where('voter_id', $voter_id)
                ->count_all_results('votes');

            // Their choice
            if ($pos['already_voted']) {
                $vote = $this->db
                    ->select('v.candidate_id, u.first_name, u.last_name')
                    ->from('votes v')
                    ->join('candidates c', 'c.id = v.candidate_id')
                    ->join('users u', 'u.id = c.user_id')
                    ->where('v.election_id', $election_id)
                    ->where('v.position_id', $pos['id'])
                    ->where('v.voter_id', $voter_id)
                    ->get()->row_array();
                $pos['my_vote'] = $vote;
            }
        }

        return $positions;
    }

    // ----------------------------------------------------------------
    // Cast a vote
    // ----------------------------------------------------------------

    public function cast_vote(int $election_id, int $position_id, int $candidate_id, int $voter_id): bool|string
    {
        // Check election is active
        $election = $this->get_election($election_id);
        if (!$election || $election['status'] !== 'active') {
            return 'This election is not currently active.';
        }

        // Prevent double voting
        $exists = $this->db
            ->where('election_id', $election_id)
            ->where('position_id', $position_id)
            ->where('voter_id', $voter_id)
            ->count_all_results('votes');

        if ($exists) {
            return 'You have already voted for this position.';
        }

        // Verify candidate belongs to this election/position and is approved
        $candidate = $this->db
            ->get_where('candidates', [
                'id'          => $candidate_id,
                'election_id' => $election_id,
                'position_id' => $position_id,
                'status'      => 'approved',
            ])->row_array();

        if (!$candidate) {
            return 'Invalid candidate selection.';
        }

        $this->db->trans_start();

        $this->db->insert('votes', [
            'election_id'  => $election_id,
            'position_id'  => $position_id,
            'candidate_id' => $candidate_id,
            'voter_id'     => $voter_id,
            'voted_at'     => date('Y-m-d H:i:s'),
        ]);

        // Increment candidate vote count
        $this->db->set('vote_count', 'vote_count + 1', false)
                 ->where('id', $candidate_id)
                 ->update('candidates');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    // ----------------------------------------------------------------
    // Check if voter has voted for any position in an election
    // ----------------------------------------------------------------

    public function get_voted_positions(int $election_id, int $voter_id): array
    {
        return $this->db
            ->select('position_id')
            ->where('election_id', $election_id)
            ->where('voter_id', $voter_id)
            ->get('votes')
            ->result_array();
    }

    // ----------------------------------------------------------------
    // Published Results for voters
    // ----------------------------------------------------------------

    public function get_results(int $election_id): array
    {
        $positions = $this->db
            ->where('election_id', $election_id)
            ->order_by('sort_order')
            ->get('positions')
            ->result_array();

        foreach ($positions as &$pos) {
            $pos['candidates'] = $this->db
                ->select('c.vote_count, c.photo, c.is_winner,
                          u.first_name, u.last_name, u.student_id, u.profile_photo')
                ->from('candidates c')
                ->join('users u', 'u.id = c.user_id')
                ->where('c.election_id', $election_id)
                ->where('c.position_id', $pos['id'])
                ->where('c.status', 'approved')
                ->order_by('c.vote_count', 'DESC')
                ->get()
                ->result_array();

            $pos['total_votes'] = array_sum(array_column($pos['candidates'], 'vote_count'));
        }

        return $positions;
    }
}
