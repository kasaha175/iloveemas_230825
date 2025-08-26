<?php
// application/controllers/MaintenanceController.php
defined('BASEPATH') OR exit('No direct script access allowed');

class MaintenanceController extends CI_Controller
{
    private $allowed = [
        'tb_transaction_items'      => 'Transaction Items (Beli)',
        'tb_transaction_items_sell' => 'Transaction Items (Jual)',
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

    public function truncateRun() // POST dari form
    {
        $u = $this->must_admin();
        if ($this->input->method(TRUE) !== 'POST') return redirect('maintenance/truncate');

        // … validasi (ack, confirm_text, password), eksekusi truncate, set flashdata …
        // lalu redirect kembali:
        return redirect('maintenance/truncate');
    }
}

