<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Tokens extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->add_module_path(APPPATH . 'modules/admin/');
        $this->load->model('Admin_model');
    }

    public function index(): void
    {
        $tokens = $this->Admin_model->get_tokens();
        
        // Only get active/upcoming elections for generation assignment
        $elections = $this->db
            ->where_in('status', ['active', 'upcoming'])
            ->order_by('title', 'ASC')
            ->get('elections')
            ->result_array();

        $this->render('admin/tokens/index', [
            'page_title' => 'Manage Tokens',
            'tokens'     => $tokens,
            'elections'  => $elections,
            'active_nav' => 'tokens',
        ]);
    }

    public function generate(): void
    {
        if ($this->input->post()) {
            $amount = (int) $this->input->post('amount');
            $prefix = trim((string)$this->input->post('prefix'));
            $election_id = $this->input->post('election_id');
            $election_id = is_numeric($election_id) && $election_id > 0 ? (int)$election_id : null;

            if ($amount > 0 && $amount <= 5000) {
                $count = $this->Admin_model->generate_tokens($amount, $prefix, $election_id);
                $this->flash_success("$count tokens successfully generated.");
            } else {
                $this->flash_error("Please specify a valid amount (1 to 5000).");
            }
        }
        redirect('admin/tokens');
    }

    public function export(): void
    {
        $tokens = $this->Admin_model->get_tokens();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=tokens_' . date('Ymd_His') . '.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Token', 'Status', 'Election', 'Used By (Matric)', 'Used At', 'Generated At']);
        
        foreach ($tokens as $t) {
            fputcsv($output, [
                $t['token'],
                ucfirst($t['status']),
                $t['election_title'] ?? 'Global (All Elections)',
                $t['matric_number'] ?? 'N/A',
                $t['used_at'] ?? 'N/A',
                $t['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
