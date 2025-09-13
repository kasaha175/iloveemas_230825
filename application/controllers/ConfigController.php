<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConfigController extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('UserModel');
        $this->load->model('ConfigModel');
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'email']); // NOTE: encryption DIHAPUS dari sini
        date_default_timezone_set('Asia/Jakarta');
    }

    private function must_admin(){
        if ($this->session->userdata('authUser') !== true) redirect(base_url('login'));
        $id = $this->session->userdata('idUser');
        $u  = $this->UserModel->userDataById($id)->row();
        if (!$u || strtolower((string)$u->u_rule) !== 'administrator') {
            show_error('Forbidden', 403);
        }
        return $u;
    }

    public function index(){
        $u = $this->must_admin();

        // Ambil config aplikasi
        $appCfg = $this->ConfigModel->getAllAssoc();
        if (!is_array($appCfg)) $appCfg = [];

        // Ambil config SMTP aktif; normalize ke array + default agar view aman
        $smtp = $this->ConfigModel->getSmtpActive();
        if (is_object($smtp)) $smtp = (array) $smtp;
        if (!is_array($smtp)) $smtp = [];
        $smtp = array_merge([
            'protocol'    => 'smtp',
            'crypto'      => '',        // '', 'ssl', 'tls'
            'host'        => '',
            'port'        => 587,
            'user'        => '',
            'password'    => '',        // tidak ditampilkan di view
            'from_email'  => '',
            'from_name'   => '',
            'timeout_sec' => 10,
            'is_active'   => 1,
        ], $smtp);

        $data = [
            'title'    => 'Konfigurasi',
            'userData' => [$u],
            'config'   => $appCfg,
            'smtp'     => $smtp,
        ];
        $data['sidebar'] = $this->load->view('Sidebar', $data, true);
        $data['content'] = $this->load->view('ConfigForm', $data, true);
        $this->load->view('UserTemplate', $data);
    }

    public function smtp(){
        // gunakan view gabungan + query tab
        return redirect(base_url('config?tab=smtp'));
    }

    public function save(){
        $u = $this->must_admin();
        if ($this->input->method(TRUE) !== 'POST') return redirect(base_url('config'));

        $payload = [
            'app_name'        => trim((string)$this->input->post('app_name', true)),
            'app_version'     => trim((string)$this->input->post('app_version', true)),
            'color_primary'   => trim((string)$this->input->post('color_primary', true)),
            'color_secondary' => trim((string)$this->input->post('color_secondary', true)),
            'color_pastel'    => trim((string)$this->input->post('color_pastel', true)),
        ];

        // validasi hex
        $is_hex = function($v){ return $v === '' || preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', (string)$v); };
        foreach (['color_primary','color_secondary','color_pastel'] as $k) {
            if (!isset($payload[$k]) || !$is_hex($payload[$k])) $payload[$k] = null;
        }

        // Uploads (SVG diblok)
        $uploads = [
            'logo'         => ['types'=>'png|jpg|jpeg|webp','name'=>'logo'],
            'favicon'      => ['types'=>'png|ico','name'=>'favicon'],
            'bg_login'     => ['types'=>'png|jpg|jpeg|webp','name'=>'bg_login'],
            'bg_dashboard' => ['types'=>'png|jpg|jpeg|webp','name'=>'bg_dashboard'],
        ];
        foreach ($uploads as $field => $opt){
            $path = $this->_handle_upload($field, $opt['types'], $opt['name']);
            if ($path) $payload[$field] = $path;
        }

        // skip null agar tidak overwrite
        $clean = [];
        foreach ($payload as $k => $v) if ($v !== null) $clean[$k] = $v;

        $this->ConfigModel->setBatch($clean);

        $this->session->set_flashdata('status', 'success');
        $this->session->set_flashdata('message', 'Konfigurasi disimpan.');
        return redirect(base_url('config'));
    }

    private function _handle_upload($field, $allowed, $prefix){
        if (empty($_FILES[$field]['name'])) return null;

        $dir = FCPATH.'assets/img/config/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        // pastikan svg diblok
        $allowed = str_replace(['svg|','|svg','svg'], '', (string)$allowed);

        $config = [
            'upload_path'   => $dir,
            'allowed_types' => $allowed,
            'max_size'      => 2048,
            'file_name'     => uniqid($prefix.'-', true),
            'overwrite'     => false,
        ];
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($field)) {
            log_message('error', 'Upload error '.$field.': '.$this->upload->display_errors('', ''));
            $this->session->set_flashdata('status', 'error');
            $this->session->set_flashdata('message', 'Upload gagal untuk '.$field);
            return null;
        }

        $data = $this->upload->data();

        // MIME check
        $full = $data['full_path'];
        $realMime = @mime_content_type($full) ?: $data['file_type'];
        $okMimes = ['image/png','image/jpeg','image/webp','image/x-icon','image/vnd.microsoft.icon'];
        $ok = false; foreach ($okMimes as $m) if (stripos($realMime, $m) === 0) { $ok = true; break; }
        if (!$ok) {
            @unlink($full);
            log_message('error', 'MIME mismatch '.$field.': '.$realMime);
            $this->session->set_flashdata('status', 'error');
            $this->session->set_flashdata('message', 'Tipe file tidak diizinkan untuk '.$field);
            return null;
        }

        return 'assets/img/config/'.$data['file_name'];
    }

    public function save_smtp(){
        $u = $this->must_admin();
        if ($this->input->method(TRUE) !== 'POST') return redirect(base_url('config?tab=smtp'));

        $crypto = strtolower((string)$this->input->post('crypto', true));
        if (!in_array($crypto, ['tls','ssl','none',''], true)) $crypto = '';
        if ($crypto === 'none') $crypto = '';

        $port = (int)$this->input->post('port', true);
        if ($port < 1 || $port > 65535) $port = 587;

        $timeout = (int)$this->input->post('timeout_sec', true);
        if ($timeout < 2 || $timeout > 120) $timeout = 10;

        $from_email = trim((string)$this->input->post('from_email', true));
        if (!filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('status', 'error');
            $this->session->set_flashdata('message', 'From email tidak valid.');
            return redirect(base_url('config?tab=smtp'));
        }

        $password = trim((string)$this->input->post('password', true));

        $payload = [
            'protocol'    => 'smtp',
            'crypto'      => $crypto,
            'host'        => trim((string)$this->input->post('host', true)),
            'port'        => $port,
            'user'        => trim((string)$this->input->post('user', true)),
            'from_email'  => $from_email,
            'from_name'   => trim((string)$this->input->post('from_name', true)),
            'timeout_sec' => $timeout,
            'is_active'   => (int)$this->input->post('is_active', true) === 1 ? 1 : 0,
        ];
        if ($password !== '') {
            // kalau nanti mau enkripsi, load library + pastikan encryption_key ada
            // $this->load->library('encryption');
            // $payload['password'] = $this->encryption->encrypt($password);
            $payload['password'] = $password;
        }

        $this->ConfigModel->saveSmtp($payload);

        $this->session->set_flashdata('status', 'success');
        $this->session->set_flashdata('message', 'SMTP configuration updated.');
        return redirect(base_url('config?tab=smtp'));
    }

    public function smtp_test_send()
    {
        if ($this->input->method(TRUE) !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok'=>false,'error'=>'Method not allowed']));
        }
        $this->must_admin();

        $to = trim((string)$this->input->post('to', true));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return $this->_json(['ok'=>false,'error'=>'Email tujuan tidak valid.']);
        }

        $smtp = $this->ConfigModel->getSmtpActive();
        if (is_object($smtp)) $smtp = (array)$smtp;
        if (!$smtp || empty($smtp['host'])) {
            return $this->_json(['ok'=>false,'error'=>'Konfigurasi SMTP aktif tidak ditemukan.']);
        }

        // normalisasi
        $host   = preg_replace('#^(ssl://|tls://)#i', '', trim($smtp['host']));
        $port   = (int)($smtp['port'] ?? 587);
        $crypto = strtolower((string)($smtp['crypto'] ?? ''));
        if ($crypto === 'ssl') $port = 465;
        elseif ($crypto === 'tls') $port = 587;

        // quick connectivity re-check
        $fp = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$fp) {
            log_message('error', "SMTP TEST: gagal konek $host:$port ($errno $errstr)");
            return $this->_json(['ok'=>false,'error'=>"Tidak bisa konek ke $host:$port"]);
        }
        fclose($fp);

        // kirim email tes
        $cfg = [
            'protocol'     => 'smtp',
            'smtp_host'    => $host,
            'smtp_port'    => $port,
            'smtp_user'    => $smtp['user'] ?? '',
            'smtp_pass'    => $smtp['password'] ?? '',
            'smtp_crypto'  => ($crypto === 'none' ? '' : $crypto),
            'mailtype'     => 'html',
            'charset'      => 'utf-8',
            'smtp_timeout' => (int)($smtp['timeout_sec'] ?? 15),
            'newline'      => "\r\n",
            'crlf'         => "\r\n",
            'validate'     => true,
        ];
        $this->load->library('email');
        $this->email->initialize($cfg);
        $this->email->from($smtp['from_email'] ?? 'no-reply@example.com', $smtp['from_name'] ?? 'System');
        $this->email->to($to);
        $this->email->subject('Tes SMTP - '.$this->config->item('base_url'));
        $this->email->message('<p>Halo,</p><p>Ini adalah email tes SMTP dari aplikasi.</p><p><small>'.date('Y-m-d H:i:s').'</small></p>');

        if (!$this->email->send(false)) {
            $dbg = $this->email->print_debugger(['headers']);
            log_message('error', 'SMTP test failed: '.$dbg);
            return $this->_json(['ok'=>false,'error'=>'Gagal mengirim email tes. Cek kredensial/crypto/port.']);
        }

        return $this->_json(['ok'=>true,'message'=>'Email tes terkirim ke '.$to.'.']);
    }

    public function smtp_check_net()
    {
        // HANYA AJAX POST
        if ($this->input->method(TRUE) !== 'POST') {
            return $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok'=>false,'error'=>'Method not allowed']));
        }
        // auth
        $this->must_admin();

        // ambil SMTP aktif
        $smtp = $this->ConfigModel->getSmtpActive();
        if (is_object($smtp)) $smtp = (array)$smtp;
        if (!$smtp || empty($smtp['host'])) {
            return $this->_json(['ok'=>false,'status'=>'offline','error'=>'Konfigurasi SMTP tidak ditemukan.']);
        }

        // normalisasi host/port/crypto
        $host   = preg_replace('#^(ssl://|tls://)#i', '', trim($smtp['host']));
        $port   = (int)($smtp['port'] ?? 587);
        $crypto = strtolower((string)($smtp['crypto'] ?? ''));
        if ($crypto === 'ssl') $port = 465;
        elseif ($crypto === 'tls') $port = 587;

        // ping TCP sederhana + ukur RTT
        $start = microtime(true);
        $errno = 0; $errstr = '';
        $fp = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$fp) {
            log_message('error', "SMTP CHECK-NET: gagal konek $host:$port ($errno $errstr)");
            return $this->_json([
                'ok'=>false,'status'=>'offline','error'=>"Tidak bisa konek ke $host:$port ($errno $errstr)"
            ]);
        }
        fclose($fp);
        $rtt = (int)round((microtime(true) - $start)*1000);

        // klasifikasi kualitas
        $status = 'ok';
        if ($rtt <= 120) $status = 'good';
        elseif ($rtt >= 800) $status = 'poor';

        return $this->_json([
            'ok'=>true,
            'status'=>$status,
            'rtt_ms'=>$rtt,
            'host'=>$host,
            'port'=>$port,
            'crypto'=>$crypto ?: 'none'
        ]);
    }

    private function _json(array $payload, int $code = 200)
    {
        // ikutkan token baru jika CSRF regenerate aktif
        $payload['csrf_name'] = $this->security->get_csrf_token_name();
        $payload['csrf_hash'] = $this->security->get_csrf_hash();

        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload));
    }
    
}
