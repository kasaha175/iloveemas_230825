<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class TransactionController extends CI_Controller
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
		$this->load->library('Pdf');
		$this->load->library('cart');
    }

	// === HELPER: hitung total per baris, kompatibel lama/baru ===
	private function _cartLineTotal(array $row): float
	{
		// urutan: options.price_total → options.priceTotal → top-level priceTotal → subtotal
		if (isset($row['options']['price_total']) && is_numeric($row['options']['price_total'])) {
			return (float)$row['options']['price_total'];
		}
		if (isset($row['options']['priceTotal']) && is_numeric($row['options']['priceTotal'])) {
			return (float)$row['options']['priceTotal'];
		}
		if (isset($row['priceTotal']) && is_numeric($row['priceTotal'])) {
			return (float)$row['priceTotal'];
		}
		return (float)$row['subtotal'];
	}

	// === HELPER: normalkan SEMUA item di cart: qty=1, berat di options, total di options ===
	private function _normalizeCartForBuy(): void
	{
		$contents = $this->cart->contents();
		foreach ($contents as $row) {
			$opt = $row['options'] ?? [];

			// ambil identitas
			$matName = $opt['materialName'] ?? ($row['materialName'] ?? $row['name'] ?? 'Item');
			$matType = $opt['materialType'] ?? ($row['materialType'] ?? '-');
			$carat   = $opt['carat']        ?? ($row['carat'] ?? '');
			$types   = $opt['types']        ?? ($row['types'] ?? null);

			// berat: dari options.weight_raw, atau dari top-level 'weight'
			$weightRaw = null;
			if (isset($opt['weight_raw'])) {
				$weightRaw = ($opt['weight_raw'] === null || $opt['weight_raw'] === '') ? null : (float)$opt['weight_raw'];
			} elseif (isset($row['weight']) && is_numeric($row['weight'])) {
				$weightRaw = (float)$row['weight'];
			} elseif (isset($opt['weight']) && is_numeric($opt['weight'])) {
				$weightRaw = (float)$opt['weight'];
			}

			// tampilkan berat
			$weightDisp = ($weightRaw === null ? '-' : (string)$weightRaw);

			// harga per unit (per gram/unit)
			$priceUnit = (float)$row['price'];

			// total baris
			$lineTotal = null;
			if (isset($opt['price_total']) && is_numeric($opt['price_total'])) {
				$lineTotal = (float)$opt['price_total'];
			} elseif (isset($opt['priceTotal']) && is_numeric($opt['priceTotal'])) {
				$lineTotal = (float)$opt['priceTotal'];
			} elseif (isset($row['priceTotal']) && is_numeric($row['priceTotal'])) {
				$lineTotal = (float)$row['priceTotal'];
			} else {
				// fallback hitung manual: diamond (tanpa berat) = priceUnit * 1
				$lineTotal = round($priceUnit * ($weightRaw === null ? 1 : $weightRaw));
			}

			// update row: qty fix 1 + options standar
			$this->cart->update([
				'rowid'   => $row['rowid'],
				'qty'     => 1,
				'options' => array_merge($opt, [
					'materialName' => $matName,
					'materialType' => $matType,
					'carat'        => $carat,
					'types'        => $types,
					'weight'       => $weightDisp,
					'weight_raw'   => $weightRaw,
					'price_total'  => $lineTotal,
					'priceTotal'   => $lineTotal, // kompat lama
				]),
				// kompat lama jika view/logic masih baca top-level:
				'materialName' => $matName,
				'materialType' => $matType,
				'carat'        => $carat,
				'weight'       => $weightDisp,
				'types'        => $types,
				'priceTotal'   => $lineTotal,
			]);
		}
	}

	// === HELPER: hitung total & qty dari cart (pakai _cartLineTotal) ===
	private function _cartTotalsBuy(): array
	{
		$total = 0.0; $qtt = 0;
		foreach ($this->cart->contents() as $r) {
			$total += $this->_cartLineTotal($r);
			$qtt   += 1;
		}
		return [$total, $qtt];
	}

	// === HELPER: tulis ulang detail BUY ke DB dari cart (options-first) ===
	private function _writeBuyDetailsFromCart(int $idTransaction): void
	{
		$this->db->where('ti_t_id', $idTransaction)->delete('tb_transaction_items');

		foreach ($this->cart->contents() as $a) {
			$opt = $a['options'] ?? [];
			$row = [
				'ti_t_id'          => $idTransaction,
				'ti_material'      => $opt['materialName'] ?? ($a['materialName'] ?? $a['name']),
				'ti_material_type' => $opt['materialType'] ?? ($a['materialType'] ?? '-'),
				'ti_carat'         => $opt['carat'] ?? ($a['carat'] ?? ''),
				'ti_weight'        => array_key_exists('weight_raw', $opt) ? $opt['weight_raw'] : (is_numeric($a['weight'] ?? null) ? (float)$a['weight'] : null),
				'ti_price'         => (float)$a['price'], // per gram/unit
				'ti_high_low'      => (string)($opt['types'] ?? ($a['types'] ?? '')),
				'ti_price_total'   => $this->_cartLineTotal($a),
				'ti_date_created'  => $this->dateToday,
			];
			$this->TransactionModel->buyCheckoutItems($row);
		}
	}

	// Total satu baris cart: utamakan options.price_total
	private function _cartLineTotalSell(array $row): float
	{
		if (isset($row['options']['price_total']) && is_numeric($row['options']['price_total'])) {
			return (float)$row['options']['price_total'];
		}
		if (isset($row['options']['priceTotal']) && is_numeric($row['options']['priceTotal'])) {
			return (float)$row['options']['priceTotal'];
		}
		if (isset($row['priceTotal']) && is_numeric($row['priceTotal'])) {
			return (float)$row['priceTotal'];
		}
		return (float)$row['subtotal'];
	}

	// Hitung total & qtt untuk SELL
	private function _cartTotalsSell(): array
	{
		$total = 0.0; $qtt = 0;
		foreach ($this->cart->contents() as $r) {
			$total += $this->_cartLineTotalSell($r);
			$qtt   += 1;
		}
		return [$total, $qtt];
	}

	// Tulis ulang detail SELL ke DB dari cart (options-first)
	private function _writeSellDetailsFromCart(int $idTransaction): void
	{
		$this->db->where('ti_t_id', $idTransaction)->delete('tb_transaction_items_sell');
		foreach ($this->cart->contents() as $a) {
			$opt  = $a['options'] ?? [];
			$line = $this->_cartLineTotalSell($a);

			$dataItems = [
				'ti_t_id'          => $idTransaction,
				'ti_material'      => $opt['materialName'] ?? ($a['materialName'] ?? $a['name'] ?? 'Item'),
				'ti_material_type' => $opt['materialType'] ?? ($a['materialType'] ?? '-'),
				'ti_carat'         => $opt['carat'] ?? ($a['carat'] ?? ''),
				'ti_weight'        => array_key_exists('raw_weight', $opt) ? $opt['raw_weight']
										: (is_numeric($a['weight'] ?? null) ? (float)$a['weight'] : null),
				'ti_price'         => (float)$a['price'],   // per gram / per item, sesuai cabang
				'ti_price_total'   => $line,
				'ti_date_created'  => $this->dateToday,
			];
			$this->TransactionModel->sellCheckoutItems($dataItems);
		}
	}


    function index()
    {
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION";
		if ($authUser == true) {
			$this->data['customer'] = $this->MasterModel->customerData()->result();
			$this->data['content'] = $this->load->view('CustomerSelect', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
    }
	function list()
    {
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION";
		if ($authUser == true) {
			
			if(@$this->input->get('dateStart')){
				$this->db->where('t_date_created >=', $this->input->get('dateStart'));
				
			}
			if(@$this->input->get('dateEnd')){
				$this->db->where('t_date_created <=', $this->input->get('dateEnd'));
				
			}
			$this->db->where('t_status !=', 'SELESAI');
			// $this->db->where('is_delete', null);
			$this->data['transaction'] = $this->db->get('all_transaction')->result();
			$this->data['content'] = $this->load->view('ListTransaction', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
    }
	public function redirectTransaction($no_order)
	{
		// Pastikan user login
		if (!$this->session->userdata('authUser')) {
			redirect(base_url());
			return;
		}

		$this->data['title'] = 'TRANSACTION';

		// Bersihkan sesi/cart lama
		$this->session->unset_userdata([
			'idCustomer', 'idTransaction', 'jenis_transaksi', 'noOrder', 'no_order'
		]);
		$this->cart->destroy();

		// Ambil tipe transaksi dari tabel gabungan
		$transaction = $this->db->where('t_no_order', $no_order)
								->get('all_transaction')->row();

		if (!$transaction) {
			// Tidak ditemukan – kembali aman
			redirect(base_url('transaction-list'));
			return;
		}

		$type = strtoupper(trim($transaction->t_type)); // BUY atau SELL

		if ($type === 'SELL') {
			// Header SELL
			$cek_tr = $this->db->where('t_no_order', $no_order)
							->get('tb_transaction_sell')->row();
			if (!$cek_tr) {
				redirect(base_url('transaction-list'));
				return;
			}

			// Simpan session dasar
			$this->session->set_userdata([
				'idCustomer'      => $cek_tr->t_customer,
				'idTransaction'   => $cek_tr->t_id,
				'jenis_transaksi' => 'sell',
				'noOrder'         => $no_order,
				'no_order'        => $no_order, // kompatibilitas
			]);

			// Detail items
			$barang = $this->db->where('ti_t_id', $cek_tr->t_id)
							->get('tb_transaction_items_sell')->result();

			foreach ($barang as $item) {
				$isDiamond = strcasecmp(trim($item->ti_material), 'DIAMOND') === 0;

				// qty untuk cart wajib numerik > 0
				$qty = $isDiamond
					? 1
					: ((is_numeric($item->ti_weight) && (float)$item->ti_weight > 0) ? (float)$item->ti_weight : 0);

				if ($qty <= 0) {
					log_message('error', 'Cart insert skipped (invalid qty SELL): ti_id=' . $item->ti_id . ', weight=' . $item->ti_weight);
					continue;
				}

				$price      = (float)$item->ti_price;
				$priceTotal = (isset($item->ti_price_total) && is_numeric($item->ti_price_total))
								? (float)$item->ti_price_total
								: $price * $qty;

				$ok = $this->cart->insert([
					'id'    => $item->ti_id,
					'qty'   => $qty,
					'price' => $price,
					'name'  => $item->ti_material ?: 'Item',
					'options' => [
						'materialName'  => $item->ti_material,
						'materialType'  => $item->ti_material_type,
						'carat'         => $item->ti_carat,
						// untuk display di view: '-' jika diamond
						'weight'        => $isDiamond ? '-' : (string)$item->ti_weight,
						// raw numeric jika perlu perhitungan
						'raw_weight'    => $isDiamond ? null : (float)$item->ti_weight,
						'priceTotal'    => $priceTotal,
					],
				]);

				if (!$ok) {
					log_message('error', 'Cart insert failed (SELL): '. json_encode($item));
				}
			}

			redirect(base_url('transaction/sell/'));
			return;
		}

		// BUY
		$cek_tr = $this->db->where('t_no_order', $no_order)
						->get('tb_transaction')->row();
		if (!$cek_tr) {
			redirect(base_url('transaction-list'));
			return;
		}

		$this->session->set_userdata([
			'idCustomer'      => $cek_tr->t_customer,
			'idTransaction'   => $cek_tr->t_id,
			'jenis_transaksi' => 'buy',
			'noOrder'         => $no_order,
			'no_order'        => $no_order, // kompatibilitas
		]);

		$barang = $this->db->where('ti_t_id', $cek_tr->t_id)
						->get('tb_transaction_items')->result();

		foreach ($barang as $item) {
			$isDiamond = strcasecmp(trim($item->ti_material), 'DIAMOND') === 0;

			$qty = $isDiamond
				? 1
				: ((is_numeric($item->ti_weight) && (float)$item->ti_weight > 0) ? (float)$item->ti_weight : 0);

			if ($qty <= 0) {
				log_message('error', 'Cart insert skipped (invalid qty BUY): ti_id=' . $item->ti_id . ', weight=' . $item->ti_weight);
				continue;
			}

			$price      = (float)$item->ti_price;
			$priceTotal = (isset($item->ti_price_total) && is_numeric($item->ti_price_total))
							? (float)$item->ti_price_total
							: $price * $qty;

			$ok = $this->cart->insert([
				'id'    => $item->ti_id,
				'qty'   => $qty,
				'price' => $price,
				'name'  => $item->ti_material ?: 'Item',
				'options' => [
					'materialName'  => $item->ti_material,
					'materialType'  => $item->ti_material_type,
					'carat'         => $item->ti_carat,
					'weight'        => $isDiamond ? '-' : (string)$item->ti_weight,
					'raw_weight'    => $isDiamond ? null : (float)$item->ti_weight,
					'priceTotal'    => $priceTotal,
				],
			]);

			if (!$ok) {
				log_message('error', 'Cart insert failed (BUY): '. json_encode($item));
			}
		}

		redirect(base_url('transaction/buy/'));
	}

	public function confirmEdit()
	{
		// Wajib login
		if ($this->session->userdata('authUser') !== true) {
			return $this->output->set_status_header(401)
				->set_content_type('application/json','utf-8')
				->set_output(json_encode(['status'=>'gagal','msg'=>'Unauthorized']));
		}

		// Ambil & sanitasi input
		$post     = $this->input->post(NULL, true);
		$type     = strtolower($post['type'] ?? '');
		$id       = (int)($post['id'] ?? 0);
		$alasan   = trim($post['alasan'] ?? '');
		$password = (string)($post['password'] ?? '');

		if (!$id || !in_array($type, ['buy','sell'], true) || $alasan === '' || $password === '') {
			return $this->output->set_content_type('application/json','utf-8')
				->set_output(json_encode([
					'status' => 'gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				]));
		}

		// User & hash di DB
		$idUser   = (int)$this->session->userdata('idUser');
		$userData = $this->UserModel->userDataById($idUser)->row();

		if (!$userData) {
			return $this->output->set_content_type('application/json','utf-8')
				->set_output(json_encode([
					'status'=>'gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				]));
		}

		$stored = (string)($userData->u_password ?? '');
		$ok = false;

		// 1) Hash modern (bcrypt/argon) — password_verify
		$info = password_get_info($stored);
		if (!empty($info['algo'])) {
			$ok = password_verify($password, $stored);

			// (opsional) upgrade hash bila diperlukan
			if ($ok && password_needs_rehash($stored, PASSWORD_DEFAULT)) {
				$this->db->update('tb_user',
					['u_password' => password_hash($password, PASSWORD_DEFAULT)],
					['u_id' => $userData->u_id]
				);
			}
		} else {
			// 2) Fallback legacy: MD5 / SHA1
			//    (hindari timing attacks dengan hash_equals)
			if (strlen($stored) === 32 && ctype_xdigit($stored)) {
				$ok = hash_equals(strtolower($stored), md5($password));
			} elseif (strlen($stored) === 40 && ctype_xdigit($stored)) {
				$ok = hash_equals(strtolower($stored), sha1($password));
			}
		}

		if (!$ok) {
			return $this->output->set_content_type('application/json','utf-8')
				->set_output(json_encode([
					'status' => 'gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				]));
		}

		// Update alasan pada header transaksi
		$table = ($type === 'sell') ? 'tb_transaction_sell' : 'tb_transaction';

		$this->db->where('t_id', $id)->update($table, [
			't_alasan'     => $alasan,
			't_updated_at' => date('Y-m-d H:i:s'),
			't_updated_by' => $idUser
		]);

		// Ambil ulang header untuk response
		$row = $this->db->where('t_id', $id)->get($table)->row();
		if (!$row) {
			return $this->output->set_content_type('application/json','utf-8')
				->set_output(json_encode([
					'status'=>'gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				]));
		}

		// Kirim sukses + refresh CSRF
		return $this->output->set_content_type('application/json','utf-8')
			->set_output(json_encode([
				'status'        => 'berhasil',
				'no_transaksi'  => $row->t_no_order,
				'id'            => (int)$row->t_id,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}

	function deleteTransaction($no_order)
    {
        $authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->db->where('t_no_order', $no_order);
			$transaction = $this->db->get('all_transaction')->row();
			if($transaction->t_type == 'SELL'){
				$this->db->where('t_no_order', $no_order);
				$this->db->update('tb_transaction_sell', ['is_delete' => 'TRUE']);
				
			}
			else{
				$this->db->where('t_no_order', $no_order);
				$this->db->update('tb_transaction', ['is_delete' => 'TRUE']);
			}
			$data_session = array(
				'status' => 'success',
				'message' => "Transaksi Berhasil Dihapus",
			);
			$this->session->set_userdata($data_session);
			redirect(base_url()."transaction-list");
		}
		else {
			redirect(base_url());
		}
	}
    function lm()
    {
        $authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$this->data['content'] = $this->load->view('BuyLM', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function platinum(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$this->data['content'] = $this->load->view('BuyHighLow', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function paladium(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$this->data['content'] = $this->load->view('BuyHighLow', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function iridium(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$this->data['content'] = $this->load->view('BuyHighLow', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function rhodium(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$this->data['content'] = $this->load->view('BuyHighLow', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function ruthenium(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$this->data['content'] = $this->load->view('BuyHighLow', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function silver(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$this->data['content'] = $this->load->view('BuyHighLow', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
	}

	public function buy()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser   = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";

		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();

			// muat view utama ke dalam template
			$this->data['content'] = $this->load->view('Buy', $this->data, true); // <-- pakai 'Buy', bukan 'transaction/buy'
			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	function newCustomer(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION";
		if ($authUser == true) {
			$this->data['content'] = $this->load->view('CustomerNew', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function numberToRomanRepresentation($number) {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}
	function newCustomerProcess(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$month = $this->numberToRomanRepresentation(date('m', strtotime($this->dateToday)));
			$noUrut = $this->MasterModel->lastCustomer()->row("c_id");
			if($noUrut>0){
				$noUrut = $noUrut+1;
			}else{
				$noUrut = 1;
			}
			$year = date('Y', strtotime($this->dateToday));
			$year = $year[2].$year[3];
			echo $noOrder = "ILE/".$noUrut."/".$month."/".$year;
			$data = array(
				'c_name' => strtoupper($this->input->post("name")),
				'c_id_number' => strtoupper($this->input->post("idNumber")),
				'c_address' => strtoupper($this->input->post("address")),
				'c_resident_address' => strtoupper($this->input->post("resident_address")),
				'c_phone' => $this->input->post("phone"),
				'c_u_id' => $idUser,
				'c_no_order' => $noOrder,
				'c_date_created' => $this->dateToday,
			);
			$this->MasterModel->customerAdd($data);
			$data_session = array(
				'status' => 'success',
				'message' => "Add customer is success!",
			);
			$this->session->set_userdata($data_session);
			$key = $this->input->post('key');
			if($key!='add'){
				redirect(base_url()."transaction");
			}else{
				redirect(base_url()."master/customer");
			}
		}
		else {
			redirect(base_url());
		}
	}

	public function updateLive()
	{
		// Wajib POST + CSRF valid
		if (strtoupper($this->input->method()) !== 'POST') {
			return $this->output->set_status_header(405)
				->set_content_type('application/json','utf-8')
				->set_output(json_encode(['ok'=>false,'msg'=>'Method Not Allowed']));
		}

		$id = trim((string)$this->input->post('id', true));
		if ($id === '') {
			return $this->output->set_status_header(400)
				->set_content_type('application/json','utf-8')
				->set_output(json_encode([
					'ok'=>false,
					'msg'=>'Missing id',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				]));
		}

		// jangan di-explode; id kita sudah numeric murni dari Select2
		$this->session->unset_userdata('idTransaction');
		$this->session->set_userdata(['idCustomer' => $id]);
		$this->cart->destroy();

		// kirim balik status + token baru
		return $this->output->set_content_type('application/json','utf-8')
			->set_output(json_encode([
				'ok' => true,
				'idCustomer' => $id,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			]));
	}

	function buyCart()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
            $idMaterial = $this->uri->segment(3);
			$materialName = $this->MaterialModel->materialDataBy('m_id', $idMaterial,'Buy')->row("m_name");
            // echo "<pre>";
            // print_r ($materialName);
            // echo "</pre>";
			if (!empty($materialName)) {
				$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
				$idCustomer = $this->session->userdata("idCustomer");
				if(empty($idCustomer)){
					$idCustomer = 7;
				}
				$this->data['nameCustomer'] = $this->MasterModel->customerDatas($idCustomer)->row("c_name");
				$this->data['materianName'] = $materialName;
				$this->data['materialType'] = $this->MaterialModel->materialTypeData()->result();
				$this->data['carat'] = $this->MaterialModel->caratData($idMaterial)->result();
				$this->data['potongan'] = $this->MaterialModel->potonganData($idMaterial)->result();
				$this->data['content'] = $this->load->view('BuyCart', $this->data, true);
				$this->load->view("UserTemplate", $this->data);
			}
			else {
				redirect(base_url() . "transaction/buy/");
			}
		}
		else {
			redirect(base_url());
		}
	}
	
	public function buyAddToCart()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser   = $this->session->userdata("idUser");
		if (!$authUser) { return redirect(base_url()); }

		$idMaterial  = (int)$this->input->post('idMaterial');
		$types       = $this->input->post('types');         // high/low (opsional)
		$materialType= $this->input->post('materialType');  // bisa tahun potongan, dsb
		$carat       = $this->input->post('carat');         // bisa '24(99.9)' atau persen
		$weight      = $this->input->post('weight');        // string input
		$percentage  = $this->input->post('percentage');    // persen utk Pt/Pd/Rh/Ir/Ru/Ta

		$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
		$materialName = $this->MaterialModel->materialDataBy('m_id', $idMaterial, 'Buy')->row("m_name");

		// === Ambil formula (tetap dari kode aslinya) ===
		$rtiAU = abs($this->MaterialModel->formulaData()->row("f_rti_au"));
		$rtiAG = abs($this->MaterialModel->formulaData()->row("f_rti_ag"));
		$rtiPT = abs($this->MaterialModel->formulaData()->row("f_rti_pt"));
		$rtiRU = abs($this->MaterialModel->formulaData()->row("f_rti_ru"));
		$rtiTA = abs($this->MaterialModel->formulaData()->row("f_rti_ta"));

		$AUpotonganrulow   = $this->MasterModel->formulasData('rti-ru-low')->row('a');
		$AUpotonganruhigh  = $this->MasterModel->formulasData('rti-ru')->row('a');

		$AUpotonganK24     = $this->MasterModel->formulasData('rti-au')->row('a');
		$AUpotonganK2499   = $this->MasterModel->formulasData('rti-au')->row('h');
		$AUpresentasePotonganK24 = $this->MasterModel->formulasData('rti-au')->row('b');
		$AUpresentasePotonganCustProf = $this->MasterModel->formulasData('rti-au')->row('f');
		$AUpresentaseLMBaru = $this->MasterModel->formulasData('rti-au')->row('d');
		$AUpresentaseLMLama = $this->MasterModel->formulasData('rti-au')->row('e');
		$AUpotonganubs     = $this->MasterModel->formulasData('rti-au')->row('g');
		$AUgb_99           = $this->MasterModel->formulasData('rti-au')->row('gb_99');
		$AUgb_99_9         = $this->MasterModel->formulasData('rti-au')->row('gb_99_9');
		$potongan_lm       = $this->MasterModel->formulasData('rti-au')->row('potongan_lm');

		$AGpresentasePotonganAG     = $this->MasterModel->formulasData('rti-ag')->row('a');
		$AGpresentasePotonganAGLow  = $this->MasterModel->formulasData('rti-ag-low')->row('a');

		$PTpresentasePotonganPt     = $this->MasterModel->formulasData('rti-pt')->row('a');
		$PTpresentasePotonganPtLow  = $this->MasterModel->formulasData('rti-pt-low')->row('a');
		$PTpresentasePotonganPd     = $this->MasterModel->formulasData('rti-pt')->row('b');
		$PTpresentasePotonganPdLow  = $this->MasterModel->formulasData('rti-pt-low')->row('b');
		$PTpresentasePotonganRh     = $this->MasterModel->formulasData('rti-pt')->row('c');
		$PTpresentasePotonganRhLow  = $this->MasterModel->formulasData('rti-pt-low')->row('c');
		$PTpresentasePotonganIr     = $this->MasterModel->formulasData('rti-pt')->row('d');
		$PTpresentasePotonganIrLow  = $this->MasterModel->formulasData('rti-pt-low')->row('d');

		// === Siapkan variabel normalisasi cart ===
		$rowId      = uniqid('i');          // id baris cart
		$priceUnit  = 0.0;                  // harga per gram / per unit
		$weightRaw  = null;                 // null = item unit (diamond), float = gram
		$caratLabel = '';                   // label yang ditampilkan di tabel
		$typeLabel  = $materialType ?? '-'; // default '-'

		// === Hitung price/gram (atau unit) & set label sesuai material ===
		if ($idMaterial != 1) { // bukan DIAMOND
			// berat wajib numerik > 0 untuk item berbasis berat
			$weightRaw = (is_numeric($weight) && (float)$weight > 0) ? (float)$weight : 0;
			if ($weightRaw <= 0) {
				// skip item invalid
				$this->session->set_userdata([
					'status'  => 'danger',
					'message' => 'Berat tidak valid.',
				]);
				return redirect(base_url("transaction/buy/$idMaterial/?t=$types"));
			}

			if ($idMaterial == 2) { // EMAS PER PERHIASAN (KARAT)
				if ($carat == '24(99.9)')      $priceUnit = round($rtiAU + $AUpotonganK2499);
				else if ($carat == '24(99)')   $priceUnit = round($rtiAU + $AUpotonganK24);
				else if ($carat == 23)         $priceUnit = round((0.958 * $rtiAU) + (0.958 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 22)         $priceUnit = round((0.916 * $rtiAU) + (0.916 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 21)         $priceUnit = round((0.875 * $rtiAU) + (0.875 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 20)         $priceUnit = round((0.833 * $rtiAU) + (0.833 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 19)         $priceUnit = round((0.791 * $rtiAU) + (0.791 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 18)         $priceUnit = round((0.75  * $rtiAU) + (0.75  * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 17)         $priceUnit = round((0.708 * $rtiAU) + (0.708 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 16)         $priceUnit = round((0.666 * $rtiAU) + (0.666 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 15)         $priceUnit = round((0.625 * $rtiAU) + (0.625 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 14)         $priceUnit = round((0.583 * $rtiAU) + (0.583 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 13)         $priceUnit = round((0.541 * $rtiAU) + (0.541 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 12)         $priceUnit = round((0.5   * $rtiAU) + (0.5   * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 11)         $priceUnit = round((0.458 * $rtiAU) + (0.458 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 10)         $priceUnit = round((0.416 * $rtiAU) + (0.416 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 9)          $priceUnit = round((0.375 * $rtiAU) + (0.375 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 8)          $priceUnit = round((0.333 * $rtiAU) + (0.333 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 7)          $priceUnit = round((0.291 * $rtiAU) + (0.291 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 6)          $priceUnit = round((0.25  * $rtiAU) + (0.25  * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 5)          $priceUnit = round((0.208 * $rtiAU) + (0.208 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 4)          $priceUnit = round((0.166 * $rtiAU) + (0.166 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 3)          $priceUnit = round((0.125 * $rtiAU) + (0.125 * $rtiAU*($AUpresentasePotonganK24/100)));
				else if ($carat == 2)          $priceUnit = round((0.083 * $rtiAU) + (0.083 * $rtiAU*($AUpresentasePotonganK24/100)));
				else                            $priceUnit = 1;
				$caratLabel = (string)$carat;
			}
			else if ($idMaterial == 3) { // LM BARU
				$tahun_potongan = $this->input->post('tahun_potongan');
				$harga_potongan = json_decode($potongan_lm, true)[$tahun_potongan];
				$priceUnit  = $rtiAU + $harga_potongan;
				$typeLabel  = $tahun_potongan;
				$caratLabel = '24';
			}
			else if ($idMaterial == 4) { // LM LAMA
				$priceUnit  = $rtiAU + $AUpresentaseLMLama;
				$typeLabel  = '-';
				$caratLabel = '24';
			}
			else if ($idMaterial == 5) { // SILVER (Ag)
				if ($types === 'high') {
					$priceUnit = floor((($carat/100) * $rtiAG) + floor(($carat/100) * $rtiAG * ($AGpresentasePotonganAG/100)));
				} else {
					$priceUnit = round((($carat/100) * $rtiAG) + round(($carat/100) * $rtiAG * ($AGpresentasePotonganAGLow/100)));
				}
				$caratLabel = 'Ag '.$carat.' %';
			}
			else if ($idMaterial == 6) { // Pt
				if ($types === 'high') {
					if ($percentage < 100) $priceUnit = floor(($percentage/100) * floor($rtiPT + ($rtiPT * ($PTpresentasePotonganPt/100))));
					else                    $priceUnit = round($rtiPT + ($rtiPT * ($PTpresentasePotonganPt/100)));
				} else {
					if ($percentage < 100) $priceUnit = floor(($percentage/100) * floor($rtiPT + ($rtiPT * ($PTpresentasePotonganPtLow/100))));
					else                    $priceUnit = round($rtiPT + ($rtiPT * ($PTpresentasePotonganPtLow/100)));
				}
				$caratLabel = 'Pt '.$percentage.' %';
			}
			else if ($idMaterial == 7) { // Pd
				if ($types === 'high') {
					if ($percentage < 100) $priceUnit = floor(($percentage/100) * floor(($rtiPT) + (($rtiPT) * $PTpresentasePotonganPd/100)));
					else                    $priceUnit = round(($percentage/100) * round(($rtiPT) + (($rtiPT) * $PTpresentasePotonganPd/100)));
				} else {
					if ($percentage < 100) $priceUnit = floor(($percentage/100) * floor(($rtiPT) + (($rtiPT) * $PTpresentasePotonganPdLow/100)));
					else                    $priceUnit = round(($percentage/100) * round(($rtiPT) + (($rtiPT) * $PTpresentasePotonganPdLow/100)));
				}
				$caratLabel = 'Pd '.$percentage.' %';
			}
			else if ($idMaterial == 8) { // Ir
				if ($types === 'high') {
					$priceUnit = round(($rtiPT + ($rtiPT * ($PTpresentasePotonganIr/100))) * $percentage/100);
				} else {
					$priceUnit = round(($rtiPT + ($rtiPT * ($PTpresentasePotonganIrLow/100))) * $percentage/100);
				}
				$caratLabel = 'Ir '.$percentage.' %';
			}
			else if ($idMaterial == 9) { // Rh
				if ($types === 'high') {
					if ($percentage < 100) $priceUnit = floor(($percentage/100) * floor(($rtiPT) + (($rtiPT) * $PTpresentasePotonganRh/100)));
					else                    $priceUnit = round(($percentage/100) * round(($rtiPT) + (($rtiPT) * $PTpresentasePotonganRh/100)));
				} else {
					if ($percentage < 100) $priceUnit = floor(($percentage/100) * floor(($rtiPT) + (($rtiPT) * $PTpresentasePotonganRhLow/100)));
					else                    $priceUnit = round(($percentage/100) * round(($rtiPT) + (($rtiPT) * $PTpresentasePotonganRhLow/100)));
				}
				$caratLabel = 'Rh '.$percentage.' %';
			}
			else if ($idMaterial == 10) { // Au %
				$priceUnit  = ($rtiAU + ($rtiAU * $AUpresentasePotonganCustProf/100)) * $percentage/100;
				$caratLabel = 'Au '.$percentage.' %';
			}
			else if ($idMaterial == 17) { // UBS (per gram)
				$priceUnit  = ($rtiAU + $AUpotonganubs);
				$typeLabel  = '-';
				$caratLabel = '';
			}
			else if ($idMaterial == 19) { // Ru
				if ($types == 'high') $priceUnit = floor((($percentage/100) * $rtiRU) + floor(($percentage/100) * $rtiRU * ($AUpotonganruhigh/100)));
				else                  $priceUnit = floor((($percentage/100) * $rtiRU) + floor(($percentage/100) * $rtiRU * ($AUpotonganrulow/100)));
				$typeLabel  = $types;
				$caratLabel = 'RU '.$percentage.'%';
			}
			else if ($idMaterial == 21) { // Ta
				$priceUnit  = $rtiTA * $percentage / 100;
				$typeLabel  = '-';
				$caratLabel = $percentage.'%';
			}
			else if ($idMaterial == 23) { // Gold Bar GB
				$priceUnit  = ($carat == '24(99.9)') ? ($rtiAU + $AUgb_99_9) : ($rtiAU + $AUgb_99);
				$typeLabel  = '-';
				$caratLabel = 'K'.$carat;
			}
			else {
				// default fallback
				$priceUnit = (float)$rtiAU;
			}

			// === Insert ke cart (qty = 1, total di options) ===
			$lineTotal = round($priceUnit * $weightRaw);

			$this->cart->insert([
				'id'    => $rowId,
				'qty'   => 1,
				'price' => (float)$priceUnit,          // per gram
				'name'  => $materialName ?: 'Item',
				'options' => [
					'materialName' => $materialName,
					'materialType' => $typeLabel,
					'carat'        => $caratLabel,
					'types'        => $types ?: null,
					'weight'       => (string)$weightRaw,
					'weight_raw'   => $weightRaw,
					'price_total'  => $lineTotal,
					'priceTotal'   => $lineTotal,      // kompat lama
				],
				// kompat lama (kalau masih ada view lama yang baca top-level)
				'materialName' => $materialName,
				'materialType' => $typeLabel,
				'carat'        => $caratLabel,
				'weight'       => (string)$weightRaw,
				'types'        => $types ?: null,
				'prices'       => (float)$priceUnit,
				'priceTotal'   => $lineTotal,
			]);
		}
		else {
			// === DIAMOND / item tanpa berat: harga langsung ===
			$priceUnit  = (float)$this->input->post('price');
			$weightRaw  = null; // penanda unit item (bukan berbasis gram)
			$caratLabel = '-';
			$typeLabel  = '-';

			$this->cart->insert([
				'id'    => $rowId,
				'qty'   => 1,
				'price' => $priceUnit,           // langsung harga baris
				'name'  => $materialName ?: 'Item', // "DIAMOND"
				'options' => [
					'materialName' => $materialName,
					'materialType' => $typeLabel,
					'carat'        => $caratLabel,
					'types'        => $types ?: null,
					'weight'       => '-',       // tidak ada berat
					'weight_raw'   => null,
					'price_total'  => $priceUnit,
					'priceTotal'   => $priceUnit,
				],
				// kompat lama
				'materialName' => $materialName,
				'materialType' => $typeLabel,
				'carat'        => $caratLabel,
				'weight'       => '-',
				'types'        => $types ?: null,
				'prices'       => $priceUnit,
				'priceTotal'   => $priceUnit,
			]);
		}

		// === Hitung total & qty dari cart (pakai options.price_total bila ada) ===
		$total = 0.0; $qtt = 0;
		foreach ($this->cart->contents() as $a) {
			$line = 0.0;
			if (isset($a['options']['price_total']) && is_numeric($a['options']['price_total'])) {
				$line = (float)$a['options']['price_total'];
			} elseif (isset($a['options']['priceTotal']) && is_numeric($a['options']['priceTotal'])) {
				$line = (float)$a['options']['priceTotal'];
			} elseif (isset($a['priceTotal']) && is_numeric($a['priceTotal'])) {
				$line = (float)$a['priceTotal'];
			} else {
				$line = (float)$a['subtotal'];
			}
			$total += $line;
			$qtt   += 1;
		}

		// === Buat/update header transaksi BUY ===
		$idTransaction = $this->session->userdata("idTransaction");
		if (!$idTransaction) {
			$idCustomer = $this->session->userdata("idCustomer");
			if (empty($idCustomer)) { $idCustomer = 7; }

			$this->data['nameCustomer']  = $this->MasterModel->customerDatas($idCustomer)->row("c_name");
			$this->data['phoneCustomer'] = $this->MasterModel->customerDatas($idCustomer)->row("c_phone");

			$year    = date('Y', strtotime($this->dateToday));
			$noOrder = $this->db->query("SELECT COUNT(*) as count FROM tb_transaction WHERE YEAR(t_date_created)='$year'")->row('count');
			$noOrderNew = "PB-".substr($year,2).date('m',strtotime($this->dateToday))."-".(($noOrder?:0)+1);

			$header = [
				't_no_order'     => $noOrderNew,
				't_date_created' => $this->dateToday,
				't_status'       => 'PROSES',
				't_created_at'   => date('H:i:s', strtotime($this->dateToday)),
				't_created_by'   => $idUser,
				't_customer'     => $idCustomer,
				't_phone'        => $this->data['phoneCustomer'],
				't_note'         => '',
				't_type'         => 'BUY',
				't_paid_by'      => $this->data['nameCustomer'],
				't_receive_by'   => $idUser,
				't_price_total'  => $total,
				't_qtt'          => $qtt,
				't_visible'      => 1,
				't_price_grand_total'  => $total,
			];
			$idTransaction = $this->TransactionModel->buyCheckout($header);
			$this->session->set_userdata([
				'idTransaction'   => $idTransaction,
				'jenis_transaksi' => "buy",
			]);
		} else {
			$this->db->update('tb_transaction', [
				't_price_total' => $total,
				't_qtt'         => $qtt,
				't_price_grand_total'  => $total,
			], ['t_id' => $idTransaction]);
		}

		// === Tulis ulang detail dari cart ke DB (options-first) ===
		$this->db->where('ti_t_id', $idTransaction)->delete('tb_transaction_items');
		foreach ($this->cart->contents() as $a) {
			$opt = $a['options'] ?? [];
			// total baris
			$line = isset($opt['price_total']) ? (float)$opt['price_total']
				: (isset($opt['priceTotal']) ? (float)$opt['priceTotal']
				: (isset($a['priceTotal'])   ? (float)$a['priceTotal'] : (float)$a['subtotal']));
			$dataItems = [
				'ti_t_id'          => $idTransaction,
				'ti_material'      => $opt['materialName'] ?? ($a['materialName'] ?? $a['name']),
				'ti_material_type' => $opt['materialType'] ?? ($a['materialType'] ?? '-'),
				'ti_carat'         => $opt['carat'] ?? ($a['carat'] ?? ''),
				'ti_weight'        => array_key_exists('weight_raw', $opt) ? $opt['weight_raw']
									: (is_numeric($a['weight'] ?? null) ? (float)$a['weight'] : null),
				'ti_price'         => (float)$a['price'], // per gram/unit
				'ti_high_low'      => (string)($opt['types'] ?? ($a['types'] ?? '')),
				'ti_price_total'   => $line,
				'ti_date_created'  => $this->dateToday,
			];
			$this->TransactionModel->buyCheckoutItems($dataItems);
		}

		// kembali ke halaman cart material
		redirect(base_url()."transaction/buy/$idMaterial/?t=$types");
	}

	public function buyAddToCartReset()
	{
		$authUser = $this->session->userdata("authUser");
		if (!$authUser) { return redirect(base_url()); }

		$idMaterial = $this->input->get('idMaterial');
		$idRow      = $this->input->get('idRow');   // optional: hapus satu item
		$t          = $this->input->get('t');       // high/low dsb (tetap diteruskan ke view)

		$idTransaction = (int)$this->session->userdata("idTransaction");

		// Hapus di cart
		if (!empty($idRow)) {
			$this->cart->update([
				'rowid' => $idRow,
				'qty'   => 0
			]);
		} else {
			$this->cart->destroy();
		}

		// Normalisasi sisa cart
		$this->_normalizeCartForBuy();

		// Sinkronkan header + detail (jika masih ada transaksi berjalan)
		if ($idTransaction) {
			// total & qty
			list($total, $qtt) = $this->_cartTotalsBuy();

			// update header
			$this->db->update('tb_transaction', [
				't_price_total' => $total,
				't_qtt'         => $qtt,
			], ['t_id' => $idTransaction]);

			// tulis ulang detail
			$this->_writeBuyDetailsFromCart($idTransaction);
		}

		redirect(base_url("transaction/buy/$idMaterial/?t=$t"));
	}

    public function buyCheckout()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser   = $this->session->userdata("idUser");
		if (!$authUser) { return redirect(base_url()); }

		$idTransaction = (int)$this->session->userdata("idTransaction");
		if (!$idTransaction) { return redirect(base_url('transaction/buy')); }

		// Normalisasi cart lebih dulu (qty=1, total per baris terset rapi)
		$this->_normalizeCartForBuy();

		// Admin fee numerik (operator + / -)
		$op  = $this->input->get('operator');             // '+' atau '-'
		$adm = (float)$this->input->get('biayaAdmin');     // bisa kosong
		$biayaAdmin = ($op === '-') ? -abs($adm) : abs($adm);

		// Total & qty dari cart
		list($total, $qtt) = $this->_cartTotalsBuy();

		// GRAND TOTAL
		$grand = $total + $biayaAdmin;

		// Update header
		$this->db->update('tb_transaction', [
			't_status'      => 'CHECKOUT',
			't_price_total' => $total,
			't_price_admin' => $biayaAdmin,
			't_qtt'         => $qtt,
			't_price_grand_total'  => $grand, 
		], ['t_id' => $idTransaction]);

		// Tulis detail
		$this->_writeBuyDetailsFromCart($idTransaction);

		// Ambil no order untuk pesan sukses
		$noOrder = $this->db->select('t_no_order')->where('t_id', $idTransaction)
					->get('tb_transaction')->row('t_no_order');

		// Bereskan sesi/cart
		$this->session->unset_userdata('idCustomer');
		$this->cart->destroy();

		$this->session->set_userdata([
			'status'  => 'success',
			'message' => "Checkout no order <b>".htmlspecialchars($noOrder)."</b> is success!!",
		]);

		redirect(base_url("report/buy-print/$idTransaction/"));
	}

	public function sell()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser   = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION SELL";

		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$this->data['data']     = $this->MaterialModel->materialData('Sell')->result();

			// muat view utama ke dalam template
			$this->data['content'] = $this->load->view('Sell', $this->data, true); // <-- pakai 'Sell'
			$this->load->view("UserTemplate", $this->data);
		} else {
			redirect(base_url());
		}
	}

	function sellCart()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION SELL";
		if ($authUser == true) {
			$idMaterial = $this->uri->segment(3);
			$materialName = $this->MaterialModel->materialDataBy('m_id', $idMaterial,'Sell')->row("m_name");
			if (!empty($materialName)) {
				$idCustomer = $this->session->userdata("idCustomer");
				if(empty($idCustomer)){
					$idCustomer = 7;
				}
				$this->data['nameCustomer'] = $this->MasterModel->customerDatas($idCustomer)->row("c_name");
				$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
				$this->data['materianName'] = $materialName;
				$this->data['materialType'] = $this->MaterialModel->materialTypeData()->result();
				$this->data['potongan'] = $this->MaterialModel->potonganData($idMaterial)->result();
				$this->data['carat'] = $this->MaterialModel->caratData($idMaterial)->result();
				$this->data['content'] = $this->load->view('SellCart', $this->data, true);
				$this->load->view("UserTemplate", $this->data);
			}
			else {
				redirect(base_url() . "transaction/sell/");
			}
		}
		else {
			redirect(base_url());
		}
	}
	
	public function sellAddToCart()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser   = $this->session->userdata("idUser");
		if (!$authUser) { return redirect(base_url()); }

		$idMaterial   = (int)$this->input->post('idMaterial');
		$materialType = $this->input->post('materialType'); // bisa '-' / tahun / dsb
		$carat        = $this->input->post('carat');
		$weight       = $this->input->post('weight');

		$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
		$materialName = $this->MaterialModel->materialDataBy('m_id', $idMaterial, 'Sell')->row("m_name");

		// Ambil formula seperti kode asli
		$rtiAU = $this->MaterialModel->formulaData()->row("f_rti_au_sell");
		$rtiUbs= $this->MaterialModel->formulaData()->row("f_material_ubs_sell");
		$AUtambahAUG = $this->MasterModel->formulasData('material-au')->row('g');
		$AUPotongan  = $this->MasterModel->formulasData('material-au')->row('a');
		$potongan_ubs= $this->MasterModel->formulasData('material-ubs')->row('a');
		$potongan_lm = $this->MasterModel->formulasData('lm')->row('potongan_lm');
		$rtiAG = $this->MaterialModel->formulaData()->row("f_rti_ag_sell");
		$LMpresentaseLMBaru = $this->MasterModel->formulasData('lm')->row('b');
		$LMpresentaseLMLama = $this->MasterModel->formulasData('lm')->row('a');

		// Validasi & normalisasi weight (SELL semua item punya data)
		$weightRaw = (is_numeric($weight) && (float)$weight > 0) ? (float)$weight : 0;
		if ($weightRaw <= 0) {
			$this->session->set_userdata(['status'=>'danger','message'=>'Berat tidak valid.']);
			return redirect(base_url("transaction/sell/$idMaterial/"));
		}

		$rowId      = uniqid('s');
		$priceUnit  = 0.0;   // per gram / per item (sesuai cabang)
		$priceTotal = 0.0;   // total baris
		$typeLabel  = $materialType ?: '-';
		$caratLabel = '';

		// === Rumus sesuai kode asli per idMaterial ===
		if ($idMaterial == 16) { // AG 1000?
			$priceUnit  = round($rtiAG);
			$priceTotal = round($priceUnit * $weightRaw);
			$caratLabel = '100%';
			$typeLabel  = '-';
		}
		else if ($idMaterial == 18) { // UBS
			$priceUnit  = ($rtiUbs + $potongan_ubs);
			$priceTotal = $priceUnit * $weightRaw;
			$typeLabel  = '-';
			$caratLabel = '';
		}
		else if ($idMaterial == 15) { // material-au (24)
			$priceUnit  = $rtiAU + $AUPotongan;     // per gram
			$priceTotal = $priceUnit * $weightRaw;
			$typeLabel  = '-';
			$caratLabel = '24';
		}
		else if ($idMaterial == 14) { // LM LAMA tabel harga
			// ambil harga dasar per keping dari field f_*
			if ($weightRaw == 0.5)      { $price = $this->MaterialModel->formulaData()->row("f_nol5"); }
			else if ($weightRaw == 1)   { $price = $this->MaterialModel->formulaData()->row("f_1"); }
			else if ($weightRaw == 2)   { $price = $this->MaterialModel->formulaData()->row("f_2"); }
			else if ($weightRaw == 2.5) { $price = $this->MaterialModel->formulaData()->row("f_2_coma_5"); }
			else if ($weightRaw == 3)   { $price = $this->MaterialModel->formulaData()->row("f_3"); }
			else if ($weightRaw == 5)   { $price = $this->MaterialModel->formulaData()->row("f_5"); }
			else if ($weightRaw == 10)  { $price = $this->MaterialModel->formulaData()->row("f_10"); }
			else if ($weightRaw == 25)  { $price = $this->MaterialModel->formulaData()->row("f_25"); }
			else if ($weightRaw == 50)  { $price = $this->MaterialModel->formulaData()->row("f_50"); }
			else if ($weightRaw == 100) { $price = $this->MaterialModel->formulaData()->row("f_100"); }
			else if ($weightRaw == 250) { $price = $this->MaterialModel->formulaData()->row("f_250"); }
			else if ($weightRaw == 500) { $price = $this->MaterialModel->formulaData()->row("f_500"); }
			else if ($weightRaw == 1000){ $price = $this->MaterialModel->formulaData()->row("f_1000"); }
			else { $price = 1; }

			if ($weightRaw == 0.5) {
				// khusus 0.5
				$priceTotal = $price + ($LMpresentaseLMLama*0.5);
				$priceUnit  = $priceTotal; // per item (keping 0.5)
			} else {
				$price      = $price + $LMpresentaseLMLama;
				$priceUnit  = $price;                 // per gram / per item sesuai tabelmu
				$priceTotal = round($price * $weightRaw);
			}

			$typeLabel  = '-';
			$caratLabel = '24';
		}
		else if ($idMaterial == 13) { // LM BARU tabel + potongan tahun
			if     ($weightRaw == 0.5) { $base = $this->MaterialModel->formulaData()->row("f_nol5"); }
			elseif ($weightRaw == 1)   { $base = $this->MaterialModel->formulaData()->row("f_1"); }
			elseif ($weightRaw == 2)   { $base = $this->MaterialModel->formulaData()->row("f_2"); }
			elseif ($weightRaw == 2.5) { $base = $this->MaterialModel->formulaData()->row("f_2_coma_5"); }
			elseif ($weightRaw == 3)   { $base = $this->MaterialModel->formulaData()->row("f_3"); }
			elseif ($weightRaw == 5)   { $base = $this->MaterialModel->formulaData()->row("f_5"); }
			elseif ($weightRaw == 10)  { $base = $this->MaterialModel->formulaData()->row("f_10"); }
			elseif ($weightRaw == 25)  { $base = $this->MaterialModel->formulaData()->row("f_25"); }
			elseif ($weightRaw == 50)  { $base = $this->MaterialModel->formulaData()->row("f_50"); }
			elseif ($weightRaw == 100) { $base = $this->MaterialModel->formulaData()->row("f_100"); }
			elseif ($weightRaw == 250) { $base = $this->MaterialModel->formulaData()->row("f_250"); }
			elseif ($weightRaw == 500) { $base = $this->MaterialModel->formulaData()->row("f_500"); }
			elseif ($weightRaw == 1000){ $base = $this->MaterialModel->formulaData()->row("f_1000"); }
			else                       { $base = 1; }

			$tahun_potongan = $this->input->post('tahun_potongan');
			$harga_potongan = json_decode($potongan_lm, true)[$tahun_potongan];
			$priceUnit  = $base + $harga_potongan;    // mengikuti logika kamu
			$priceTotal = round($priceUnit * $weightRaw);
			$typeLabel  = $tahun_potongan;
			$caratLabel = '24';
		}
		else {
			// Cabang lain (kalau ada) → fallback sederhana
			$priceUnit  = 1;
			$priceTotal = round($priceUnit * $weightRaw);
			$caratLabel = (string)($carat ?? '');
		}

		// === Insert ke cart dengan schema options-first (qty=1) ===
		$this->cart->insert([
			'id'    => $rowId,
			'qty'   => 1,
			'price' => (float)$priceUnit,
			'name'  => $materialName ?: 'Item',
			'options' => [
				'materialName' => $materialName,
				'materialType' => $typeLabel,
				'carat'        => $caratLabel,
				'weight'       => (string)$weightRaw,
				'raw_weight'   => $weightRaw,
				'price_total'  => $priceTotal,
				'priceTotal'   => $priceTotal, // kompat lama
			],
			// kompat lama (kalau ada view lama yang masih baca top-level)
			'materialName' => $materialName,
			'materialType' => $typeLabel,
			'carat'        => $caratLabel,
			'weight'       => (string)$weightRaw,
			'prices'       => (float)$priceUnit,
			'priceTotal'   => $priceTotal,
		]);

		// === Buat / update header SELL + tulis detail ===
		list($total, $qtt) = $this->_cartTotalsSell();
		$idTransaction = $this->session->userdata("idTransaction");

		if (!$idTransaction) {
			$idCustomer = $this->session->userdata("idCustomer") ?: 7;
			$this->data['nameCustomer']  = $this->MasterModel->customerDatas($idCustomer)->row("c_name");
			$this->data['phoneCustomer'] = $this->MasterModel->customerDatas($idCustomer)->row("c_phone");

			$year = date('Y', strtotime($this->dateToday));
			$noOrder = $this->db->query("SELECT COUNT(*) as count FROM tb_transaction_sell WHERE YEAR(t_date_created)='$year'")->row('count');
			$noOrderNew = "PJ-".substr($year, 2).date('m', strtotime($this->dateToday))."-".(($noOrder ?: 0) + 1);


			$header = [
				't_no_order'     => $noOrderNew,
				't_date_created' => $this->dateToday,
				't_status'       => 'PROSES',
				't_created_at'   => date('H:i:s', strtotime($this->dateToday)),
				't_created_by'   => $idUser,
				't_customer'     => $idCustomer,
				't_phone'        => $this->data['phoneCustomer'],
				't_note'         => '',
				't_type'         => 'SELL',
				't_paid_by'      => $this->data['nameCustomer'],
				't_receive_by'   => $idUser,
				't_price_total'  => $total,
				't_visible'      => 1,
				't_qtt'          => $qtt,
				't_price_grand_total'  => $total,
			];
			$idTransaction = $this->TransactionModel->sellCheckout($header);
			$this->session->set_userdata(['idTransaction'=>$idTransaction,'jenis_transaksi'=>'sell']);
		} else {
			$this->db->update('tb_transaction_sell', ['t_price_total'=>$total,'t_qtt'=>$qtt], ['t_id'=>$idTransaction]);
		}

		$this->_writeSellDetailsFromCart($idTransaction);

		return redirect(base_url()."transaction/sell/$idMaterial/");
	}

	public function sellAddToCartReset()
	{
		$authUser = $this->session->userdata("authUser");
		if (!$authUser) { return redirect(base_url()); }

		$idMaterial   = $this->input->get('idMaterial');
		$idRow        = $this->input->get('idRow');
		$idTransaction= (int)$this->session->userdata("idTransaction");

		// Hapus item di cart
		if (!empty($idRow)) {
			$this->cart->update(['rowid' => $idRow, 'qty' => 0]);
		} else {
			$this->cart->destroy();
		}

		if ($idTransaction) {
			// Hitung ulang total & qty dari cart yang tersisa
			list($total, $qtt) = $this->_cartTotalsSell();

			// Update header: grand total sementara = total (admin dihitung saat checkout)
			$this->db->update('tb_transaction_sell', [
				't_price_total'        => $total,
				't_qtt'                => $qtt,
				't_price_grand_total'  => $total,
			], ['t_id' => $idTransaction]);

			// Tulis ulang detail (fungsi ini sudah menghapus detail lama)
			$this->_writeSellDetailsFromCart($idTransaction);
		}

		return redirect(base_url()."transaction/sell/$idMaterial/");
	}

	public function sellCheckout()
	{
		$authUser = $this->session->userdata("authUser");
		if (!$authUser) { return redirect(base_url()); }

		$idTransaction = (int)$this->session->userdata("idTransaction");
		if (!$idTransaction) { return redirect(base_url('transaction/sell')); }

		// Hapus detail lama
		$this->db->where('ti_t_id', $idTransaction)->delete('tb_transaction_items_sell');

		// Hitung total & qty dari cart (options-first)
		list($total, $qtt) = $this->_cartTotalsSell();

		// Admin fee
		$op        = $this->input->get('operator');
		$admVal    = (float)$this->input->get('biayaAdmin');
		$biayaAdmin= ($op === '-') ? -abs($admVal) : abs($admVal);

		$grand     = $total + $biayaAdmin;

		// Update header
		$this->db->update('tb_transaction_sell', [
			't_status'      => 'CHECKOUT',
			't_price_total' => $total,
			't_price_admin' => $biayaAdmin,
			't_qtt'         => $qtt,
			't_price_grand_total'  => $grand,
		], ['t_id'=>$idTransaction]);

		// Tulis detail dari cart
		$this->_writeSellDetailsFromCart($idTransaction);

		// Sukses
		$this->session->unset_userdata('idCustomer');
		$this->cart->destroy();

		// ambil no order untuk pesan
		$noOrder = $this->db->select('t_no_order')->where('t_id',$idTransaction)->get('tb_transaction_sell')->row('t_no_order');

		$this->session->set_userdata([
			'status'  => 'success',
			'message' => "Checkout no order <b>".htmlspecialchars($noOrder)."</b> is success!!",
		]);

		return redirect(base_url()."report/sell-print/$idTransaction/");
	}

	function sellDeleteTransaction(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idTransaction = $this->uri->segment(3);
			$this->TransactionModel->sellDeleteTransaction($idTransaction);
			$data_session = array(
				'status' => 'success',
				'message' => "Delete transaction is success!!",
			);
			$this->session->set_userdata($data_session); 
			redirect(base_url()."report/sell/");
		}
		else {
			redirect(base_url());
		}
	}
	function buyDeleteTransaction(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idTransaction = $this->uri->segment(3);
			$this->TransactionModel->buyDeleteTransaction($idTransaction);
			$data_session = array(
				'status' => 'success',
				'message' => "Delete transaction is success!!",
			);
			$this->session->set_userdata($data_session); 
			redirect(base_url()."report/buy/");
		}
		else {
			redirect(base_url());
		}
	}
	function buyPrint(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idTransaction = $this->uri->segment(3);
			$this->data['data'] = $this->TransactionModel->buyTransactionData($idTransaction)->result();
			if(empty($this->data['data'])){
				redirect(base_url()."report/buy/");
			}
			$noOrder = $this->TransactionModel->buyTransactionData($idTransaction)->row("t_no_order");
			$this->data['title'] = $noOrder;
			$this->data['detail'] = $this->TransactionModel->buyTransactionItemsData($idTransaction)->result();
			$this->load->view("PrintBuy", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function sellPrint(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idTransaction = $this->uri->segment(3);
			$this->data['data'] = $this->TransactionModel->sellTransactionData($idTransaction)->result();
			if(empty($this->data['data'])){
				redirect(base_url()."report/sell/");
			}
			$noOrder = $this->TransactionModel->sellTransactionData($idTransaction)->row("t_no_order");
			$this->data['title'] = $noOrder;
			$this->data['detail'] = $this->TransactionModel->sellTransactionItemsData($idTransaction)->result();
			$this->load->view("PrintSell", $this->data);
		}
		else {
			redirect(base_url());
		}
	}
	function keep(){
		$this->load->library('Pdf');
		$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
		ob_start();
		$pdf->SetTitle('INV BUY');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(20, 20, 20, 20);
		$pdf->AddPage();
		$text = "";
		for($i=1; $i <100; $i++){
			$text = $text.'<tr>
			<th style="width:5%;">'.$i.'</th>
			<th colspan="2" style="width:90%;">LAMPIRAN PENDUKUNG BUY</th>
	</tr>';
		};
		$lmpiranAkhir5 = '
		<table border="0.7" style="font-family: Arial Narrow;font-size:8sp; width:104%;">
			'.$text.'
		</table>';
		$pdf->writeHTML($lmpiranAkhir5, true, true, true, true, '');
		$pdf->Output('Dupak_Print.pdf', 'I');
	}
	public function chartDestroy()
	{
		$idTransaction = $this->session->userdata("idTransaction");
		$jenis = $this->session->userdata("jenis_transaksi");
		if($jenis=="sell"){
			$this->db->update('tb_transaction_sell', ['t_status' => 'SELESAI'], ['t_id' => $idTransaction]);
		}else{
			$this->db->update('tb_transaction', ['t_status' => 'SELESAI'], ['t_id' => $idTransaction]);
		}
		$this->cart->destroy();
	}

	public function getTransactions()
{
    $this->load->model('TransactionModel');
    $start = intval($this->input->post('start'));
    $length = intval($this->input->post('length'));
    $search = $this->input->post('search')['value'] ?? '';

    $transactions = $this->TransactionModel->getTransactions($start, $length, $search);
    $totalRecords = $this->TransactionModel->getTotalRecords();
    $filteredRecords = $this->TransactionModel->getFilteredRecords($search);

    // Tambahkan default value jika price_total tidak ada
    $data = [];
    foreach ($transactions as $key => $transaction) {
        $data[] = [
            'no' => $start + $key + 1,
            'action' => '<a href="' . base_url('transaction/redirect/' . $transaction->t_no_order) . '" class="btn btn-primary btn-sm">Action</a>',
            'transaction' => $transaction->t_type ?? 'N/A',
            'no_order' => $transaction->t_no_order ?? 'N/A',
            'status' => $transaction->t_status ?? 'N/A',
            'date' => $transaction->t_date_created ?? 'N/A',
            'customer' => $transaction->t_paid_by ?? 'N/A',
            'qty' => intval($transaction->t_qtt ?? 0),
            'price_total' => $transaction->t_price_total ?? 0
        ];
    }

    // Log untuk debugging
    log_message('debug', json_encode($data));

    echo json_encode([
        'draw' => intval($this->input->post('draw')),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data
    ]);
}

public function getCustomers()
{
    $search = trim((string)$this->input->get('search', true));
    $page   = max(1, (int)$this->input->get('page'));
    $limit  = 10;
    $offset = ($page - 1) * $limit;

    // ---- Build the filter once and reuse for COUNT + DATA
    $this->db->start_cache();
    $this->db->from('tb_customer c');

    if ($search !== '') {
        $like = $this->db->escape_like_str($search);
        $this->db->group_start()
                 ->like('c.c_name', $search, 'both')           // name
                 ->or_like('c.c_id_number', $search, 'both')    // id_number
                 // use raw where for CAST to avoid backticks on the expression
                 ->or_where("CAST(c.c_id AS CHAR) LIKE '%{$like}%'", null, false)
                 ->group_end();
    }
    $this->db->stop_cache();

    // ---- Count total (reusing the same filter)
    $total = (int)$this->db->count_all_results();

    // ---- Page data
    $this->db->select('c.c_id, c.c_name, c.c_id_number');
    $this->db->order_by('c.c_name', 'ASC');
    $this->db->limit($limit, $offset);
    $query = $this->db->get();
    $rows  = $query->result();

    // ---- Cleanup cache
    $this->db->flush_cache();

    // ---- Response for Select2 (+ minimal debug to help you)
    $out = [
        'results'    => $rows,
        'pagination' => ['more' => ($offset + $limit) < $total],
        // comment this out later if you don’t want it
        'debug'      => [
            'search' => $search,
            'page'   => $page,
            'limit'  => $limit,
            'count'  => $total,
        ],
    ];

    return $this->output
        ->set_content_type('application/json', 'utf-8')
        ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
        ->set_output(json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

	public function updateAllStatus() 
	{
		$this->load->model('TransactionModel'); // Pastikan model sudah dibuat
		try {
			// Proses update status di model
			$result = $this->TransactionModel->updateAllToSelesai();

			// Kirimkan respons sukses
			echo json_encode([
				'success' => true,
				'message' => 'All transactions have been updated to SELESAI.'
			]);
		} catch (Exception $e) {
			// Kirimkan respons error
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]);
		}
	}

	// tandai checkbox "checked" di HTML sesuai payload
	private function _applyChecksToHtml(string $html, array $checkedBranchIds, array $checkedPays): string
	{
		// cabang: input.cb-cabang[value="ID"]
		foreach ($checkedBranchIds as $id) {
			$v = preg_quote((string)$id, '/');
			$html = preg_replace(
				'/(<input[^>]*class="cb-cabang"[^>]*value="'.$v.'"[^>]*)(>)/i',
				'$1 checked="checked"$2',
				$html
			);
		}
		// payment: input.cb-pay[value="CASH"|"CREDIT"|...]
		foreach ($checkedPays as $p) {
			$v = preg_quote((string)$p, '/');
			$html = preg_replace(
				'/(<input[^>]*class="cb-pay"[^>]*value="'.$v.'"[^>]*)(>)/i',
				'$1 checked="checked"$2',
				$html
			);
		}
		return $html;
	}

	private function _generatePdf(string $html, string $destPath, string $paper='A4', string $orientation='portrait'): void
	{
		log_message('debug', 'PDF: mencoba Chrome...');
		try {
			$this->load->library('ChromePrinter');
			$res = $this->chromeprinter->htmlToPdf($html, $destPath, $paper, $orientation);
			log_message('debug', 'PDF: sukses Chrome -> '.$destPath.' | log: '.json_encode($res));
		} catch (\Throwable $e) {
			log_message('error', 'PDF: Chrome gagal => '.$e->getMessage());
			throw $e;
		}
	}

	private function _downloadFile(string $full, string $filename): void
	{
		$this->output
			->set_header('Content-Type: application/pdf')
			->set_header('Content-Length: ' . filesize($full))
			->set_header('Content-Disposition: attachment; filename="' . $filename . '"');
		readfile($full);
		exit;
	}

	public function savePrint($type='buy', $id=0)
	{
		// ===== Auth & method =====
		if ($this->session->userdata('authUser') !== true) {
			return $this->output->set_status_header(401)->set_output('Unauthorized');
		}
		if (strtoupper($this->input->method()) !== 'POST') {
			return $this->output->set_status_header(405)->set_output('Method Not Allowed');
		}

		// ===== Validasi type =====
		$type = strtolower((string)$type);
		if (!in_array($type, ['buy','sell'], true)) {
			return $this->output->set_status_header(400)->set_output('Bad Request');
		}
		$id = (int)$id;

		// ===== Ambil header transaksi =====
		$tTable = ($type === 'sell') ? 'tb_transaction_sell' : 'tb_transaction';
		$row    = $this->db->where('t_id', $id)->get($tTable)->row();
		if (!$row) {
			return $this->output->set_status_header(404)->set_output('Transaction not found');
		}

		// ===== Payload dari view =====
		$cabang      = $this->input->post('cabang');                  // array of {id,label}
		$payments    = $this->input->post('payments');                // array
		$paper       = strtoupper($this->input->post('paper') ?: 'A4');
		$orientation = strtolower($this->input->post('orientation') ?: 'portrait');
		$rawHtml     = (string)$this->input->post('rawHtml', false);  // HTML mentah (tanpa filtering)

		if ($rawHtml === '') {
			return $this->output->set_status_header(422)
				->set_content_type('application/json','utf-8')
				->set_output(json_encode(['ok'=>false,'msg'=>'rawHtml missing']));
		}

		// ===== Normalisasi cabang & payments =====
		$branches  = [];
		$branchIds = [];
		if (is_array($cabang)) {
			foreach ($cabang as $r) {
				$bid   = (int)($r['id'] ?? 0);
				$label = (string)($r['label'] ?? '');
				if ($bid > 0) {
					$branches[]  = ['id'=>$bid, 'label'=>$label];
					$branchIds[] = $bid;
				}
			}
		}
		$payments = is_array($payments) ? array_values(array_unique(array_map('strtoupper', $payments))) : [];

		// ===== Tandai checkbox di HTML (1:1 di PDF) =====
		$htmlFinal = $this->_applyChecksToHtml($rawHtml, $branchIds, $payments);

		// ===== Dapatkan no_order & tanggal Transaksi =====
		$noOrder = null;
		foreach (['t_no_order','s_no_order','ts_no_order','no_order','order_no','no_invoice'] as $c) {
			if (!empty($row->{$c})) { $noOrder = (string)$row->{$c}; break; }
		}
		if (!$noOrder) { $noOrder = strtoupper($type).'-'.$id; } // fallback aman

		$tDate = null;
		foreach (['t_date_created','date_created','created_at','t_date'] as $c) {
			if (!empty($row->{$c})) { $tDate = (string)$row->{$c}; break; }
		}
		if (!$tDate) { $tDate = date('Y-m-d'); }

		// ===== Build direktori penyimpanan (RELATIVE & ABSOLUTE) =====
		$tahun   = date('Y', strtotime($tDate));
		$bulan   = date('m', strtotime($tDate));
		$relDir  = 'uploads/prints/'.$type.'/'.$tahun.'/'.$bulan.'/';
		$absDir  = rtrim(str_replace('\\','/', FCPATH), '/').'/'.$relDir;

		// Pastikan folder ada
		if (!is_dir($absDir) && !@mkdir($absDir, 0775, true)) {
			return $this->output->set_status_header(500)
				->set_content_type('application/json','utf-8')
				->set_output(json_encode(['ok'=>false,'msg'=>'Tidak bisa membuat folder penyimpanan PDF.']));
		}

		// ===== Hapus SEMUA file lama transaksi ini (NOORDER-*.pdf) =====
		// Jika ada file yang tidak bisa dihapus (locked), hentikan dengan 423
		$pattern  = $absDir . $noOrder . '-*.pdf';
		$oldFiles = glob($pattern) ?: [];
		foreach ($oldFiles as $old) {
			if (@is_file($old) && !@unlink($old)) {
				return $this->output->set_status_header(423) // Locked
					->set_content_type('application/json','utf-8')
					->set_output(json_encode([
						'ok'  => false,
						'msg' => 'File PDF lama sedang dibuka. Tutup halaman PDF terlebih dahulu, lalu ulangi.'
					]));
			}
		}

		// ===== Nama file BARU: {NOORDER}-{YYYYMMDD-HHMMSS}.pdf =====
		$stamp    = date('Ymd-His');
		$fileName = $noOrder . '-' . $stamp . '.pdf';
		$relPath  = str_replace('\\','/', $relDir . $fileName); // simpan RELATIVE
		$absPath  = $absDir . $fileName;

		// ===== Generate PDF: tulis ke temp lalu rename (atomic-ish) =====
		try {
			$tmpPath = $absPath . '.part';

			// Chrome-only generator
			$this->_generatePdf($htmlFinal, $tmpPath, $paper, $orientation);

			// rename -> final
			if (!@rename($tmpPath, $absPath)) {
				@unlink($tmpPath);
				return $this->output->set_status_header(500)
					->set_content_type('application/json','utf-8')
					->set_output(json_encode(['ok'=>false,'msg'=>'Gagal menyimpan PDF akhir.']));
			}
		} catch (\Throwable $e) {
			return $this->output->set_status_header(500)
				->set_content_type('application/json','utf-8')
				->set_output(json_encode(['ok'=>false,'msg'=>'PDF gagal dibuat: '.$e->getMessage()]));
		}

		// ===== Simpan/Update jejak ke DB — 1 baris per transaksi =====
		$this->db->trans_start();

		$payloadDb = [
			't_type'        => strtoupper($type),
			't_id'          => (int)$id,
			'no_order'      => $noOrder,
			'paper'         => $paper,
			'orientation'   => $orientation,
			'branches_json' => json_encode($branches, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
			'payments_json' => json_encode($payments, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
			'html_raw'      => $rawHtml,
			'html_final'    => $htmlFinal,
			'pdf_path'      => $relPath, // RELATIVE (aktif terbaru)
			'updated_at'    => date('Y-m-d H:i:s'),
			'updated_by'    => (int)$this->session->userdata('idUser'),
		];

		// Upsert manual: coba update; jika tidak ada baris, insert
		$this->db->where(['t_type'=>strtoupper($type), 't_id'=>$id])->update('tb_transaction_prints', $payloadDb);
		if ($this->db->affected_rows() === 0) {
			$payloadDb['created_at'] = $payloadDb['updated_at'];
			$payloadDb['created_by'] = $payloadDb['updated_by'];
			$this->db->insert('tb_transaction_prints', $payloadDb);
		}

		$this->db->trans_complete();
		if (!$this->db->trans_status()) {
			return $this->output->set_status_header(500)
				->set_content_type('application/json','utf-8')
				->set_output(json_encode(['ok'=>false,'msg'=>'DB error saat menyimpan metadata PDF.']));
		}

		// ===== Response =====
		return $this->output->set_content_type('application/json','utf-8')
			->set_output(json_encode([
				'ok'       => true,
				'download' => base_url('transaction/print-file/'.$type.'/'.$id),
				'path'     => $relPath, // contoh: uploads/prints/buy/2025/09/NO123-20250909-183012.pdf
				'note'     => 'PDF berhasil dibuat. 1 transaksi 1 file aktif; nama file memakai timestamp.'
			], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
	}

	public function print_file($type = 'buy', $id = 0)
	{
		$type = strtolower((string)$type);
		$id   = (int)$id;
		if (!$id || !in_array($type, ['buy','sell'], true)) { show_404(); }

		// ========== DEBUG MODE ==========
		$debugParam   = $this->input->get('debug');
		if ($debugParam === null) { $debugParam = $this->input->get('dbg'); }
		$hasDebug     = ($debugParam !== null);
		$mode         = strtolower(trim((string)($debugParam ?? 'both')));

		// default bila ?debug tanpa nilai → both
		if ($mode === '') { $mode = 'both'; }

		$doView = $hasDebug && in_array($mode, ['1','true','yes','view','both','all','json'], true);
		$doLog  = $hasDebug && in_array($mode, ['1','true','yes','log','both','all'], true);

		// ========== HINTS ==========
		$noParam = trim((string)$this->input->get('no')); // no_order
		$crtRaw  = (string)($this->input->get('created_at') ?: $this->input->get('createdAt') ?: $this->input->get('ts'));
		$crtTs   = $crtRaw ? @strtotime($crtRaw) : null;
		$crt     = $crtTs ? date('Y-m-d H:i:s', $crtTs) : null;

		$trace = [
			'params' => ['type'=>$type,'t_id'=>$id,'no'=>$noParam,'created_at'=>$crt],
			'tries'  => [],
		];

		// ========== 1) Cari di tb_transaction_prints ==========
		$row = null;
		if ($this->db->table_exists('tb_transaction_prints')) {
			// a) pakai created_at hint jika ada
			$this->db->from('tb_transaction_prints');
			$this->db->where('t_type', strtoupper($type));
			$this->db->where('t_id', $id);
			if ($noParam !== '') $this->db->where('no_order', $noParam);
			if ($crt) $this->db->where('created_at <=', $crt);
			$this->db->order_by('created_at','DESC')->order_by('id','DESC')->limit(1);
			$row = $this->db->get()->row();
			$trace['tries'][] = ['table'=>'tb_transaction_prints','with_created_at'=> (bool)$crt,'with_no'=> ($noParam!==''),
								'found'=> (bool)($row && !empty($row->pdf_path))];

			// b) tanpa created_at bila a) tidak ketemu
			if ((!$row || empty($row->pdf_path)) && $crt) {
				$this->db->from('tb_transaction_prints');
				$this->db->where('t_type', strtoupper($type));
				$this->db->where('t_id', $id);
				if ($noParam !== '') $this->db->where('no_order', $noParam);
				$this->db->order_by('created_at','DESC')->order_by('id','DESC')->limit(1);
				$row = $this->db->get()->row();
				$trace['tries'][] = ['table'=>'tb_transaction_prints','with_created_at'=> false,'with_no'=> ($noParam!==''),
									'found'=> (bool)($row && !empty($row->pdf_path))];
			}

			// c) drop no_order bila b) tidak ketemu
			if ((!$row || empty($row->pdf_path)) && $noParam !== '') {
				$this->db->from('tb_transaction_prints');
				$this->db->where('t_type', strtoupper($type));
				$this->db->where('t_id', $id);
				$this->db->order_by('created_at','DESC')->order_by('id','DESC')->limit(1);
				$row = $this->db->get()->row();
				$trace['tries'][] = ['table'=>'tb_transaction_prints','drop_no_order'=> true,
									'found'=> (bool)($row && !empty($row->pdf_path))];
			}
		}

		// ========== 2) Fallback: tb_transaction_print (lama, opsional) ==========
		if ((!$row || empty($row->pdf_path)) && $this->db->table_exists('tb_transaction_print')) {
			$pathField = $this->db->field_exists('pdf_print','tb_transaction_print') ? 'pdf_print' : 'pdf_path';

			$this->db->select($pathField.' AS pdf_path')->from('tb_transaction_print')->where('t_id', $id);
			if ($this->db->field_exists('t_type','tb_transaction_print')) {
				$this->db->where('t_type', strtoupper($type));
			}
			if ($noParam !== '' && $this->db->field_exists('no_order','tb_transaction_print')) {
				$this->db->where('no_order', $noParam);
			}
			if ($crt && $this->db->field_exists('created_at','tb_transaction_print')) {
				$this->db->where('created_at <=', $crt);
			}
			if ($this->db->field_exists('created_at','tb_transaction_print')) {
				$this->db->order_by('created_at','DESC');
			}
			if ($this->db->field_exists('id','tb_transaction_print')) {
				$this->db->order_by('id','DESC');
			}
			$this->db->limit(1);
			$row2 = $this->db->get()->row();

			$trace['tries'][] = ['table'=>'tb_transaction_print','found'=> (bool)($row2 && !empty($row2->pdf_path))];

			if ($row2 && !empty($row2->pdf_path)) $row = $row2;
		}

		// ========== DEBUG OUTPUT (sebelum streaming) ==========
		if ($doLog) {
			// log sebagai ERROR agar muncul meski log_threshold rendah
			@log_message('error', '[print_file debug] '.json_encode([
				'selected_pdf' => $row->pdf_path ?? null,
				'trace'        => $trace
			], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
		}
		if ($doView) {
			return $this->output->set_content_type('application/json','utf-8')
				->set_output(json_encode([
					'ok'        => (bool)($row && !empty($row->pdf_path)),
					'pdf_path'  => $row->pdf_path ?? null,
					'trace'     => $trace
				], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
		}

		// ========== Validasi hasil ==========
		if (!$row || empty($row->pdf_path)) {
			show_error('PDF belum pernah dibuat untuk transaksi ini.', 404);
		}

		$pdfPath = (string)$row->pdf_path;

		// URL langsung?
		if (preg_match('#^https?://#i', $pdfPath)) { redirect($pdfPath, 'location', 302); return; }

		// Path lokal → normalisasi & keamanan
		$pdfPath = ltrim(str_replace('\\','/',$pdfPath), '/');
		$abs     = FCPATH.$pdfPath;

		$uploadsRoot = str_replace('\\','/', realpath(FCPATH.'uploads/prints'));
		$absReal     = str_replace('\\','/', realpath($abs));

		if (!$absReal || ($uploadsRoot && strpos($absReal, $uploadsRoot) !== 0)) {
			show_error('Lokasi file tidak valid.', 403);
		}
		if (!is_file($absReal)) {
			show_error('File PDF tidak ditemukan di disk.', 404);
		}

		// ========== Stream PDF ==========
		if (function_exists('apache_setenv')) @apache_setenv('no-gzip', '1');
		@ini_set('zlib.output_compression', 'Off');
		while (ob_get_level() > 0) { @ob_end_clean(); }
		$this->output->enable_profiler(false);

		$disp = ($this->input->get('dl') === '1') ? 'attachment' : 'inline';
		$size = @filesize($absReal);

		header('Content-Type: application/pdf');
		header('Content-Disposition: '.$disp.'; filename="'.basename($absReal).'"');
		if ($size) header('Content-Length: '.$size);
		header('Accept-Ranges: bytes');
		header('Cache-Control: private, max-age=0, must-revalidate');
		header('Pragma: public');
		header('Expires: 0');

		$fp = fopen($absReal, 'rb');
		fpassthru($fp);
		fclose($fp);
		exit;
	}

	private function _resolveOrderNo(object $row, string $type, int $id): string {
		foreach (['t_no_order','s_no_order','ts_no_order','no_order','order_no','no_invoice'] as $c) {
			if (!empty($row->{$c})) return (string)$row->{$c};
		}
		return strtoupper($type).'-'.$id;
	}
	private function _resolveTransDate(object $row): string {
		foreach (['t_date_created','date_created','created_at','t_date'] as $c) {
			if (!empty($row->{$c})) return (string)$row->{$c};
		}
		return date('Y-m-d');
	}
	private function _ensureDir(string $dir): void {
		if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
			throw new \RuntimeException('Tidak bisa membuat folder penyimpanan PDF.');
		}
	}
	/**
	 * Kembalikan [relDir, absDir, baseNameNoOrder] tanpa filename final (karena pakai timestamp).
	 */
	private function _resolvePdfDir(string $type, object $row, int $id): array {
		$noOrder = $this->_resolveOrderNo($row, $type, $id);
		$tDate   = $this->_resolveTransDate($row);
		$tahun   = date('Y', strtotime($tDate));
		$bulan   = date('m', strtotime($tDate));
		$relDir  = 'uploads/prints/'.$type.'/'.$tahun.'/'.$bulan.'/';
		$absDir  = rtrim(str_replace('\\','/', FCPATH), '/').'/'.$relDir;
		return [$relDir, $absDir, $noOrder];
	}


}
