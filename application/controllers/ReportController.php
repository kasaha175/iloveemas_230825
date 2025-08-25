<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportController extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('UserModel');
		$this->load->model('MasterModel');
		$this->load->model('TransactionModel');
		$this->load->model('MaterialModel');
		$this->data["title"] = "";
		date_default_timezone_set("Asia/Jakarta");
		$this->dateToday = date("Y-m-d H:i:s");
		$this->load->library('Pdf'); // tetap jika dipakai modul lain
		$this->load->library('cart');
		$this->_init_dompdf(); // siapkan dompdf (third_party)
	}

	// ====================== PAGES & REPORT ======================

	function report()
	{
		$authUser = $this->session->userdata("authUser");
		$this->data["title"] = "REPORT";
		if ($authUser == true) {
			$this->data['content'] = $this->load->view('Report', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	function buy()
	{
		$authUser = $this->session->userdata("authUser");
		$this->data["title"] = "REPORT BUY";
		if ($authUser == true) {
			$dateStart = $this->input->get('dateStart');
			$dateEnd   = $this->input->get('dateEnd');
			$this->data['data'] = $this->TransactionModel->buyTransaction($dateStart, $dateEnd)->result();
			$this->data['content'] = $this->load->view('ReportBuy', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	function buyGraph()
	{
		$authUser = $this->session->userdata("authUser");
		$this->data["title"] = "REPORT BUY GRAPH";
		if ($authUser == true) {
			$year     = $this->input->get('year') ?: date('Y');
			$material = $this->input->get('material');

			$this->data['materialData']  = $this->MaterialModel->materialData('Buy')->result();
			$this->data['yearData']      = $this->MasterModel->yearData()->result();
			$this->data['data']          = $this->TransactionModel->buyTransactionGraph($year, $material)->result();
			$this->data['dataCustomer']  = $this->TransactionModel->buyTransactionCustomerGraph($year);
			$this->data['content']       = $this->load->view('ReportBuyGraph', $this->data, true);

			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	function sellGraph()
	{
		$authUser = $this->session->userdata("authUser");
		$this->data["title"] = "REPORT SELL GRAPH";
		if ($authUser == true) {
			$year     = $this->input->get('year') ?: date('Y');
			$material = $this->input->get('material');

			$this->data['materialData']  = $this->MaterialModel->materialData('Sell')->result();
			$this->data['dataCustomer']  = $this->TransactionModel->sellTransactionCustomerGraph($year);
			$this->data['yearData']      = $this->MasterModel->yearData()->result();
			$this->data['data']          = $this->TransactionModel->sellTransactionGraph($year, $material)->result();
			$this->data['content']       = $this->load->view('ReportSellGraph', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	function sell()
	{
		$authUser = $this->session->userdata("authUser");
		$this->data["title"] = "REPORT SELL";
		if ($authUser == true) {
			$dateStart = $this->input->get('dateStart');
			$dateEnd   = $this->input->get('dateEnd');
			$this->data['data'] = $this->TransactionModel->sellTransaction($dateStart, $dateEnd)->result();
			$this->data['content'] = $this->load->view('ReportSell', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	function buyDetail()
	{
		$authUser = $this->session->userdata("authUser");
		if ($authUser == true) {
			$idTransaction       = $this->uri->segment(3);
			$qHeader             = $this->TransactionModel->buyTransactionData($idTransaction);
			$this->data['data']  = $qHeader->result();
			if (empty($this->data['data'])) { redirect(base_url("report/buy/")); }
			$header               = $qHeader->row();
			$this->data['title']  = $header->t_no_order;
			$this->data['detail'] = $this->TransactionModel->buyTransactionItemsData($idTransaction)->result();
			$this->data['content']= $this->load->view('ReportBuyDetail', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	function sellDetail()
	{
		$authUser = $this->session->userdata("authUser");
		if ($authUser == true) {
			$idTransaction       = $this->uri->segment(3);
			$qHeader             = $this->TransactionModel->sellTransactionData($idTransaction);
			$this->data['data']  = $qHeader->result();
			if (empty($this->data['data'])) { redirect(base_url("report/sell/")); }
			$header               = $qHeader->row();
			$this->data['title']  = $header->t_no_order;
			$this->data['detail'] = $this->TransactionModel->sellTransactionItemsData($idTransaction)->result();
			$this->data['content']= $this->load->view('ReportSellDetail', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	// ====================== PRINT VIEW (HTML) ======================

	public function buyPrint()
	{
		if (!$this->session->userdata('authUser')) { redirect(base_url()); return; }
		$idTransaction = $this->uri->segment(3);
		$qHeader       = $this->TransactionModel->buyTransactionData($idTransaction);
		$rows          = $qHeader->result();
		if (empty($rows)) { redirect(base_url("report/buy/")); return; }
		$header                 = $qHeader->row();
		$this->data['data']     = $rows;
		$this->data['title']    = $header->t_no_order;
		$this->data['detail']   = $this->TransactionModel->buyTransactionItemsData($idTransaction)->result();
		$this->load->view("PrintBuy", $this->data);
	}

	public function sellPrint()
	{
		if (!$this->session->userdata('authUser')) { redirect(base_url()); return; }
		$idTransaction = $this->uri->segment(3);
		$qHeader       = $this->TransactionModel->sellTransactionData($idTransaction);
		$rows          = $qHeader->result();
		if (empty($rows)) { redirect(base_url("report/sell/")); return; }
		$header                 = $qHeader->row();
		$this->data['data']     = $rows;
		$this->data['title']    = $header->t_no_order;
		$this->data['detail']   = $this->TransactionModel->sellTransactionItemsData($idTransaction)->result();
		$this->load->view("PrintSell", $this->data);
	}

	// ====================== PRINT ACTION (INSERT + PDF + UPDATE) ======================

	/** POST JSON: { cabang:[{nama,alamat},...], payments:["CASH","DEBIT",...]} */
	public function buyPrintAction($idTransaction)
	{
		$this->_handlePrintAction((int)$idTransaction, 'BUY');
	}

	/** POST JSON: { cabang:[{nama,alamat},...], payments:["CASH","DEBIT",...]} */
	public function sellPrintAction($idTransaction)
	{
		$this->_handlePrintAction((int)$idTransaction, 'SELL');
	}

	// ====================== PRIVATE UTILS ======================

	/** Load dompdf dari third_party (atau Composer jika ada) */
	private function _init_dompdf(): void
	{
		// Composer (jika suatu saat ada)
		if (file_exists(FCPATH . 'vendor/autoload.php')) {
			require_once FCPATH . 'vendor/autoload.php';
			return;
		}
		// Third-party (yang kamu pakai sekarang)
		if (file_exists(APPPATH . 'third_party/dompdf/autoload.inc.php')) {
			require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
			return;
		}
		show_error('Dompdf tidak ditemukan. Taruh di application/third_party/dompdf atau install via Composer.', 500);
	}

	/** Core handler: simpan input, generate PDF offline, update header, balas URL PDF */
	private function _handlePrintAction(int $id, string $type)
{
    // --- AUTH ---
    if (!$this->session->userdata('authUser')) {
        return $this->_json(401, ['ok'=>false,'msg'=>'Unauthorized']);
    }
    if ($id <= 0) {
        return $this->_json(400, ['ok'=>false,'msg'=>'Invalid transaction id']);
    }

    // --- BACA PAYLOAD (JSON / form-encoded payload) ---
    $raw = $this->input->raw_input_stream;
    if (empty($raw)) { $raw = $this->input->post('payload', true); }
    log_message('error', "PRINT ACTION HIT type={$type} id={$id}");
    log_message('error', 'RAW_IN='.substr((string)$raw, 0, 1000));

    $payload  = json_decode((string)$raw, true);
    if (!is_array($payload)) $payload = [];
    $cabang   = isset($payload['cabang'])   && is_array($payload['cabang'])   ? $payload['cabang']   : [];
    $payments = isset($payload['payments']) && is_array($payload['payments']) ? $payload['payments'] : [];
    log_message('error', 'PARSED_PAYLOAD='.json_encode($payload));

    // --- AMBIL DATA TRANSAKSI ---
    if ($type === 'BUY') {
        $qHeader = $this->TransactionModel->buyTransactionData($id);
        $detail  = $this->TransactionModel->buyTransactionItemsData($id)->result();
    } else {
        $qHeader = $this->TransactionModel->sellTransactionData($id);
        $detail  = $this->TransactionModel->sellTransactionItemsData($id)->result();
    }
    $rows = $qHeader->result();
    if (empty($rows)) {
        log_message('error', "Transaksi tidak ditemukan id={$id} type={$type}");
        return $this->_json(404, ['ok'=>false,'msg'=>'Transaksi tidak ditemukan']);
    }
    $header  = $qHeader->row();
    $noOrder = (string)$header->t_no_order;

    // --- RENDER HTML VIEW (BARU KEMUDIAN DOMPDF) ---
    $dataView = [
        'data'    => $rows,
        'detail'  => $detail,
        'title'   => $noOrder,
        'for_pdf' => true, // sembunyikan toolbar & asset eksternal di view
    ];
    $viewName = ($type === 'BUY') ? 'PrintBuy' : 'PrintSell';
    $html     = $this->load->view($viewName, $dataView, true);

    // --- PERSIAPAN FOLDER & NAMA FILE ---
    $subdir  = strtolower($type) . '/' . date('Y') . '/' . date('m');
    $dirPath = FCPATH . 'uploads/transactions/' . $subdir . '/';
    if (!is_dir($dirPath) && !@mkdir($dirPath, 0775, true)) {
        log_message('error', 'MKDIR gagal: '.$dirPath);
        return $this->_json(500, ['ok'=>false,'msg'=>'Gagal membuat folder penyimpanan PDF.']);
    }
    if (!is_writable($dirPath)) {
        log_message('error', 'Folder tidak writable: '.$dirPath);
        return $this->_json(500, ['ok'=>false,'msg'=>'Folder uploads tidak writable.']);
    }

    $fileName = $type . '_' . preg_replace('/\W+/', '-', $noOrder) . '_' . time() . '.pdf';
    $filePath = $dirPath . $fileName;                                // path fisik
    $dbPath   = 'uploads/transactions/' . $subdir . '/' . $fileName;  // untuk DB
    $absUrl   = base_url($dbPath);

    // --- GENERATE PDF (SEKALI SAJA, SETELAH $html JADI) ---
    try {
        // Autoload dompdf (pastikan __construct kamu sudah memanggil _init_dompdf(), atau aktifkan baris require di sini)
        // if (file_exists(FCPATH.'vendor/autoload.php')) require_once FCPATH.'vendor/autoload.php';
        // elseif (file_exists(APPPATH.'third_party/dompdf/autoload.inc.php')) require_once APPPATH.'third_party/dompdf/autoload.inc.php';

        $opt = new \Dompdf\Options();
        $opt->set('isRemoteEnabled', false);      // offline (kalau perlu remote asset, ganti true)
        $opt->set('isHtml5ParserEnabled', true);
        $opt->set('defaultFont', 'DejaVu Sans');
        if (defined('FCPATH')) { $opt->setChroot(FCPATH); }

        // Temp & font cache writable
        $dompdfTmp  = APPPATH.'cache/dompdf';
        $dompdfFont = $dompdfTmp.'/fonts';
        if (!is_dir($dompdfTmp))  @mkdir($dompdfTmp, 0777, true);
        if (!is_dir($dompdfFont)) @mkdir($dompdfFont, 0777, true);
        if (method_exists($opt, 'setTempDir'))   $opt->setTempDir($dompdfTmp);
        if (method_exists($opt, 'setFontCache')) $opt->setFontCache($dompdfFont);
        if (method_exists($opt, 'setLogOutputFile')) $opt->setLogOutputFile($dompdfTmp.'/dompdf.log');

        @ini_set('memory_limit', '256M');
        @set_time_limit(60);

        $dompdf = new \Dompdf\Dompdf($opt);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();
    } catch (\Throwable $e) {
        log_message('error', 'DOMPDF render error: '.$e->getMessage());
        return $this->_json(500, ['ok'=>false, 'msg'=>'DOMPDF ERROR: '.$e->getMessage()]);
    }

    if (@file_put_contents($filePath, $pdfOutput) === false) {
        log_message('error', 'write PDF gagal ke '.$filePath);
        return $this->_json(500, ['ok'=>false,'msg'=>'Gagal menulis file PDF (izin folder?).']);
    }

    // --- INSERT + UPDATE TRANSAKSI ---
    $this->db->trans_begin();

    $this->db->insert('tb_transaction_print', [
        'tp_t_id'         => $id,
        'tp_type'         => $type,
        'tp_cabang_json'  => json_encode($cabang, JSON_UNESCAPED_UNICODE),
        'tp_payment_json' => json_encode($payments, JSON_UNESCAPED_UNICODE),
        'tp_pdf_path'     => $dbPath,
        'tp_created_by'   => (int)$this->session->userdata('idUser'),
        'tp_created_at'   => date('Y-m-d H:i:s'),
    ]);
    $err = $this->db->error();
    if ($err['code']) {
        log_message('error', 'INSERT tb_transaction_print error: '.json_encode($err));
        $this->db->trans_rollback(); @unlink($filePath);
        return $this->_json(500, ['ok'=>false,'msg'=>'DB error saat INSERT (tb_transaction_print).']);
    }

    $table = ($type==='BUY') ? 'tb_transaction' : 'tb_transaction_sell';
    $updateData = [
        't_status'       => 'SELESAI',
        // Hapus baris kolom jika tabel kamu belum punya field di bawah ini
        't_pdf_path'     => $dbPath,
        't_date_printed' => date('Y-m-d H:i:s'),
        't_printed_by'   => (int)$this->session->userdata('idUser'),
    ];
    $this->db->where('t_id', $id)->update($table, $updateData);
    $err = $this->db->error();
    if ($err['code']) {
        log_message('error', 'UPDATE '.$table.' error: '.json_encode($err));
        $this->db->trans_rollback(); @unlink($filePath);
        return $this->_json(500, ['ok'=>false,'msg'=>'DB error saat UPDATE ('.$table.').']);
    }

    if (!$this->db->trans_status()) {
        log_message('error', 'DB trans_status FALSE');
        $this->db->trans_rollback(); @unlink($filePath);
        return $this->_json(500, ['ok'=>false,'msg'=>'DB transaction failed.']);
    }
    $this->db->trans_commit();

    return $this->_json(200, ['ok'=>true,'pdf_url'=>$absUrl,'pdf_path'=>$dbPath]);
}


	private function _json(int $code, array $payload)
	{
		return $this->output
			->set_status_header($code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
	public function testDompdf(){
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

}
?>
