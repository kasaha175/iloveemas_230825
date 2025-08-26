<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConfigModel extends CI_Model {

    private $table = 'tb_config';

    public function getAllAssoc(){
        $rows = $this->db->get($this->table)->result();
        $out = [];
        foreach ($rows as $r) $out[$r->config_key] = $r->config_value;
        return $out;
    }

    public function set($key, $value){
        $exists = $this->db->where('config_key', $key)->get($this->table)->row();
        if ($exists) {
            return $this->db->where('config_key',$key)
                            ->update($this->table, ['config_value'=>$value, 'updated_at'=>date('Y-m-d H:i:s')]);
        }
        return $this->db->insert($this->table, [
            'config_key'=>$key, 'config_value'=>$value, 'updated_at'=>date('Y-m-d H:i:s')
        ]);
    }

    public function setBatch($assoc){
        foreach ($assoc as $k=>$v) { $this->set($k, $v); }
        return true;
    }
}
