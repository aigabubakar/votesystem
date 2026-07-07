<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Candidates extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/admin/');
        $this->load->model('Admin_model');
    }

    public function index(): void
    {
        $candidates = $this->Admin_model->get_candidates();
        $elections  = $this->Admin_model->get_elections();
        $this->render('admin/candidates/index', [
            'page_title' => 'Manage Candidates',
            'candidates' => $candidates,
            'elections'  => $elections,
            'active_nav' => 'candidates',
        ]);
    }

    public function by_election(int $election_id): void
    {
        $election   = $this->Admin_model->get_election($election_id);
        $candidates = $this->Admin_model->get_candidates(['election_id' => $election_id]);
        $this->render('admin/candidates/index', [
            'page_title' => 'Candidates – ' . ($election['title'] ?? ''),
            'candidates' => $candidates,
            'election'   => $election,
            'elections'  => $this->Admin_model->get_elections(),
            'active_nav' => 'candidates',
        ]);
    }

    public function create(): void
    {
        $elections = $this->Admin_model->get_elections();
        $students  = $this->Admin_model->get_students();

        $positions = [];
        $selected_election = (int) $this->input->post('election_id');
        if ($selected_election) {
            $positions = $this->Admin_model->get_positions($selected_election);
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('election_id',  'Election',  'required|integer');
            $this->form_validation->set_rules('position_id',  'Position',  'required|integer');
            $this->form_validation->set_rules('user_id',      'Student',   'required|integer');

            if ($this->form_validation->run()) {
                $election_id = (int) $this->input->post('election_id');
                $position_id = (int) $this->input->post('position_id');
                $user_id     = (int) $this->input->post('user_id');

                if ($this->Admin_model->candidate_exists($election_id, $position_id, $user_id)) {
                    $this->flash_error('This student is already a candidate for this position.');
                } else {
                    // Handle photo upload
                    $photo = null;
                    if (!empty($_FILES['photo']['name'])) {
                        $photo = $this->_upload_photo();
                        if ($photo === null) {
                            $this->flash_error('Candidate photo failed to upload: ' . strip_tags($this->upload->display_errors()));
                            redirect('admin/candidates/create');
                        }
                    }

                    $data = [
                        'election_id' => $election_id,
                        'position_id' => $position_id,
                        'user_id'     => $user_id,
                        'manifesto'   => $this->input->post('manifesto', true),
                        'status'      => $this->input->post('status', true) ?: 'approved',
                        'photo'       => $photo,
                    ];

                    if ($this->Admin_model->save_candidate($data)) {
                        $this->flash_success('Candidate added successfully.');
                        redirect('admin/candidates/election/' . $election_id);
                    } else {
                        $this->flash_error('Failed to add candidate.');
                    }
                }
            }
        }

        $this->render('admin/candidates/form', [
            'page_title'        => 'Add Candidate',
            'action'            => site_url('admin/candidates/create'),
            'elections'         => $elections,
            'students'          => $students,
            'positions'         => $positions,
            'selected_election' => $selected_election,
            'candidate'         => null,
            'active_nav'        => 'candidates',
        ]);
    }

    public function approve(int $id): void
    {
        $candidate = $this->Admin_model->get_candidate($id);
        $this->Admin_model->update_candidate_status($id, 'approved');
        $this->flash_success('Candidate approved.');
        redirect('back');
    }

    public function reject(int $id): void
    {
        $this->Admin_model->update_candidate_status($id, 'rejected');
        $this->flash_error('Candidate rejected.');
        redirect('back');
    }

    public function delete(int $id): void
    {
        $candidate = $this->Admin_model->get_candidate($id);
        $eid = $candidate['election_id'] ?? null;
        $this->Admin_model->delete_candidate($id);
        $this->flash_success('Candidate removed.');
        redirect($eid ? 'admin/candidates/election/' . $eid : 'admin/candidates');
    }

    private function _upload_photo(): ?string
    {
        $upload_path = FCPATH . 'assets/uploads/candidates/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 2048,
            'encrypt_name'  => true,
        ];

        $this->load->library('upload', $config);
        if ($this->upload->do_upload('photo')) {
            return 'assets/uploads/candidates/' . $this->upload->data('file_name');
        }
        return null;
    }
}
