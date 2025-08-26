<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportController extends CI_Controller
{
    /** payload umum ke view */
    protected $data = [];
    private   $dateToday;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        // MODEL
        $this->load->model(['UserModel','MasterModel','TransactionModel','MaterialModel','ConfigModel']);

        // LIB & HELPER
        $this->load->library(['cart','Pdf']);
        // penting supaya html_escape() tersedia di view
        $this->load->helper(['url','form','security']);

        $this->data['title'] = '';
        $this->dateToday     = date("Y-m-d H:i:s");
    }

    /** Pastikan login, siapkan data umum (title, userData, config, sidebar) */
    private function boot(string $title): void
    {
        if ($this->session->userdata('authUser') !== true) {
            redirect(base_url()); exit;
        }
        $idUser = $this->session->userdata('idUser');
        $this->data['title']    = $title;
        $this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
        $this->data['config']   = $this->ConfigModel->getAllAssoc();
        $this->data['sidebar']  = $this->load->view('Sidebar', $this->data, true);
    }

    /** Render helper: sisipkan $content ke UserTemplate */
    private function render(string $view): void
    {
        $this->data['content'] = $this->load->view($view, $this->data, true);
        $this->load->view('UserTemplate', $this->data);
    }

    // ====================== PAGES & REPORT ======================

    public function report()
    {
        $this->boot('REPORT');
        $this->render('Report'); // View menu utama report (grid 4 kartu)
    }

    public function buy()
    {
        $this->boot('REPORT BUY');
        $dateStart = $this->input->get('dateStart');
        $dateEnd   = $this->input->get('dateEnd');
        $this->data['data'] = $this->TransactionModel->buyTransaction($dateStart, $dateEnd)->result();
        $this->render('ReportBuy');
    }

    public function sell()
    {
        $this->boot('REPORT SELL');
        $dateStart = $this->input->get('dateStart');
        $dateEnd   = $this->input->get('dateEnd');
        $this->data['data'] = $this->TransactionModel->sellTransaction($dateStart, $dateEnd)->result();
        $this->render('ReportSell');
    }

    public function buyGraph()
    {
        $this->boot('REPORT BUY GRAPH');
        $year     = $this->input->get('year') ?: date('Y');
        $material = $this->input->get('material');

        $this->data['materialData'] = $this->MaterialModel->materialData('Buy')->result();
        $this->data['yearData']     = $this->MasterModel->yearData()->result();
        $this->data['data']         = $this->TransactionModel->buyTransactionGraph($year, $material)->result();
        $this->data['dataCustomer'] = $this->TransactionModel->buyTransactionCustomerGraph($year);

        $this->render('ReportBuyGraph');
    }

    public function sellGraph()
    {
        $this->boot('REPORT SELL GRAPH');

        // ambil filter (gunakan TRUE agar XSS filtering aktif)
        $year     = $this->input->get('year', true);
        $material = $this->input->get('material', true);

        if ($year === null || $year === '') $year = date('Y');
        if ($material === null)             $material = '';

        // dropdown
        $this->data['materialData'] = $this->MaterialModel->materialData('Sell')->result();
        $this->data['yearData']     = $this->MasterModel->yearData()->result();

        // data chart
        $qGraph = $this->TransactionModel->sellTransactionGraph($year, $material);
        $this->data['data'] = is_object($qGraph) ? $qGraph->result() : [];

        $cust = $this->TransactionModel->sellTransactionCustomerGraph($year);
        $this->data['dataCustomer'] = is_array($cust) ? $cust : [];

        // tampilkan view: application/views/ReportSellGraph.php
        $this->render('ReportSellGraph');
    }

    public function buyDetail()
    {
        $this->boot('REPORT BUY DETAIL');
        $idTransaction       = $this->uri->segment(3);
        $qHeader             = $this->TransactionModel->buyTransactionData($idTransaction);
        $this->data['data']  = $qHeader->result();
        if (empty($this->data['data'])) { redirect(base_url("report/buy/")); return; }
        $header              = $qHeader->row();
        $this->data['title'] = $header->t_no_order;
        $this->data['detail']= $this->TransactionModel->buyTransactionItemsData($idTransaction)->result();
        $this->render('ReportBuyDetail');
    }

    public function sellDetail()
    {
        $this->boot('REPORT SELL DETAIL');
        $idTransaction       = $this->uri->segment(3);
        $qHeader             = $this->TransactionModel->sellTransactionData($idTransaction);
        $this->data['data']  = $qHeader->result();
        if (empty($this->data['data'])) { redirect(base_url("report/sell/")); return; }
        $header              = $qHeader->row();
        $this->data['title'] = $header->t_no_order;
        $this->data['detail']= $this->TransactionModel->sellTransactionItemsData($idTransaction)->result();
        $this->render('ReportSellDetail');
    }

    // ====================== PRINT VIEW (HTML) ======================

    public function buyPrint()
    {
        $this->boot('PRINT BUY');
        $idTransaction = $this->uri->segment(3);
        $qHeader       = $this->TransactionModel->buyTransactionData($idTransaction);
        $rows          = $qHeader->result();
        if (empty($rows)) { redirect(base_url("report/buy/")); return; }
        $header                 = $qHeader->row();
        $this->data['data']     = $rows;
        $this->data['title']    = $header->t_no_order;
        $this->data['detail']   = $this->TransactionModel->buyTransactionItemsData($idTransaction)->result();
        // print view langsung (di luar template utama)
        $this->load->view("PrintBuy", $this->data);
    }

    public function sellPrint()
    {
        $this->boot('PRINT SELL');
        $idTransaction = $this->uri->segment(3);
        $qHeader       = $this->TransactionModel->sellTransactionData($idTransaction);
        $rows          = $qHeader->result();
        if (empty($rows)) { redirect(base_url("report/sell/")); return; }
        $header                 = $qHeader->row();
        $this->data['data']     = $rows;
        $this->data['title']    = $header->t_no_order;
        $this->data['detail']   = $this->TransactionModel->sellTransactionItemsData($idTransaction)->result();
        // print view langsung (di luar template utama)
        $this->load->view("PrintSell", $this->data);
    }

    // ====================== PRINT ACTION (INSERT + PDF + UPDATE) ======================

    /** POST JSON: { cabang:[{nama,alamat},...], payments:["CASH","DEBIT",...]} */
    public function buyPrintAction($idTransaction) { $this->_handlePrintAction((int)$idTransaction, 'BUY'); }

    /** POST JSON: { cabang:[{nama,alamat},...], payments:["CASH","DEBIT",...]} */
    public function sellPrintAction($idTransaction) { $this->_handlePrintAction((int)$idTransaction, 'SELL'); }

    // ====================== PRIVATE UTILS ======================

    /** Load dompdf dari third_party (atau Composer jika ada) */
    private function _init_dompdf(): void
    {
        if (file_exists(FCPATH . 'vendor/autoload.php')) {
            require_once FCPATH . 'vendor/autoload.php';
            return;
        }
        if (file_exists(APPPATH . 'third_party/dompdf/autoload.inc.php')) {
            require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
            return;
        }
        // jangan hentikan app; fungsi printAction akan menangani error ini sendiri jika dipakai
    }

    /** Core handler: simpan input, generate PDF offline, update header, balas URL PDF */
    private function _handlePrintAction(int $id, string $type)
    {
        // (ISI SAMA persis seperti versi kamu sebelumnya – tidak diubah)
        // ------ PASTE dari kode kamu yang sudah jalan ------
        // !!! Gunakan blok _handlePrintAction yang kemarin (panjang), biar aman.
        // ----------------------------------------------------
    }

    private function _json(int $code, array $payload)
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function testDompdf()
    {
        // (Biarkan sama seperti punyamu)
        try{
            if (file_exists(FCPATH.'vendor/autoload.php')) require_once FCPATH.'vendor/autoload.php';
            elseif (file_exists(APPPATH.'third_party/dompdf/autoload.inc.php')) require_once APPPATH.'third_party/dompdf/autoload.inc.php';
            $opt = new \Dompdf\Options();
            $opt->set('defaultFont','DejaVu Sans');
            $dompdf = new \Dompdf\Dompdf($opt);
            $dompdf->loadHtml('<h1>Hello Dompdf</h1>');
            $dompdf->setPaper('A4','portrait');
            $dompdf->render();
            @mkdir(FCPATH.'uploads/transactions',0777,true);
            file_put_contents(FCPATH.'uploads/transactions/test.pdf', $dompdf->output());
            echo json_encode(['ok'=>true,'url'=>base_url('uploads/transactions/test.pdf')]);
        }catch(\Throwable $e){
            echo json_encode(['ok'=>false,'msg'=>$e->getMessage()]);
        }
    }

	public function buy_dt()
	{
		// Selalu balas JSON (jangan redirect/HTML)
		$this->output->set_content_type('application/json','utf-8');
		if ($this->session->userdata('authUser') !== true) {
			return $this->output->set_status_header(401)->set_output(json_encode([
				'draw'=>0,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[],
				$this->security->get_csrf_token_name()=>$this->security->get_csrf_hash(),
				'error'=>'Unauthorized'
			]));
		}

		$draw   = (int) ($this->input->post('draw')   ?? 0);
		$start  = (int) ($this->input->post('start')  ?? 0);
		$length = (int) ($this->input->post('length') ?? 10);

		$postSearch = $this->input->post('search');
		$search     = (is_array($postSearch) && isset($postSearch['value'])) ? trim($postSearch['value']) : '';

		$postOrder = $this->input->post('order');
		$order0    = (is_array($postOrder) && isset($postOrder[0])) ? $postOrder[0] : ['column'=>4,'dir'=>'desc'];

		// mapping index kolom -> nama kolom
		$cols = [
			0=>'t_id', 1=>null, 2=>'t_no_order', 3=>'t_status', 4=>'t_date_created',
			5=>'nameCreator', 6=>'nameReceive', 7=>'nameCustomer', 8=>'t_qtt', 9=>'t_price_total'
		];
		$orderBy = $cols[(int)$order0['column']] ?? 't_date_created';
		$dir     = (isset($order0['dir']) && strtolower($order0['dir'])==='asc') ? 'ASC' : 'DESC';

		$dateStart = $this->input->post('dateStart') ?? $this->input->get('dateStart') ?? date('Y-m-01');
		$dateEnd   = $this->input->post('dateEnd')   ?? $this->input->get('dateEnd')   ?? date('Y-m-t');

		$res  = $this->TransactionModel->dtBuy($start,$length,$dateStart,$dateEnd,$search,$orderBy,$dir);

		return $this->output->set_output(json_encode([
			'draw'            => $draw,
			'recordsTotal'    => (int)$res['total'],
			'recordsFiltered' => (int)$res['filtered'],
			'data'            => $res['rows'],
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
	}

	public function sell_dt()
	{
		$this->output->set_content_type('application/json','utf-8');
		if ($this->session->userdata('authUser') !== true) {
			return $this->output->set_status_header(401)->set_output(json_encode([
				'draw'=>0,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[],
				$this->security->get_csrf_token_name()=>$this->security->get_csrf_hash(),
				'error'=>'Unauthorized'
			]));
		}

		$draw   = (int) ($this->input->post('draw')   ?? 0);
		$start  = (int) ($this->input->post('start')  ?? 0);
		$length = (int) ($this->input->post('length') ?? 10);

		$postSearch = $this->input->post('search');
		$search     = (is_array($postSearch) && isset($postSearch['value'])) ? trim($postSearch['value']) : '';

		$postOrder = $this->input->post('order');
		$order0    = (is_array($postOrder) && isset($postOrder[0])) ? $postOrder[0] : ['column'=>4,'dir'=>'desc'];

		$cols = [
			0=>'t_id', 1=>null, 2=>'t_no_order', 3=>'t_status', 4=>'t_date_created',
			5=>'nameCreator', 6=>'nameReceive', 7=>'nameCustomer', 8=>'t_qtt', 9=>'t_price_total'
		];
		$orderBy = $cols[(int)$order0['column']] ?? 't_date_created';
		$dir     = (isset($order0['dir']) && strtolower($order0['dir'])==='asc') ? 'ASC' : 'DESC';

		$dateStart = $this->input->post('dateStart') ?? $this->input->get('dateStart') ?? date('Y-m-01');
		$dateEnd   = $this->input->post('dateEnd')   ?? $this->input->get('dateEnd')   ?? date('Y-m-t');

		$res  = $this->TransactionModel->dtSell($start,$length,$dateStart,$dateEnd,$search,$orderBy,$dir);

		return $this->output->set_output(json_encode([
			'draw'            => $draw,
			'recordsTotal'    => (int)$res['total'],
			'recordsFiltered' => (int)$res['filtered'],
			'data'            => $res['rows'],
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
	}

}
