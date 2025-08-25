<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model {

	// Deteksi nama kolom yang tersedia di tb_user
    private function mapColumns(){
        $cols = array_map('strtolower', $this->db->list_fields('tb_user'));

        $usernameCol = in_array('u_username', $cols) ? 'u_username' :
                       (in_array('username', $cols)   ? 'username'   : null);

        $passwordCol = in_array('u_password', $cols) ? 'u_password' :
                       (in_array('password', $cols)   ? 'password'   : null);

        $idCol = in_array('u_id', $cols) ? 'u_id' :
                 (in_array('id', $cols)   ? 'id'   : null);

        return (object)[
            'username' => $usernameCol,
            'password' => $passwordCol,
            'id'       => $idCol,
            // kolom lain opsional; jangan select jika ragu
        ];
    }

    public function cabangData(){
        return $this->db->get('tb_cabang');
    }

     public function findByUsername($username){
        $map = $this->mapColumns();
        if (!$map->username || !$map->password || !$map->id) {
            log_message('error', 'UserModel: kolom tidak lengkap di tb_user');
            return null;
        }

        // Hindari DB error jadi 500
        $prev = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $q = $this->db->select("{$map->id} AS u_id, {$map->username} AS u_username, {$map->password} AS u_password")
                      ->from('tb_user')
                      ->where($map->username, $username)
                      ->limit(1)
                      ->get();

        if ($q === FALSE) {
            $err = $this->db->error();
            log_message('error', 'DB error findByUsername: '.json_encode($err));
            $this->db->db_debug = $prev;
            return null;
        }

        $row = $q->row();
        $this->db->db_debug = $prev;
        return $row;
    }

    /** Legacy: kalau masih ada pemanggil lama, tetap aman (bound array) */
    public function userData($username, $password){
        return $this->db->get_where('tb_user', [
            'u_username' => $username,
            'u_password' => $password
        ]);
    }

    public function userDataById($user_id){
        return $this->db->select('a.*')
                        ->from('tb_user a')
                        ->where('a.u_id', $user_id) // bound param
                        ->limit(1)
                        ->get();
    }

    public function petugasCountData(){
        return $this->db->from('tb_user')
                        ->where('u_rule', 'Petugas')
                        ->count_all_results();
    }

    public function petugasData(){
        return $this->db->select('a.*, c.*, b.u_name as creator')
                        ->from('tb_user a')
                        ->join('tb_user b', 'a.u_creator_id = b.u_id', 'left')
                        ->join('tb_cabang c','a.u_ca_id = c.ca_id','left')
                        ->where('a.u_rule', 'Petugas')
                        ->get();
    }

    public function petugasTambahProcess($data){
        return $this->db->insert('tb_user', $data);
    }

    public function petugasUbahProcess($data, $idPetugas){
        return $this->db->where('u_id', $idPetugas)
                        ->update('tb_user', $data);
    }

    public function petugasHapusProcess($id){
        return $this->db->where('u_id', $id)->delete('tb_user');
    }

    /** Upgrade hash password dari md5 ke password_hash() */
    public function upgradePassword($userId, $newHash){
        $map = $this->mapColumns();
        if (!$map->password || !$map->id) {
            log_message('error', 'UserModel: kolom tidak lengkap saat upgradePassword');
            return false;
        }
        $prev = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $ok = $this->db->where($map->id, $userId)
                       ->update('tb_user', [ $map->password => $newHash ]);

        if (!$ok){
            log_message('error', 'DB error upgradePassword: '.json_encode($this->db->error()));
        }
        $this->db->db_debug = $prev;
        return $ok;
    }
}
