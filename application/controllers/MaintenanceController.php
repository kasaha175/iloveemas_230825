<?php
// application/controllers/MaintenanceController.php
defined('BASEPATH') OR exit('No direct script access allowed');

class MaintenanceController extends CI_Controller
{
    private $allowed = [
        'tb_transaction_items'      => 'Transaction Items (Beli)',
        'tb_transaction_items_sell' => 'Transaction Items (Jual)',
        'tb_transaction_prints'     => 'Transaction Prints (meta & file path)', // ⬅️ TAMBAH INI
        'tb_transaction'            => 'Transaction (Beli)',
        'tb_transaction_sell'       => 'Transaction (Jual)',
        'tb_customer'               => 'Customer',
    ];

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('UserModel');
        $this->load->model('ConfigModel');                 // <-- penting (untuk theme)
        $this->load->model('MaintenanceModel', 'Maint');   // <-- untuk count & eksekusi
        $this->load->helper(['url','form']);
    }

    private function must_admin()
    {
        if ($this->session->userdata('authUser') !== true) redirect(base_url('login'));
        $id = $this->session->userdata('idUser');
        $u  = $this->UserModel->userDataById($id)->row();
        if (!$u || strtolower($u->u_rule) !== 'administrator') show_404();
        return $u;
    }

    public function truncate() // GET
    {
        $u = $this->must_admin();

        $data['title']    = 'Data Maintenance — Truncate';
        $data['userData'] = [$u];
        $data['config']   = $this->ConfigModel->getAllAssoc();                 // <-- dikirim ke layout
        $data['allowed']  = $this->allowed;
        $data['counts']   = $this->Maint->countTables(array_keys($this->allowed));

        $data['sidebar']  = $this->load->view('Sidebar', $data, true);
        $data['content']  = $this->load->view('MaintenanceTruncate', $data, true);
        $this->load->view('UserTemplate', $data);
    }

    public function truncateRun()
    {
        $u = $this->must_admin();
        if ($this->input->method(TRUE) !== 'POST') {
            return redirect('maintenance/truncate');
        }

        // Validasi minimal
        $tables  = (array)$this->input->post('tables');
        $ack     = (bool)$this->input->post('ack');
        $confirm = trim((string)$this->input->post('confirm_text'));
        $pwd     = (string)$this->input->post('admin_password');

        if (!$ack) {
            $this->session->set_flashdata('error','Centang pernyataan pemahaman risiko.');
            return redirect('maintenance/truncate');
        }
        if (strcasecmp($confirm, 'TRUNCATE') !== 0) {
            $this->session->set_flashdata('error','Teks konfirmasi harus persis: TRUNCATE');
            return redirect('maintenance/truncate');
        }

        // Verifikasi password admin
        $row = $this->UserModel->userDataById($u->u_id)->row();
        if (!$row || !password_verify($pwd, $row->u_password)) {
            $this->session->set_flashdata('error','Password administrator salah.');
            return redirect('maintenance/truncate');
        }

        // Filter hanya tabel yang diizinkan
        $allowedKeys = array_keys($this->allowed);
        $selected    = array_values(array_intersect($tables, $allowedKeys));
        if (empty($selected)) {
            $this->session->set_flashdata('error','Pilih minimal satu tabel.');
            return redirect('maintenance/truncate');
        }

        // Urutan aman (items → header → customer)
        $sequence = [
            'tb_transaction_items',
            'tb_transaction_items_sell',
            'tb_transaction_prints', 
            'tb_transaction',
            'tb_transaction_sell',
            'tb_customer',
        ];
        // ambil yang dipilih saja tapi mengikuti urutan di atas
        $ordered = array_values(array_intersect($sequence, $selected));

        // Eksekusi
        $this->load->model('MaintenanceModel','Maint');
        $summary = $this->Maint->truncateSequence($ordered);

        // Hitung sukses/gagal
        $okCnt = 0; $failCnt = 0;
        foreach ($summary as $tbl => $r) {
            $r['ok'] ? $okCnt++ : $failCnt++;
        }

        $msg = "Truncate selesai. OK: {$okCnt}, Gagal: {$failCnt}.";
        $this->session->set_flashdata('success', $msg);
        $this->session->set_flashdata('summary', $summary);

        return redirect('maintenance/truncate');
    }
}

