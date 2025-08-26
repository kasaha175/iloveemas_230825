<?php
 if (!defined('BASEPATH'))
 	exit('No direct script access allowed');

class MasterModel extends CI_Model {

    function yearData(){
        $query = $this->db->query("SELECT *
        FROM tb_year");
        return $query;
    }
    function customerDetail($id){
        $query = $this->db->query("SELECT * FROM tb_customer WHERE c_id='$id'");
        return $query;
    }
    function editCustomerProces($data,$idCustomer){
        $this->db->where('c_id', $idCustomer);
        $this->db->update('tb_customer', $data);
    }
    function deleteCustomerProcess($id){
        $query = $this->db->query("DELETE FROM tb_customer WHERE c_id='$id'");
        return $query;
    }
    function lastCustomer(){
        $query = $this->db->query("SELECT *
        FROM tb_customer c
        ORDER BY c.c_id DESC
        LIMIT 1");
        return $query;
    }
    function formulasData($type){
        $query = $this->db->query("SELECT *
        FROM tb_formulas a
        WHERE a.f_name = '$type'");
        return $query;
    }
    function formulasUpdate($key,$data){
        $this->db->where('f_name', $key);
        $this->db->update('tb_formulas', $data);
    }
    function customerData(){
        $query = $this->db->query("SELECT *
        FROM tb_customer a
        WHERE a.c_id_number!='999999999999999'
        ORDER BY a.c_name ASC");
        return $query;
    }
    function customerAdd($data){
        $this->db->insert('tb_customer', $data);
    }
    function customerDatas($idCustomer){
        $query = $this->db->query("SELECT *
        FROM tb_customer a
        WHERE a.c_id = '$idCustomer'");
        return $query;
    }

    public function dtCustomers($start, $length, $search, $orderBy, $dir)
    {
        // total
        $total = $this->db->from('tb_customer c')
                        ->where('c.c_id_number !=', '999999999999999')
                        ->count_all_results();

        // filtered count
        $qb = $this->db->from('tb_customer c')
                    ->join('tb_user u','u.u_id=c.c_u_id','left')
                    ->where('c.c_id_number !=', '999999999999999');

        if ($search !== '') {
            $qb->group_start()
            ->like('c.c_name', $search)
            ->or_like('c.c_no_order', $search)
            ->or_like('c.c_phone', $search)
            ->group_end();
        }
        $filtered = $qb->count_all_results();

        // data
        $rows = $this->db->from('tb_customer c')
                        ->join('tb_user u','u.u_id=c.c_u_id','left')
                        ->where('c.c_id_number !=', '999999999999999')
                        ->order_by($orderBy, $dir)
                        ->limit($length, $start)
                        ->get()->result();

        return ['total'=>$total, 'filtered'=>$filtered, 'rows'=>$rows];
    }

}
?>