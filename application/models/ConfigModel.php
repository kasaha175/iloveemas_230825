<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConfigModel extends CI_Model {

    private $table = 'tb_config';
    private $smtpTable = 'smtp_config';
    private $logTable  = 'log_email';

    // ---- General Config ----
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
                            ->update($this->table, [
                                'config_value'=>$value,
                                'updated_at'=>date('Y-m-d H:i:s')
                            ]);
        }
        return $this->db->insert($this->table, [
            'config_key'=>$key,
            'config_value'=>$value,
            'updated_at'=>date('Y-m-d H:i:s')
        ]);
    }

    public function setBatch($assoc){
        foreach ($assoc as $k=>$v) { $this->set($k, $v); }
        return true;
    }

    // ---- SMTP Config ----
    public function getSmtpActive(){
        return $this->db->where('is_active', 1)
                        ->order_by('id','desc')
                        ->get($this->smtpTable)
                        ->row();
    }

    public function saveSmtp($data){
        // deactivate others
        $this->db->update($this->smtpTable, ['is_active'=>0]);
        $data['is_active'] = 1;
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (!empty($data['id'])){
            $this->db->where('id',$data['id'])->update($this->smtpTable, $data);
            return $data['id'];
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->smtpTable, $data);
        return $this->db->insert_id();
    }

    // ---- Log Email ----
    public function logEmail($smtpId, $recipient, $subject, $body, $status, $error=null){
        return $this->db->insert($this->logTable, [
            'smtp_config_id' => $smtpId,
            'recipient'      => $recipient,
            'subject'        => $subject,
            'body'           => $body,
            'status'         => $status,
            'error_message'  => $error,
            'created_at'     => date('Y-m-d H:i:s')
        ]);
    }

    public function getLogs($limit=50){
        return $this->db->order_by('id','desc')
                        ->limit($limit)
                        ->get($this->logTable)
                        ->result();
    }
}
