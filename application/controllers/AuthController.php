<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('UserModel');
		$this->load->model('TransactionModel');
		$this->load->model('ProductModel');
        $this->data["title"] = "And Team Andromeda Tours";
        date_default_timezone_set("Asia/Jakarta"); 
        $this->dateToday = date("Y-m-d H:i:s");
	}

    public function login()
    {
        if ($this->session->userdata('authUser') === true) {
            return redirect(base_url('dashboard'));
        }
        $data['title'] = 'Login';
        $this->load->view('/login', $data); // sesuaikan path view
    }

    public function loginProcess()
    {
        if ($this->session->userdata('authUser') === true) {
            return redirect(base_url('dashboard'));
        }
        if ($this->input->method(TRUE) !== 'POST') {
            return redirect(base_url('login'));
        }

        $username = trim((string)$this->input->post('username', true));
        $password = (string)$this->input->post('password');

        if ($username === '' || $password === '') {
            $this->session->set_userdata(['failedLogin' => true, 'authUser' => false]);
            return redirect(base_url('login'));
        }

        $user = $this->UserModel->findByUsername($username);
        $ok   = false;

        if ($user) {
            $stored = (string)($user->u_password ?? '');

            if ($stored !== '') {
                $info = password_get_info($stored);

                // Hash modern
                if (!empty($info['algo'])) {
                    $ok = password_verify($password, $stored);

                // Legacy MD5
                } elseif (preg_match('/^[a-f0-9]{32}$/i', $stored)) {
                    if (hash_equals(strtolower($stored), md5($password))) {
                        $ok = true;
                        // coba upgrade; jika gagal tetap login, tapi log error
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        if (!$this->UserModel->upgradePassword($user->u_id, $newHash)) {
                            log_message('error', 'Upgrade password gagal untuk user_id='.$user->u_id);
                        }
                    }
                } else {
                    // hash tak dikenal
                    log_message('error', 'Format hash tak dikenal untuk user_id='.$user->u_id);
                }
            }
        } else {
            // user null karena schema salah atau tidak ditemukan
            log_message('info', 'Login gagal: user tidak ditemukan/DB error untuk username='.$username);
        }

        if ($ok) {
            $this->session->sess_regenerate(true);
            $this->session->set_userdata(['authUser' => true, 'idUser' => $user->u_id]);
            return redirect(base_url('dashboard'));
        }

        usleep(300000);
        $this->session->set_userdata(['failedLogin' => true, 'authUser' => false]);
        return redirect(base_url('login'));
    }

    public function logoutProcess()
    {
        // jika mau izinkan GET/POST:
        if (!in_array($this->input->method(TRUE), ['POST','GET'], true)) {
            return redirect(base_url('login'));
        }
        $this->session->sess_destroy();
        return redirect(base_url('login'));
    }

}

?>