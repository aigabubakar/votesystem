<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once BASEPATH . 'core/Model.php';

/**
 * MY_Model – Base model providing common CRUD helpers.
 */
#[\AllowDynamicProperties]
class MY_Model extends CI_Model
{
    protected string $table    = '';
    protected string $primary  = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    // ----------------------------------------------------------------
    // Generic CRUD
    // ----------------------------------------------------------------

    public function get_all(array $where = [], string $order_by = 'id', string $direction = 'ASC'): array
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->order_by($order_by, $direction)->get($this->table)->result_array();
    }

    public function get_by_id(int $id): ?array
    {
        $row = $this->db->get_where($this->table, [$this->primary => $id])->row_array();
        return $row ?: null;
    }

    public function get_one(array $where): ?array
    {
        $row = $this->db->get_where($this->table, $where)->row_array();
        return $row ?: null;
    }

    public function insert(array $data): int|false
    {
        $this->db->insert($this->table, $data);
        return $this->db->affected_rows() > 0 ? (int) $this->db->insert_id() : false;
    }

    public function update(int $id, array $data): bool
    {
        $this->db->where($this->primary, $id)->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete(int $id): bool
    {
        $this->db->delete($this->table, [$this->primary => $id]);
        return $this->db->affected_rows() > 0;
    }

    public function count(array $where = []): int
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        return (int) $this->db->count_all_results($this->table);
    }
}
