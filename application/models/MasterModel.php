<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterModel extends CI_Model {

    /* ==================== GENERIC ==================== */
    public function yearData(){
        return $this->db->get('tb_year');
    }

    public function lastCustomer(){
        return $this->db->select('*')
                        ->from('tb_customer c')
                        ->order_by('c.c_id','DESC')
                        ->limit(1)
                        ->get();
    }

    /* ==================== FORMULAS ==================== */
    public function formulasData($type){
        return $this->db->get_where('tb_formulas', ['f_name' => $type]);
    }

    public function formulasUpdate($key,$data){
        return $this->db->where('f_name', $key)->update('tb_formulas', $data);
    }

    /* ==================== CUSTOMER CRUD ==================== */
    public function customerDetail($id){
        return $this->db->get_where('tb_customer', ['c_id' => (int)$id]);
    }

    public function customerDatas($idCustomer){
        return $this->db->get_where('tb_customer', ['c_id' => (int)$idCustomer]);
    }

    public function customerData(){
        return $this->db->from('tb_customer a')
                        ->where('a.c_id_number !=', '999999999999999')
                        ->order_by('a.c_name', 'ASC')
                        ->get();
    }

    public function customerAdd(array $data){
        $this->db->insert('tb_customer', $data);
        return $this->db->affected_rows() > 0;
    }

    // dipertahankan ejaan fungsi lama agar kompatibel dengan controller
    public function editCustomerProces(array $data, $idCustomer){
        return $this->db->where('c_id', (int)$idCustomer)->update('tb_customer', $data);
    }

    public function deleteCustomerProcess($id){
        return $this->db->delete('tb_customer', ['c_id' => (int)$id]);
    }

    /** Cek duplikat nomor KTP */
    public function customerExistsByIdNumber(string $idNumber, $excludeId = null): bool
    {
        $this->db->from('tb_customer')->where('c_id_number', $idNumber);
        if (!empty($excludeId)) {
            $this->db->where('c_id <>', (int)$excludeId);
        }
        return $this->db->count_all_results() > 0;
    }

    /* ==================== DATATABLES: CUSTOMER ==================== */
    public function dtCustomers($start, $length, $search, $orderBy, $dir)
    {
        // kolom yang diijinkan untuk orderBy
        $allowedOrder = [
            'c_id','c_no_order','c_id_number','c_name','c_address','c_resident_address',
            'c_phone','c_email','c_date_created','u_name'
        ];
        if (!in_array($orderBy, $allowedOrder, true)) {
            $orderBy = 'c_no_order';
        }
        $dir = (strtoupper($dir)==='DESC') ? 'DESC' : 'ASC';

        // total
        $total = $this->db->from('tb_customer c')
                          ->where('c.c_id_number !=','999999999999999')
                          ->count_all_results();

        // filtered count
        $qb = $this->db->from('tb_customer c')
                       ->join('tb_user u','u.u_id=c.c_u_id','left')
                       ->where('c.c_id_number !=','999999999999999');

        if ($search !== '') {
            $qb->group_start()
                ->like('c.c_name', $search)
                ->or_like('c.c_no_order', $search)
                ->or_like('c.c_phone', $search)
                ->or_like('c.c_email', $search)
                ->or_like('c.c_id_number', $search)
            ->group_end();
        }
        $filtered = $qb->count_all_results();

        // data page
        $rows = $this->db->select('c.*, u.u_name')
                         ->from('tb_customer c')
                         ->join('tb_user u','u.u_id=c.c_u_id','left')
                         ->where('c.c_id_number !=','999999999999999')
                         ->order_by($orderBy, $dir)
                         ->limit((int)$length, (int)$start)
                         ->get()->result();

        return ['total'=>$total, 'filtered'=>$filtered, 'rows'=>$rows];
    }

    /* ==================== EXPORT: CUSTOMER ==================== */
    /**
     * Data untuk Export Excel (lengkap + join created_by)
     * $filters opsional: ['created_from' => 'YYYY-mm-dd', 'created_to' => 'YYYY-mm-dd']
     */
    public function customersForExport(array $filters = []): array
    {
        $this->db->select("
            c.c_id,
            c.c_no_order,
            c.c_id_number,
            c.c_name,
            c.c_address,
            c.c_resident_address,
            c.c_phone,
            c.c_email,
            c.c_date_created,
            u.u_name
        ");
        $this->db->from('tb_customer c');
        $this->db->join('tb_user u', 'u.u_id = c.c_u_id', 'left');
        $this->db->where('c.c_id_number !=', '999999999999999');

        if (!empty($filters['created_from'])) {
            $this->db->where('c.c_date_created >=', $filters['created_from']);
        }
        if (!empty($filters['created_to'])) {
            $this->db->where('c.c_date_created <', $filters['created_to']);
        }

        $this->db->order_by('c.c_id', 'ASC');
        return $this->db->get()->result_array();
    }

    /** Alias agar kompatibel dengan pemanggilan lama */
    public function getAllCustomers(): array
    {
        return $this->customersForExport();
    }

    /* ==================== CABANG & MEMO (apa adanya) ==================== */
    public function dtMemos($start, $length, $search, $orderBy, $dir)
    {
        $total = $this->db->from('tb_memo')->count_all_results();

        $qb = $this->db->from('tb_memo');
        if ($search !== '') {
            $qb->group_start()
               ->like('tm_value', $search)
               ->or_like('tm_priority', $search)
               ->group_end();
        }
        $filtered = $qb->count_all_results();

        $rows = $this->db->from('tb_memo')
                         ->order_by($orderBy, $dir)
                         ->limit((int)$length, (int)$start)
                         ->get()->result();

        return ['total'=>$total, 'filtered'=>$filtered, 'rows'=>$rows];
    }

    public function dtCabang($start, $length, $search, $orderBy, $dir)
    {
        $total = $this->db->from('tb_cabang')->where('status','ENABLE')->count_all_results();

        $qb = $this->db->from('tb_cabang')->where('status','ENABLE');
        if ($search !== '') {
            $qb->group_start()
               ->like('nama_cabang', $search)
               ->or_like('alamat_cabang', $search)
               ->group_end();
        }
        $filtered = $qb->count_all_results();

        $rows = $this->db->from('tb_cabang')
                         ->where('status','ENABLE')
                         ->order_by($orderBy, $dir)
                         ->limit((int)$length, (int)$start)
                         ->get()->result();

        return ['total'=>$total, 'filtered'=>$filtered, 'rows'=>$rows];
    }

    public function searchCustomers($search = '', $limit = 10, $offset = 0)
    {
        $this->db->select('c_id, c_name, c_id_number');
        $this->db->from('tb_customer');

        if (!empty($search)) {
            $this->db->group_start()
                ->like('c_name', $search)
                ->or_like('c_id_number', $search)
                ->or_like('c_id', $search)
            ->group_end();
        }

        $this->db->order_by('c_name', 'ASC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    // Hitung total hasil pencarian
    public function countCustomers($search = '')
    {
        $this->db->from('tb_customer');

        if (!empty($search)) {
            $this->db->group_start()
                ->like('c_name', $search)
                ->or_like('c_id_number', $search)
                ->or_like('c_id', $search)
            ->group_end();
        }

        return $this->db->count_all_results();
    }

    public function getCustomerById($id)
    {
        return $this->db->where('c_id', $id)->get('tb_customer');
    }
}
