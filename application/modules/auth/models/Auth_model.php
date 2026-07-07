<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth_model – handles user authentication.
 */
#[\AllowDynamicProperties]
class Auth_model extends MY_Model
{
    protected string $table   = 'users';
    protected string $primary = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Attempt login by student_id and password.
     * Returns user row on success, null on failure.
     */
    public function attempt_login(string $student_id, string $credential): ?array
    {
        $user = $this->db
            ->where('student_id', $student_id)
            ->where('is_active', 1)
            ->get('users')
            ->row_array();

        if (!$user) {
            return null;
        }

        $authenticated = false;

        if ($user['role'] === 'admin') {
            // Admins still use passwords
            $authenticated = password_verify($credential, $user['password']);
        } else {
            // Voters use tokens. Fraud prevention:
            // Find the provided token
            $provided_token = $this->db
                ->where('token', $credential)
                ->get('tokens')
                ->row_array();

            if ($provided_token) {
                if ($provided_token['matric_number'] === $student_id) {
                    // Token belongs to this student. Allow login even if used (to view results/receipt).
                    $authenticated = true;
                    $user['token_election_id'] = $provided_token['election_id'];
                } elseif (empty($provided_token['matric_number']) && $provided_token['status'] === 'unused') {
                    // Token is unassigned and unused. Check if student already has a token for THIS election.
                    $this->db->where('matric_number', $student_id);
                    if ($provided_token['election_id']) {
                        $this->db->where('election_id', $provided_token['election_id']);
                    } else {
                        $this->db->where('election_id IS NULL', null, false);
                    }
                    $existing_for_election = $this->db->get('tokens')->row_array();

                    if (!$existing_for_election) {
                        // Bind this token to the student permanently
                        $this->db->where('id', $provided_token['id'])->update('tokens', [
                            'matric_number' => $student_id
                        ]);
                        $authenticated = true;
                        $user['token_election_id'] = $provided_token['election_id'];
                    }
                }
            }
        }

        if ($authenticated) {
            // Update last_login and last_session_id
            $this->db->where('id', $user['id'])->update('users', [
                'last_login'      => date('Y-m-d H:i:s'),
                'last_session_id' => session_id(),
            ]);
            return $user;
        }
        return null;
    }

    /**
     * Register a new student voter.
     */
    public function register(array $data): int|false
    {
        $data['password']   = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role']       = 'voter';
        $data['is_active']  = 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }

    /**
     * Check if student_id already exists.
     */
    public function student_id_exists(string $student_id): bool
    {
        return (bool) $this->db
            ->where('student_id', $student_id)
            ->count_all_results('users');
    }

    /**
     * Check if email already exists.
     */
    public function email_exists(string $email, ?int $exclude_id = null): bool
    {
        $this->db->where('email', $email);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return (bool) $this->db->count_all_results('users');
    }
}
