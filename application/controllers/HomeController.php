<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends CI_Controller
{
    protected $data = [];

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        // Models
        $this->load->model('UserModel');
        $this->load->model('TransactionModel', 'Tx'); // alias
        $this->load->model('CustomerModel',    'Cust'); // alias
        $this->load->model('ConfigModel'); // <-- penting: untuk inject ke view

        $this->data['title'] = '';
    }

    /** Ambil seluruh konfigurasi sebagai assoc array */
    private function _cfg()
    {
        try {
            return (array) $this->ConfigModel->getAllAssoc();
        } catch (\Throwable $e) {
            log_message('error', 'Config load error: '.$e->getMessage());
            return [];
        }
    }

    public function index()
    {
        $authUser = $this->session->userdata('authUser');
        $this->data['title']   = 'I Love Emas';
        $this->data['config']  = $this->_cfg();                  // <-- kirim ke view

        if ($authUser === true) {
            return redirect(base_url('dashboard'));
        }

        // fallback nama view login (Login.php atau login.php)
        $view = file_exists(APPPATH.'views/Login.php') ? 'Login' : 'login';
        $this->load->view($view, $this->data);
    }

    public function dashboard()
    {
        if ($this->session->userdata('authUser') !== true) {
            return redirect(base_url());
        }

        $idUser              = $this->session->userdata('idUser');
        $this->data['title'] = 'DASHBOARD';
        $this->data['config']= $this->_cfg();                   // <-- kirim ke layout

        // === KPI (sinkron untuk default, tapi di view diambil async juga OK) ===
        $this->data['kpi_tx_today'] = (int) $this->Tx->countTodayFinalized(['SELESAI']);
        $this->data['kpi_cust']     = (int) $this->Cust->countAllCustomers();

        // Data user
        $this->data['userData'] = $this->UserModel->userDataById($idUser)->result();

        // Render
        $this->data['sidebar'] = $this->load->view('Sidebar',   $this->data, true);
        $this->data['content'] = $this->load->view('Dashboard', $this->data, true);
        $this->load->view('UserTemplate', $this->data);
    }

    public function error()
    {
        $this->data['title']   = '404 PAGE NOT FOUND';
        $this->data['config']  = $this->_cfg();                  // <-- kirim ke layout
        $this->data['content'] = $this->load->view('Error', $this->data, true);
        $this->load->view('UserTemplate', $this->data);
    }

    /** Endpoint async untuk KPI dashboard */
    public function kpiDashboard()
    {
        if ($this->input->method(TRUE) !== 'GET') {
            return show_error('Method Not Allowed', 405);
        }
        if ($this->session->userdata('authUser') !== true) {
            $this->output->set_status_header(401);
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'unauthorized']));
        }

        $txToday = (int) $this->Tx->countTodayFinalized(['SELESAI']);
        $custAll = (int) $this->Cust->countAllCustomers();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'tx_today'       => $txToday,
                'customer_total' => $custAll,
            ]));
    }
}
