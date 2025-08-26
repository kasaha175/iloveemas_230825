<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerModel extends CI_Model
{
    private $table = 'tb_customer'; // ganti bila berbeda

    public function countAllCustomers()
    {
        return (int) $this->db->count_all($this->table);
    }
}
