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
			5=>'nameCreator', 6=>'nameReceive', 7=>'nameCustomer', 8=>'t_qtt', 9=>'t_price_grand_total'
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
			5=>'nameCreator', 6=>'nameReceive', 7=>'nameCustomer', 8=>'t_qtt', 9=>'t_price_grand_total'
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

    public function buy_items_json($id)
    {
        $this->output->set_content_type('application/json', 'utf-8');

        // Wajib login
        if ($this->session->userdata('authUser') !== true) {
            return $this->output
                ->set_status_header(401)
                ->set_output(json_encode([
                    'ok' => false,
                    'msg' => 'Unauthorized',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        }

        $id = (int)$id;
        if ($id <= 0) {
            return $this->_json_items_error('Invalid ID', 400);
        }

        // Header transaksi
        $qHeader = $this->TransactionModel->buyTransactionData($id);
        if (!$qHeader || $qHeader->num_rows() === 0) {
            return $this->_json_items_error('Transaction not found', 404);
        }
        $header = $qHeader->row();

        // Ambil items
        $qItems = $this->TransactionModel->buyTransactionItemsData($id);
        $items  = [];
        $subtotal = 0;

        foreach ($qItems->result() as $r) {
            // Penamaan kolom “ti_*” menyesuaikan skema yang sudah ada
            $name  = $r->ti_material  ?? ($r->ti_name ?? '-');
            $qty   = (float)($r->ti_qtt ?? $r->ti_qty ?? 0);
            $unit  = $r->ti_unit      ?? '';
            $price = (float)($r->ti_price ?? 0);
            $total = (float)($r->ti_price_total ?? ($qty * $price));

            $subtotal += $total;

            $items[] = [
                'name'  => $name,
                'qty'   => $qty,
                'unit'  => $unit,
                'price' => $price,
                'total' => $total,
            ];
        }

        // Grand total & admin fee (pakai kolom grand total jika ada, fallback ke price_total)
        $grand = (float)($header->t_price_grand_total ?? $header->t_price_total ?? 0);
        $admin = $grand - $subtotal;
        if ($admin < 0) $admin = 0;

        return $this->output->set_output(json_encode([
            'ok'          => true,
            'items'       => $items,
            'subtotal'    => $subtotal,
            'admin_fee'   => $admin,
            'grand_total' => $grand,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    public function sell_items_json($id)
    {
        $this->output->set_content_type('application/json', 'utf-8');

        if ($this->session->userdata('authUser') !== true) {
            return $this->output
                ->set_status_header(401)
                ->set_output(json_encode([
                    'ok' => false,
                    'msg' => 'Unauthorized',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        }

        $id = (int)$id;
        if ($id <= 0) {
            return $this->_json_items_error('Invalid ID', 400);
        }

        $qHeader = $this->TransactionModel->sellTransactionData($id);
        if (!$qHeader || $qHeader->num_rows() === 0) {
            return $this->_json_items_error('Transaction not found', 404);
        }
        $header = $qHeader->row();

        $qItems = $this->TransactionModel->sellTransactionItemsData($id);
        $items  = [];
        $subtotal = 0;

        foreach ($qItems->result() as $r) {
            $name  = $r->ti_material  ?? ($r->ti_name ?? '-');
            $qty   = (float)($r->ti_qtt ?? $r->ti_qty ?? 0);
            $unit  = $r->ti_unit      ?? '';
            $price = (float)($r->ti_price ?? 0);
            $total = (float)($r->ti_price_total ?? ($qty * $price));

            $subtotal += $total;

            $items[] = [
                'name'  => $name,
                'qty'   => $qty,
                'unit'  => $unit,
                'price' => $price,
                'total' => $total,
            ];
        }

        $grand = (float)($header->t_price_grand_total ?? $header->t_price_total ?? 0);
        $admin = $grand - $subtotal;
        if ($admin < 0) $admin = 0;

        return $this->output->set_output(json_encode([
            'ok'          => true,
            'items'       => $items,
            'subtotal'    => $subtotal,
            'admin_fee'   => $admin,
            'grand_total' => $grand,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    /** Helper error JSON untuk endpoint items */
    private function _json_items_error(string $msg, int $code = 400)
    {
        return $this->output
            ->set_status_header($code)
            ->set_output(json_encode([
                'ok' => false,
                'msg' => $msg,
                $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
            ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    public function downloadZip($type = 'buy')
    {
        $type = strtolower((string)$type);
        if (!in_array($type, ['buy','sell'], true)) show_404();

        $dateStart = $this->input->get('dateStart') ?: date('Y-m-01');
        $dateEnd   = $this->input->get('dateEnd')   ?: date('Y-m-t');
        $debugMode = (string)$this->input->get('debug') === '1';
        $dateEndInclusive = date('Y-m-d 23:59:59', strtotime($dateEnd));

        // ===== mapping tabel & kandidat kolom tanggal
        $table = ($type === 'buy') ? 'tb_transaction' : 'tb_transaction_sell';
        $dateCandidates = ['t_date_created','created_at','t_date_printed','date']; // sesuai skema kamu untuk buy & sell

        // deteksi kolom tanggal
        $dateCol = null;
        foreach ($dateCandidates as $cand) {
            if ($this->db->field_exists($cand, $table)) { $dateCol = $cand; break; }
        }
        if (!$dateCol) {
            return $this->output->set_status_header(500)
                ->set_content_type('application/json','utf-8')
                ->set_output(json_encode([
                    'ok' => false,
                    'error' => "Kolom tanggal tidak ditemukan pada tabel $table. Coba: ".implode(', ',$dateCandidates)
                ], JSON_PRETTY_PRINT));
        }

        // ===== ambil transaksi (ikutkan t_no_order bila ada, biar nama file rapi)
        $selectCols = "t_id, {$dateCol} AS tanggal";
        if ($this->db->field_exists('t_no_order', $table)) $selectCols .= ", t_no_order";

        $q = $this->db->select($selectCols)
            ->from($table)
            ->where("{$dateCol} >=", $dateStart)
            ->where("{$dateCol} <=", $dateEndInclusive)
            ->order_by("{$dateCol}", 'ASC')
            ->get();

        if ($q === false) {
            $err = $this->db->error();
            return $this->output->set_status_header(500)
                ->set_content_type('application/json','utf-8')
                ->set_output(json_encode([
                    'ok' => false,
                    'sql' => $this->db->last_query(),
                    'db_error_code' => $err['code'] ?? null,
                    'db_error_msg'  => $err['message'] ?? null,
                ], JSON_PRETTY_PRINT));
        }

        $rows = $q->result();
        if (empty($rows)) {
            return $this->output->set_content_type('application/json','utf-8')
                ->set_output(json_encode([
                    'ok' => true,
                    'info' => [
                        'type' => $type, 'table' => $table, 'date_column' => $dateCol,
                        'filter' => [$dateStart, $dateEndInclusive], 'count' => 0
                    ],
                    'data' => [], 'zip' => ['added' => 0, 'skipped' => 0]
                ], JSON_PRETTY_PRINT));
        }

        // ===== proses ZIP + logging
        $this->load->library('zip');
        $log = [];
        $added = 0; $skipped = 0;

        foreach ($rows as $r) {
            // 1) coba ambil dari tb_transaction_prints (paling baru)
            $pdfRow = $this->db->select('pdf_path, created_at')
                ->from('tb_transaction_prints')
                ->where('t_type', strtoupper($type))    // 'BUY' atau 'SELL'
                ->where('t_id', (int)$r->t_id)
                ->order_by('created_at','DESC')
                ->order_by('id','DESC')
                ->limit(1)
                ->get()->row();

            $src    = null;
            $dbPath = $pdfRow->pdf_path ?? null;

            // 2) fallback ke kolom lama di tabel masing-masing (t_pdf_path)
            if (!$dbPath) {
                if ($type === 'buy'  && $this->db->field_exists('t_pdf_path', 'tb_transaction')) {
                    $fb = $this->db->select('t_pdf_path')->from('tb_transaction')
                        ->where('t_id', (int)$r->t_id)->limit(1)->get()->row();
                    if ($fb && !empty($fb->t_pdf_path)) {
                        $dbPath = $fb->t_pdf_path;
                        $src    = 'tb_transaction.t_pdf_path';
                    }
                } elseif ($type === 'sell' && $this->db->field_exists('t_pdf_path', 'tb_transaction_sell')) {
                    $fb = $this->db->select('t_pdf_path')->from('tb_transaction_sell')
                        ->where('t_id', (int)$r->t_id)->limit(1)->get()->row();
                    if ($fb && !empty($fb->t_pdf_path)) {
                        $dbPath = $fb->t_pdf_path;
                        $src    = 'tb_transaction_sell.t_pdf_path';
                    }
                }
            }

            if (!$src) $src = $dbPath ? 'tb_transaction_prints' : 'none';

            // 3) resolve ke absolute path & cek file
            $abs    = $this->_resolvePdfPath($dbPath);
            $exists = ($abs && is_file($abs));

            // 4) nama file di ZIP (pakai t_no_order kalau ada)
            $noOrder = isset($r->t_no_order) && $r->t_no_order ? preg_replace('~[^A-Za-z0-9_\-]~','-', $r->t_no_order) : null;
            $basename = $noOrder ?: (string)(int)$r->t_id;
            $nameInZip = sprintf('%s/%s-%s.pdf', $type, $type, $basename);

            // 5) add/skip
            if ($exists) {
                $this->zip->read_file($abs, false, $nameInZip);
                $status = 'added';
                $added++;
            } else {
                $status = $dbPath ? 'skipped_missing_file' : 'skipped_no_path';
                $skipped++;
            }

            $log[] = [
                't_id'        => (int)$r->t_id,
                't_no_order'  => $noOrder ?: null,
                'tanggal'     => $r->tanggal,
                'date_column' => $dateCol,
                'pdf_path_db' => $dbPath,
                'pdf_path_abs'=> $abs,
                'source'      => $src,
                'status'      => $status,
                'zip_name'    => $nameInZip
            ];
        }

        // ===== mode debug → tampilkan log JSON
        if ($debugMode) {
            return $this->output->set_content_type('application/json','utf-8')
                ->set_output(json_encode([
                    'ok' => true,
                    'info' => [
                        'type' => $type, 'table' => $table, 'date_column' => $dateCol,
                        'filter' => [$dateStart, $dateEndInclusive],
                        'rows_scanned' => count($rows)
                    ],
                    'zip' => ['added' => $added, 'skipped' => $skipped],
                    'data' => $log
                ], JSON_PRETTY_PRINT));
        }

        // ===== download ZIP atau beri info kosong
        if ($added === 0) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json','utf-8')
                ->set_output(json_encode([
                    'ok' => false,
                    'message' => 'Tidak ada PDF yang bisa di-zip untuk filter tersebut.',
                    'summary' => ['added' => $added, 'skipped' => $skipped],
                    'hint' => 'Gunakan ?debug=1 untuk melihat detail path yang hilang.'
                ], JSON_PRETTY_PRINT));
        }

        $zipName = sprintf('report-%s-%s_to_%s.zip',
            $type, date('Ymd', strtotime($dateStart)), date('Ymd', strtotime($dateEnd))
        );

        header('X-Zip-Report: type='.$type.', added='.$added.', skipped='.$skipped);
        $this->zip->download($zipName);
    }

    private function _resolvePdfPath($dbPath)
    {
        $path = trim((string)$dbPath);
        if ($path === '') return '';
        if (preg_match('~^([a-zA-Z]:\\\\|/)~', $path)) return $path; // absolute (Windows/Unix)
        if (preg_match('~^https?://~i', $path)) return '';          // URL → tidak kita unduh di sini
        return FCPATH . ltrim($path, '/\\');                        // relatif → FCPATH
    }

}
