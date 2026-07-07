<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin_model – central model for all admin operations.
 */
#[\AllowDynamicProperties]
class Admin_model extends MY_Model
{
    protected string $table   = 'users';
    protected string $primary = 'id';

    // ============================================================
    // DASHBOARD STATS
    // ============================================================

    public function get_stats(): array
    {
        return [
            'total_students'    => (int) $this->db->where('role', 'voter')->count_all_results('users'),
            'total_elections'   => (int) $this->db->count_all_results('elections'),
            'active_elections'  => (int) $this->db->where('status', 'active')->count_all_results('elections'),
            'total_votes'       => (int) $this->db->count_all_results('votes'),
            'total_candidates'  => (int) $this->db->where('status', 'approved')->count_all_results('candidates'),
            'total_departments' => (int) $this->db->count_all_results('departments'),
        ];
    }

    public function get_recent_elections(int $limit = 5): array
    {
        return $this->db
            ->select('e.*, d.name AS dept_name, u.first_name, u.last_name')
            ->from('elections e')
            ->join('departments d', 'd.id = e.department_id', 'left')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->order_by('e.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    // ============================================================
    // DEPARTMENTS
    // ============================================================

    public function get_departments(): array
    {
        return $this->db->order_by('name')->get('departments')->result_array();
    }

    public function get_department(int $id): ?array
    {
        $row = $this->db->get_where('departments', ['id' => $id])->row_array();
        return $row ?: null;
    }

    public function save_department(array $data, ?int $id = null): bool|int
    {
        if ($id) {
            $this->db->where('id', $id)->update('departments', $data);
            return $this->db->affected_rows() > 0;
        }
        $this->db->insert('departments', $data);
        return $this->db->affected_rows() > 0 ? (int) $this->db->insert_id() : false;
    }

    public function delete_department(int $id): bool
    {
        $this->db->delete('departments', ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    // ============================================================
    // STUDENTS / USERS
    // ============================================================

    public function get_students(array $filters = []): array
    {
        $this->db->select('u.*, d.name AS dept_name')
                 ->from('users u')
                 ->join('departments d', 'd.id = u.department_id', 'left')
                 ->where('u.role', 'voter');

        if (!empty($filters['dept_id'])) {
            $this->db->where('u.department_id', $filters['dept_id']);
        }
        if (!empty($filters['search'])) {
            $s = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                     ->like('u.first_name', $s, 'both')
                     ->or_like('u.last_name', $s, 'both')
                     ->or_like('u.student_id', $s, 'both')
                     ->group_end();
        }

        return $this->db->order_by('u.last_name')->get()->result_array();
    }

    public function get_student(int $id): ?array
    {
        $row = $this->db
            ->select('u.*, d.name AS dept_name')
            ->from('users u')
            ->join('departments d', 'd.id = u.department_id', 'left')
            ->where('u.id', $id)
            ->get()->row_array();
        return $row ?: null;
    }

    public function save_student(array $data, ?int $id = null): bool|int
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }

        if ($id) {
            $this->db->where('id', $id)->update('users', $data);
            return $this->db->affected_rows() >= 0;
        }
        $data['role']       = 'voter';
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('users', $data);
        return $this->db->affected_rows() > 0 ? (int) $this->db->insert_id() : false;
    }

    public function toggle_student_status(int $id): bool
    {
        $this->db->set('is_active', 'IF(is_active=1, 0, 1)', false)
                 ->where('id', $id)
                 ->update('users');
        return $this->db->affected_rows() > 0;
    }

    public function delete_student(int $id): bool
    {
        $this->db->delete('users', ['id' => $id, 'role' => 'voter']);
        return $this->db->affected_rows() > 0;
    }

    public function student_id_taken(string $student_id, ?int $exclude_id = null): bool
    {
        $this->db->where('student_id', $student_id);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return (bool) $this->db->count_all_results('users');
    }

    public function email_taken(string $email, ?int $exclude_id = null): bool
    {
        $this->db->where('email', $email);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return (bool) $this->db->count_all_results('users');
    }

    // ============================================================
    // ELECTIONS
    // ============================================================

    public function get_elections(array $filters = []): array
    {
        $this->db->select('e.*, d.name AS dept_name')
                 ->from('elections e')
                 ->join('departments d', 'd.id = e.department_id', 'left');

        if (!empty($filters['status'])) {
            $this->db->where('e.status', $filters['status']);
        }

        return $this->db->order_by('e.created_at', 'DESC')->get()->result_array();
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

    public function save_election(array $data, ?int $id = null): bool|int
    {
        if ($id) {
            $this->db->where('id', $id)->update('elections', $data);
            return $this->db->affected_rows() >= 0;
        }
        $this->db->insert('elections', $data);
        return $this->db->affected_rows() > 0 ? (int) $this->db->insert_id() : false;
    }

    public function change_election_status(int $id, string $status): bool
    {
        $this->db->where('id', $id)->update('elections', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    public function delete_election(int $id): bool
    {
        $this->db->delete('elections', ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    // ============================================================
    // POSITIONS
    // ============================================================

    public function get_positions(int $election_id): array
    {
        return $this->db
            ->where('election_id', $election_id)
            ->order_by('sort_order')
            ->get('positions')
            ->result_array();
    }

    public function get_position(int $id): ?array
    {
        $row = $this->db->get_where('positions', ['id' => $id])->row_array();
        return $row ?: null;
    }

    public function save_position(array $data, ?int $id = null): bool|int
    {
        if ($id) {
            $this->db->where('id', $id)->update('positions', $data);
            return $this->db->affected_rows() >= 0;
        }
        $this->db->insert('positions', $data);
        return $this->db->affected_rows() > 0 ? (int) $this->db->insert_id() : false;
    }

    public function delete_position(int $id): bool
    {
        $this->db->delete('positions', ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    // ============================================================
    // CANDIDATES
    // ============================================================

    public function get_candidates(array $filters = []): array
    {
        $this->db->select('c.*, u.first_name, u.last_name, u.student_id, u.profile_photo,
                           p.title AS position_title, e.title AS election_title, d.name AS dept_name')
                 ->from('candidates c')
                 ->join('users u',      'u.id = c.user_id')
                 ->join('positions p',  'p.id = c.position_id')
                 ->join('elections e',  'e.id = c.election_id')
                 ->join('departments d','d.id = e.department_id', 'left');

        if (!empty($filters['election_id'])) {
            $this->db->where('c.election_id', $filters['election_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('c.status', $filters['status']);
        }

        return $this->db->order_by('p.sort_order, u.last_name')->get()->result_array();
    }

    public function get_candidate(int $id): ?array
    {
        $row = $this->db
            ->select('c.*, u.first_name, u.last_name, u.student_id, u.email,
                      p.title AS position_title, e.title AS election_title')
            ->from('candidates c')
            ->join('users u',     'u.id = c.user_id')
            ->join('positions p', 'p.id = c.position_id')
            ->join('elections e', 'e.id = c.election_id')
            ->where('c.id', $id)
            ->get()->row_array();
        return $row ?: null;
    }

    public function save_candidate(array $data, ?int $id = null): bool|int
    {
        if ($id) {
            $this->db->where('id', $id)->update('candidates', $data);
            return $this->db->affected_rows() >= 0;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('candidates', $data);
        return $this->db->affected_rows() > 0 ? (int) $this->db->insert_id() : false;
    }

    public function update_candidate_status(int $id, string $status): bool
    {
        $this->db->where('id', $id)->update('candidates', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    public function delete_candidate(int $id): bool
    {
        $this->db->delete('candidates', ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    public function candidate_exists(int $election_id, int $position_id, int $user_id, ?int $exclude_id = null): bool
    {
        $this->db->where('election_id', $election_id)
                 ->where('position_id', $position_id)
                 ->where('user_id', $user_id);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return (bool) $this->db->count_all_results('candidates');
    }

    // ============================================================
    // RESULTS
    // ============================================================

    public function get_results(int $election_id): array
    {
        return $this->db
            ->select('p.id AS position_id, p.title AS position_title,
                      c.id AS candidate_id, c.vote_count, c.photo, c.is_winner,
                      u.first_name, u.last_name, u.student_id, u.profile_photo,
                      (SELECT COUNT(*) FROM votes v WHERE v.position_id = p.id) AS total_votes_cast')
            ->from('positions p')
            ->join('candidates c', 'c.position_id = p.id AND c.election_id = p.election_id AND c.status = \'approved\'', 'left')
            ->join('users u', 'u.id = c.user_id', 'left')
            ->where('p.election_id', $election_id)
            ->order_by('p.sort_order, c.vote_count DESC')
            ->get()
            ->result_array();
    }

    public function get_voters_count(int $election_id): int
    {
        return (int) $this->db
            ->select('voter_id')
            ->distinct()
            ->where('election_id', $election_id)
            ->count_all_results('votes');
    }

    public function declare_winner(int $position_id, int $candidate_id): bool
    {
        $this->db->where('position_id', $position_id)->update('candidates', ['is_winner' => 0]);
        $this->db->where('id', $candidate_id)->update('candidates', ['is_winner' => 1]);
        return $this->db->affected_rows() >= 0;
    }

    public function get_election_statistics(int $election_id): array
    {
        $total_registered = (int) $this->db->where('role', 'voter')->count_all_results('users');
        $total_voted = $this->get_voters_count($election_id);
        
        $dept_stats = $this->db->query("
            SELECT d.name as dept_name, 
                   COUNT(DISTINCT v.voter_id) as voted_count,
                   (SELECT COUNT(*) FROM users u2 WHERE u2.department_id = d.id AND u2.role = 'voter') as total_students
            FROM departments d
            LEFT JOIN users u ON u.department_id = d.id AND u.role = 'voter'
            LEFT JOIN votes v ON v.voter_id = u.id AND v.election_id = ?
            GROUP BY d.id, d.name
            ORDER BY d.name ASC
        ", [$election_id])->result_array();

        return [
            'total_registered' => $total_registered,
            'total_voted'      => $total_voted,
            'turnout_pct'      => $total_registered > 0 ? round(($total_voted / $total_registered) * 100, 1) : 0,
            'dept_stats'       => $dept_stats
        ];
    }

    // ============================================================
    // TOKENS
    // ============================================================

    public function get_tokens(): array
    {
        return $this->db
            ->select('tokens.*, elections.title as election_title')
            ->from('tokens')
            ->join('elections', 'elections.id = tokens.election_id', 'left')
            ->order_by('tokens.created_at', 'DESC')
            ->get()
            ->result_array();
    }

    public function generate_tokens(int $amount, string $prefix = '', ?int $election_id = null): int
    {
        $count = 0;
        
        // Clean prefix to alphanumeric and uppercase
        $prefix = preg_replace('/[^A-Za-z0-9-_]/', '', $prefix);
        $prefix = strtoupper($prefix);
        if (!empty($prefix) && substr($prefix, -1) !== '-') {
            $prefix .= '-'; // Auto-append hyphen if they provided a prefix without one
        }

        for ($i = 0; $i < $amount; $i++) {
            $random_str = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $token = $prefix . $random_str;
            
            // Ensure unique
            while ($this->db->where('token', $token)->count_all_results('tokens') > 0) {
                $random_str = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                $token = $prefix . $random_str;
            }

            $this->db->insert('tokens', [
                'token'       => $token,
                'status'      => 'unused',
                'election_id' => $election_id
            ]);
            $count++;
        }
        return $count;
    }
}
