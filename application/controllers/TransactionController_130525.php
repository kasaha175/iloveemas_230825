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
				$this->db->where('t_date_created =<', $this->input->get('dateEnd'));
				
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
	function redirectTransaction($no_order)
    {
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION";
		if ($authUser == true) {
			$this->session->unset_userdata('idCustomer');
			$this->session->unset_userdata('idTransaction');
			$this->cart->destroy();
			$this->db->where('t_no_order', $no_order);
			$transaction = $this->db->get('all_transaction')->row();
			if($transaction->t_type == 'SELL'){
				$this->db->where('t_no_order', $no_order);
				$cek_tr = $this->db->get('tb_transaction_sell')->row();
				
				$id = $cek_tr->t_customer;
				$data_session = array(
					'idCustomer' => $id,
					'idTransaction' => $cek_tr->t_id,
					'jenis_transaksi' => 'sell'
				);
				$this->session->set_userdata($data_session);
				$this->db->where('ti_t_id', $cek_tr->t_id);
				$barang = $this->db->get('tb_transaction_items')->result();
				foreach($barang as $key => $value){
					
					$data = array(
						'id' => $value->ti_id,
						'qty' => $value->ti_weight,
						'price' => $value->ti_price,
						'prices' => $value->ti_price,
						'name' => 'T-Shirt',
						'materialName' => $value->ti_material,
						'materialType' => $value->ti_material_type,
						'carat' => $value->ti_carat,
						'weight' => $value->ti_weight,
						'priceTotal' => $value->ti_price_total,
					);
					
					$this->cart->insert($data);
					// echo "<pre>";
					// print_r($data);
					// echo "</pre>";
				}
				redirect(base_url('transaction/sell/'));
			}
			else{
				$this->db->where('t_no_order', $no_order);
				$cek_tr = $this->db->get('tb_transaction')->row();
				$id = $cek_tr->t_customer;
				$data_session = array(
					'idCustomer' => $id,
					'idTransaction' => $cek_tr->t_id,
					'jenis_transaksi' => 'buy'
				);
				$this->session->set_userdata($data_session);
				$this->db->where('ti_t_id', $cek_tr->t_id);
				$barang = $this->db->get('tb_transaction_items')->result();
				foreach($barang as $key => $value){
					
					$data = array(
						'id' => $value->ti_id,
						'qty' => $value->ti_weight,
						'price' => $value->ti_price,
						'prices' => $value->ti_price,
						'name' => 'T-Shirt',
						'materialName' => $value->ti_material,
						'materialType' => $value->ti_material_type,
						'carat' => $value->ti_carat,
						'weight' => $value->ti_weight,
						'priceTotal' => $value->ti_price_total,
					);
					
					$this->cart->insert($data);
					// echo "<pre>";
					// print_r($data);
					// echo "</pre>";
				}
				// echo "<pre>";
				// 	print_r($this->cart->contents());
				// 	echo "</pre>";
				// print_r($this->cart->contents());

				redirect(base_url('transaction/buy/'));
			}
		}
		else {
			redirect(base_url());
		}
    }
	function confirmEdit(){
		$datapost = $this->input->post();
		$idUser = $this->session->userdata("idUser");
		$userData = $this->UserModel->userDataById($idUser)->row();
		if($userData->u_password == md5($datapost['password'])){
			$data = array(
				't_alasan' => $datapost['alasan']
			);
			if($datapost['type'] == 'sell'){
				$this->db->update('tb_transaction_sell', $data, ['t_id' => $datapost['id']]);
				$this->db->where('t_id', $datapost['id']);
				$cek_data = $this->db->get('tb_transaction_sell')->row();
			}
			else{
				$this->db->update('tb_transaction', $data, ['t_id' => $datapost['id']]);
				$this->db->where('t_id', $datapost['id']);
				$cek_data = $this->db->get('tb_transaction')->row();
			}
			echo json_encode([
				'status' => 'berhasil',
				'no_transaksi' => $cek_data->t_no_order,
			]);
			// $this->redirectTransaction($cek_data->t_no_order);
		}
		else{
			echo json_encode([
				'status' => 'gagal'
			]);
		}
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
	function buy()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION BUY";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			//$this->data['data'] = $this->MaterialModel->materialData('Buy')->result();
			$this->data['content'] = $this->load->view('Buy', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
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
	function updateLive(){
		$id=$this->input->post('id'); 
		$id = explode(" ", $id);
		$id = $id[0];
		$this->session->unset_userdata('idTransaction');
		$data_session = array(
			'idCustomer' => $id
		);
		$this->session->set_userdata($data_session);
		$this->cart->destroy();
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
	function buyAddToCart(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idMaterial = $this->input->post('idMaterial');
			$dt = $this->input->post();
			$types = $this->input->post('types');
			$materialType = $this->input->post('materialType');
			$carat = $this->input->post('carat');
			$weight = $this->input->post('weight');
			$percentage = $this->input->post('percentage');
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$materialName = $this->MaterialModel->materialDataBy('m_id', $idMaterial,'Buy')->row("m_name");
			//formula
			$rtiAU = abs($this->MaterialModel->formulaData()->row("f_rti_au"));
			$rtiAG = abs($this->MaterialModel->formulaData()->row("f_rti_ag"));
			$rtiPT = abs($this->MaterialModel->formulaData()->row("f_rti_pt"));
			$rtiRU = abs($this->MaterialModel->formulaData()->row("f_rti_ru"));
			$rtiTA = abs($this->MaterialModel->formulaData()->row("f_rti_ta"));
			$AUpotonganrulow = $this->MasterModel->formulasData('rti-ru-low')->row('a');
			$AUpotonganruhigh = $this->MasterModel->formulasData('rti-ru')->row('a');
			$AUpotonganK24 = $this->MasterModel->formulasData('rti-au')->row('a');
			$AUpotonganK2499 = $this->MasterModel->formulasData('rti-au')->row('h');
			$AUpresentasePotonganK24 = $this->MasterModel->formulasData('rti-au')->row('b');
			$AUpresentasePotonganCustProf = $this->MasterModel->formulasData('rti-au')->row('f');
			$AUpresentaseLMBaru = $this->MasterModel->formulasData('rti-au')->row('d');
			$AUpresentaseLMLama = $this->MasterModel->formulasData('rti-au')->row('e');
			$AUpotonganubs = $this->MasterModel->formulasData('rti-au')->row('g');
			$AUgb_99 = $this->MasterModel->formulasData('rti-au')->row('gb_99');
			$AUgb_99_9 = $this->MasterModel->formulasData('rti-au')->row('gb_99_9');
			$potongan_lm = $this->MasterModel->formulasData('rti-au')->row('potongan_lm');
			$AGpresentasePotonganAG = $this->MasterModel->formulasData('rti-ag')->row('a');
			$AGpresentasePotonganAGLow = $this->MasterModel->formulasData('rti-ag-low')->row('a');
			$PTpresentasePotonganPt = $this->MasterModel->formulasData('rti-pt')->row('a');
			$PTpresentasePotonganPtLow = $this->MasterModel->formulasData('rti-pt-low')->row('a');
			$PTpresentasePotonganPd = $this->MasterModel->formulasData('rti-pt')->row('b');
			$PTpresentasePotonganPdLow = $this->MasterModel->formulasData('rti-pt-low')->row('b');
			$PTpresentasePotonganRh = $this->MasterModel->formulasData('rti-pt')->row('c');
			$PTpresentasePotonganRhLow = $this->MasterModel->formulasData('rti-pt-low')->row('c');
			$PTpresentasePotonganIr = $this->MasterModel->formulasData('rti-pt')->row('d');
			$PTpresentasePotonganIrLow = $this->MasterModel->formulasData('rti-pt-low')->row('d');
			
			//cart
			
			foreach($this->cart->contents() as $a) {
				$idLast = ($a['id']);
			}
			if (!empty($idLast)) {
				$idLast = $idLast + 1;
			}
			else {
				$idLast = 1;
			}
			if ($idMaterial != 1) {
				if ($idMaterial == 2) {
					if ($carat == '24(99.9)') {
						$price = round($rtiAU + $AUpotonganK2499);
						// $price = round(($rtiAU + ($rtiAU * - (16/100))) * $percentage/100);
						// $priceTotal = ($price * $weight);
					}
					else if ($carat == '24(99)') {
						$price = round($rtiAU + $AUpotonganK24);
					}
					else if ($carat == 23) {
						 $price = round((0.958 * $rtiAU) + (0.958 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 22) {
						 $price = round((0.916 * $rtiAU) + (0.916 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 21) {
						$price = round((0.875 * $rtiAU) + (0.875 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 20) {
						$price = round((0.833 * $rtiAU) + (0.833 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 19) {
						$price = round((0.791 * $rtiAU) + (0.791 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 18) {
						$price = round((0.75 * $rtiAU) + (0.75 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 17) {
						$price = round((0.708 * $rtiAU) + (0.708 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 16) {
						$price = round((0.666 * $rtiAU) + (0.666 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 15) {
						$price = round((0.625 * $rtiAU) + (0.625 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 14) {
						$price = round((0.583 * $rtiAU) + (0.583 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 13) {
						$price = round((0.541 * $rtiAU) + (0.541 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 12) {
						$price = round((0.5 * $rtiAU) + (0.5 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 11) {
						$price = round((0.458 * $rtiAU) + (0.458 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 10) {
						$price = round((0.416 * $rtiAU) + (0.416 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 9) {
						$price = round((0.375 * $rtiAU) + (0.375 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 8) {
						$price = round((0.333 * $rtiAU) + (0.333 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 7) {
						$price = round((0.291 * $rtiAU) + (0.291 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 6) {
						$price = round((0.25 * $rtiAU) + (0.25 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 5) {
						$price = round((0.208 * $rtiAU) + (0.208 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 4) {
						$price = round((0.166 * $rtiAU) + (0.166 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else if ($carat == 3) {
						$price = round((0.125 * $rtiAU) + (0.125 * $rtiAU*($AUpresentasePotonganK24/100)));
					}else if ($carat == 2) {
						$price = round((0.083 * $rtiAU) + (0.083 * $rtiAU*($AUpresentasePotonganK24/100)));
					}
					else {
						$price = 1;
					}
					$priceTotal = round($price * $weight);
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'materialType' => $materialType,
						'carat' => $carat,
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
                }else if ($idMaterial == 3){
					
					// Rumus Lama
					// // if($weight<1){
					// 	$pricepergram = $rtiAU + $AUpresentaseLMBaru;
					// 	$price = $pricepergram*$weight;
						
					// // }else{
					// // 	$price = $rtiAU;
					// // 	$priceTotal = round($price * $weight);
					// // }
					// End Rumus Lama
					// Rumus Baru
					$tahun_potongan = $this->input->post('tahun_potongan');
					$harga_potongan = json_decode($potongan_lm, true)[$tahun_potongan];
					$pricepergram = $rtiAU + $harga_potongan;
					$price = $pricepergram*$weight;
					
					// End Rumus Baru
					$priceTotal = round($price);
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $pricepergram,
						'prices' => $pricepergram,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'materialType' => $tahun_potongan,
						'carat' => '24',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
                }else if ($idMaterial == 4){
					// if($weight<1){
                		$pricepergram = $rtiAU + $AUpresentaseLMLama;
						$price = $pricepergram*$weight;
						$priceTotal = round($price);
					// }else{
					// 	$price = $rtiAU + $AUpresentaseLMLama;
					// 	$priceTotal = round($price * $weight);
					// }
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $pricepergram,
						'prices' => $pricepergram,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'materialType' => '-',
						'carat' => '24',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
                }else if ($idMaterial == 5){
					// echo $AGpresentasePotonganAGLow;
					// die;
					if($types=='high'){
						// echo $types;die;
						// if ($carat == 1000) { 
						// 	$price = round($rtiAG + ($rtiAG * $AGpresentasePotonganAG/100));
						// }
						// else if ($carat == 925) {
						// 	$price = round((0.925 * $rtiAG) + (0.925  * $rtiAG * ($AGpresentasePotonganAG/100)));
						// }
						// else if ($carat == 900) {
						// 	$price = round((0.90 * $rtiAG) + (0.90  * $rtiAG * ($AGpresentasePotonganAG/100)));
						// }else if ($carat == 500) {
						// 	$price = round((0.50 * $rtiAG) + (0.50  * $rtiAG * ($AGpresentasePotonganAG/100)));
						// }
						// else {
					       echo $price = floor((($carat/100) * $rtiAG) + floor(($carat/100)  * $rtiAG * ($AGpresentasePotonganAG/100)));
						// }
					}else{
						// if ($carat == 1000) { 
						// 	$price = round($rtiAG + ($rtiAG * $AGpresentasePotonganAGLow/100));
						// }
						// else if ($carat == 925) {
						// 	$price = round((0.925 * $rtiAG) + (0.925  * $rtiAG * ($AGpresentasePotonganAGLow/100)));
						// }
						// else if ($carat == 900) {
						// 	$price = round((0.90 * $rtiAG) + (0.90  * $rtiAG * ($AGpresentasePotonganAGLow/100)));
						// }else if ($carat == 500) {
						// 	$price = round((0.50 * $rtiAG) + (0.50  * $rtiAG * ($AGpresentasePotonganAGLow/100)));
						// }
						// else {
					        $price = round((($carat/100) * $rtiAG) + round(($carat/100)  * $rtiAG * ($AGpresentasePotonganAGLow/100)));
						// }
					}
					$priceTotal = round($price * $weight);
	               // echo $carat;
	               // die;
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'types' => $types,
						'materialName' => $materialName,
						'materialType' => $materialType,
						'carat' => 'Ag '.$carat.' %',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
                }else if ($idMaterial == 6){
					if($types=='high'){
						if($percentage<100){
							// $price = floor((($percentage/100) * $rtiPT) - (($percentage/100) * $rtiPT *  ($PTpresentasePotonganPt/100)));
							// $price = floor(($percentage/100) * floor(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganPt/100)));
							// 99.98% * (373.052 - (373.052*-15%))
							$price = floor(($percentage/100) * floor($rtiPT + ($rtiPT * ($PTpresentasePotonganPt/100))));
							$priceTotal = floor($price * $weight);	
						}else{
							// $price = round((($percentage/100) * $rtiPT) - (($percentage/100) * $rtiPT *  ($PTpresentasePotonganPt/100)));
							// $price = round(($percentage/100) * round(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganPt/100)));
							$price = round($rtiPT + ($rtiPT * ($PTpresentasePotonganPt/100)));
							$priceTotal = round($price * $weight);	
						}
					}else{
						if($percentage<100){
							// $price = floor((($percentage/100) * $rtiPT) - (($percentage/100) * $rtiPT *  ($PTpresentasePotonganPt/100)));
							// $price = floor(($percentage/100) * floor(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganPt/100)));
							// 99.98% * (373.052 - (373.052*-15%))
							$price = floor(($percentage/100) * floor($rtiPT + ($rtiPT * ($PTpresentasePotonganPtLow/100))));
							$priceTotal = floor($price * $weight);	
						}else{
							// $price = round((($percentage/100) * $rtiPT) - (($percentage/100) * $rtiPT *  ($PTpresentasePotonganPt/100)));
							// $price = round(($percentage/100) * round(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganPt/100)));
							$price = round($rtiPT + ($rtiPT * ($PTpresentasePotonganPtLow/100)));
							$priceTotal = round($price * $weight);	
						}
					}
                	$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'types' => $types,
						'materialName' => $materialName,
						'materialType' => $materialType,
						'carat' => 'Pt '.$percentage.' %',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
                }else if ($idMaterial == 7){
					if($types=='high'){
						if($percentage<100){
						//	persentase * (rti pt + (rti pt* potongan))
						//  $weight.' '.$percentage;
						// $price = ((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganPd/100)));
						$price = floor(($percentage/100) * floor(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganPd/100)));
						$priceTotal = floor($price * $weight);	
						}else{
						// $price = round((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganPd/100)));
						$price = round(($percentage/100) * round(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganPd/100)));
						$priceTotal = round($price * $weight);	
						}
					}else{
						if($percentage<100){
							//	persentase * (rti pt + (rti pt* potongan))
							//  $weight.' '.$percentage;
							// $price = ((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganPd/100)));
							$price = floor(($percentage/100) * floor(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganPdLow/100)));
							$priceTotal = floor($price * $weight);	
							}else{
							// $price = round((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganPd/100)));
							$price = round(($percentage/100) * round(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganPdLow/100)));
							$priceTotal = round($price * $weight);	
							}
					}
                	$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'types' => $types,
						'materialName' => $materialName,
						'materialType' => $materialType,
						'carat' => 'Pd '.$percentage.' %',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
                }else if ($idMaterial == 8){
					// $price = (($percentage/100) * $rtiPT) - (($percentage/100) * $rtiPT *  ($PTpresentasePotonganIr/100));
					// -	Harga platinum 	= RTI Pt – (harga RTI Pt x 15 %) 
					// -	Rumus ir 		= harga platinum - (harga platinum x 30%) 
					// Rumus : total price 	= persentasi*(harga platinum-(harga platinum*potongan harga))
					// = 69.3%*(349009-(349009*30%))
					// = 169304
					if($types=='high'){
						// if($percentage<100){
							$pricePlatinum = floor($rtiPT + ($rtiPT * ($PTpresentasePotonganPt/100)));
							
							
							$price = round(($rtiPT + ($rtiPT * ($PTpresentasePotonganIr/100))) * $percentage/100);
							// 451625 +/- (451625 * (80/100)) *100 / 100
							$priceTotal = ($price * $weight);	
						// }else{
						// 	$priceold = $rtiPT * 40 / 100 * $percentage / 100;
						// 	// $pricePlatinum = round($rtiPT + ($rtiPT * -($PTpresentasePotonganPt/100)));
						// 	$price = floor($rtiPT - $priceold);
						// 	echo "<pre>";
						// 	print_r ($rtiPT);
						// 	echo "</pre>";
						// 	$priceTotal = round($price * $weight);	
						// }
					}else{
						// if($percentage<100){
							// $pricePlatinum = floor($rtiPT + ($rtiPT * -($PTpresentasePotonganPt/100)));
							$price = round(($rtiPT + ($rtiPT * ($PTpresentasePotonganIrLow/100))) * $percentage/100);
							$priceTotal = ($price * $weight);	
						// }else{
						// 	$pricePlatinum = round($rtiPT + ($rtiPT * -($PTpresentasePotonganPtLow/100)));
						// 	$price = round(($percentage/100) * round($pricePlatinum - ($pricePlatinum * ($PTpresentasePotonganIrLow/100))));
						// 	$priceTotal = round($price * $weight);	
						// }
					}
					// 	die;
                	$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'types' => $types,
						'materialName' => $materialName,
						'materialType' => $materialType,
						'carat' => 'Ir '.$percentage.' %',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
                }else if ($idMaterial == 9){
					if($types=='high'){
						if($percentage<100){
						// $price = floor((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganRh/100)));
						$price = floor(($percentage/100) * floor(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganRh/100)));
						$priceTotal = floor($price * $weight);	
						}else{
						// $price = round((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganRh/100)));
						$price = round(($percentage/100) * round(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganRh/100)));
						$priceTotal = round($price * $weight);	
						}
					}else{
						if($percentage<100){
							// $price = floor((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganRh/100)));
							$price = floor(($percentage/100) * floor(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganRhLow/100)));
							$priceTotal = floor($price * $weight);	
							}else{
							// $price = round((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganRh/100)));
							$price = round(($percentage/100) * round(($rtiPT) +  (($rtiPT) * $PTpresentasePotonganRhLow/100)));
							$priceTotal = round($price * $weight);	
							}
					}
                	$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'types' => $types,
						'materialName' => $materialName,
						'materialType' => $materialType,
						'carat' => 'Rh '.$percentage.' %',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
                }else if ($idMaterial == 10){
					// if($percentage<100){
					// // $price = floor((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganRh/100)));
					// // echo $price = floor(($rtiAU/100) - floor((($rtiAU/100)-$AUpresentasePotonganCustProf)));
					// // echo $rtiAU;
					// $price = floor($rtiAU - ($rtiAU * ($AUpresentasePotonganCustProf/100)));
					// // $price = floor((($percentage/100) * ($price)));
					// $priceTotal = $price * $weight; 	
					// }else{
					// // $price = round((($percentage/100) * $rtiPT) + (($percentage/100) * $rtiPT *  ($PTpresentasePotonganRh/100)));
					// $price = round($rtiAU - ($rtiAU * ($AUpresentasePotonganCustProf/100)));
					// $price = round((($percentage/100) * ($price)));
					// $priceTotal = $price * $weight; 	
					// }
					$price = ($rtiAU + ($rtiAU * $AUpresentasePotonganCustProf/100)) * $percentage/100;
					$priceTotal = ($price * $weight);	
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'materialType' => $materialType,
						'carat' => 'Au '.$percentage.' %',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
				}else if ($idMaterial == 17){
					$price = ($rtiAU + $AUpotonganubs);
					$priceTotal = $price*$weight;
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'materialType' => '-',
						'carat' => '',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
				}else if ($idMaterial == 19){
					if($types == 'high'){
						$pricepergram = floor((($percentage/100) * $rtiRU) + floor(($percentage/100)  * $rtiRU * ($AUpotonganruhigh/100)));
					}
					else{
						$pricepergram = floor((($percentage/100) * $rtiRU) + floor(($percentage/100)  * $rtiRU * ($AUpotonganrulow/100)));
					}
					
					$price = $pricepergram*$weight;
					
					// End Rumus Baru
					$priceTotal = round($price);
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $pricepergram,
						'prices' => $pricepergram,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'types' => $types,
						'materialType' => '-',
						'carat' => 'RU '.$percentage.'%',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
				}
				else if ($idMaterial == 21){
					
					
					$pricepergram = $rtiTA * $percentage / 100; 
					$priceTotal = $pricepergram * $weight; 
					
					// End Rumus Baru
					$priceTotal = round($priceTotal);
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $pricepergram,
						'prices' => $pricepergram,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'materialType' => '-',
						'carat' => $percentage."%",
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
				}
				else if ($idMaterial == 23){
					if ($carat == '24(99.9)') {
						

						$pricepergram = $rtiAU + $AUgb_99_9;
						// $price = round(($rtiAU + ($rtiAU * - (16/100))) * $percentage/100);
						// $priceTotal = ($price * $weight);
					}
					else{
						$pricepergram = $rtiAU + $AUgb_99;
					}
					$price = $pricepergram*$weight;
					
					// End Rumus Baru
					$priceTotal = round($price);
					$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $pricepergram,
						'prices' => $pricepergram,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'materialType' => '-',
						'carat' => 'K'.$carat,
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
				}
				
			}else{
				$weight = 1;
				$price = $this->input->post('price');
				$priceTotal = $price * $weight; 	
				$data = array(
					'id' => $idLast,
					'qty' => $weight,
					'price' => $price,
					'prices' => $price,
					'name' => 'T-Shirt',
					'materialName' => $materialName,
					'materialType' => '-',
					'carat' => '-',
					'weight' => '-',
					'priceTotal' => $priceTotal,
				);
			}
			// if($idMaterial <= 10 || $idMaterial == 17){
			$this->cart->insert($data);
			// }
			// Add To Transaction
			
			$idTransaction = $this->session->userdata("idTransaction");
			$total = 0;
			$qtt = 0;
			foreach($this->cart->contents() as $a){
				$total = $total + $a["priceTotal"];
				$qtt=$qtt+1;
			}
			if(!$idTransaction){
				$idCustomer = $this->session->userdata("idCustomer");
				if(empty($idCustomer)){
					$idCustomer = 7;
				}
				$this->data['nameCustomer'] = $this->MasterModel->customerDatas($idCustomer)->row("c_name");
				$this->data['phoneCustomer'] = $this->MasterModel->customerDatas($idCustomer)->row("c_phone");
				
				$year = date('Y',strtotime($this->dateToday));
				$noOrder = $this->db->query("SELECT COUNT(*) as count FROM tb_transaction WHERE YEAR(t_date_created)='$year'")->row('count');
				if(!empty($noOrder)){
					$noOrderNew = "PB-".substr(date('Y',strtotime($this->dateToday)),2).date('m',strtotime($this->dateToday))."-".($noOrder+1);
				}else{
					$noOrderNew = "PB-".substr(date('Y',strtotime($this->dateToday)),2).date('m',strtotime($this->dateToday))."-1";
				}
				$data = array(
					't_no_order' => $noOrderNew,
					't_date_created' => $this->dateToday,
					't_status' => 'PROSES',
					't_created_at' => date('H:i:s',strtotime($this->dateToday)),
					't_created_by' => $idUser,
					't_customer' => $idCustomer,
					't_phone' => $this->data['phoneCustomer'],
					't_note' => '',
					't_type' => 'BUY',
					't_paid_by' => $this->data['nameCustomer'],
					't_receive_by' => $idUser,
					't_price_total' => $total,
					't_qtt' => $qtt,
					't_visible' => 1,
				);
				$idTransaction = $this->TransactionModel->buyCheckout($data);
				$data_session = array(
					'idTransaction' => $idTransaction,
					'jenis_transaksi' => "buy"
				);
				$this->session->set_userdata($data_session);
			}
			else{
				$data = array(
					't_price_total' => $total,
					't_qtt' => $qtt,
				);
				$this->db->update('tb_transaction', $data, ['t_id' => $idTransaction]);
			}
			$this->db->where('ti_t_id', $idTransaction);
			$this->db->delete('tb_transaction_items');
			foreach($this->cart->contents() as $a){
				$dataItems = array(
					'ti_t_id' => $idTransaction,
					'ti_material' => $a['materialName'],
					'ti_material_type' => $a['materialType'],
					'ti_carat' => $a['carat'],
					'ti_weight' => $a['weight'],
					'ti_price' => $a['prices'],
					'ti_high_low' => strval($a['types']),
					'ti_price_total' => $a['priceTotal'],
					'ti_date_created' => $this->dateToday,
				);
				$this->TransactionModel->buyCheckoutItems($dataItems);
			}
			// echo "<pre>";
			// print_r ($idTransaction);
			// echo "</pre>";
			redirect(base_url()."transaction/buy/$idMaterial/?t=$types");
		}else {
			redirect(base_url());
		}
	}
	function buyAddToCartReset()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idMaterial = $this->input->get('idMaterial');
			$idRow = $this->input->get('idRow');
			$t = $this->input->get('t');
			// die;
			$idTransaction = $this->session->userdata("idTransaction");
			$this->db->where('ti_t_id', $idTransaction);
			$this->db->delete('tb_transaction_items');
			
			if (!empty($idRow)) {
				$qty = 0;
				$array = array(
					'rowid' => $idRow,
					'qty' => $qty
				);
				print_r($array);
				$this->cart->update($array);
				foreach($this->cart->contents() as $a){
					$dataItems = array(
						'ti_t_id' => $idTransaction,
						'ti_material' => $a['materialName'],
						'ti_material_type' => $a['materialType'],
						'ti_carat' => $a['carat'],
						'ti_weight' => $a['weight'],
						'ti_price' => $a['prices'],
						'ti_high_low' => strval($a['types']),
						'ti_price_total' => $a['priceTotal'],
						'ti_date_created' => $this->dateToday,
					);
					$this->TransactionModel->buyCheckoutItems($dataItems);
				}
			}
			else {
				$this->cart->destroy();
			}
			redirect(base_url() . "transaction/buy/$idMaterial/?t=$t");
		}
		else {
			redirect(base_url());
		}
    }
    function buyCheckout(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idTransaction = $this->session->userdata("idTransaction");
			$this->db->where('ti_t_id', $idTransaction);
			$this->db->delete('tb_transaction_items');
			$biayaAdmin = $this->input->get('operator').''.$this->input->get('biayaAdmin');
			$total = 0;
			$qtt = 0;
			foreach($this->cart->contents() as $a){
				$total = $total + $a["priceTotal"];
				$qtt=$qtt+1;
			}
			$data = array(
				't_status' => 'CHECKOUT',
				't_price_total' => $total,
				't_price_admin' => $biayaAdmin,
				't_qtt' => $qtt,
			);
			$this->db->update('tb_transaction', $data, ['t_id' => $idTransaction]);
			foreach($this->cart->contents() as $a){
				$dataItems = array(
					'ti_t_id' => $idTransaction,
					'ti_material' => $a['materialName'],
					'ti_material_type' => $a['materialType'],
					'ti_carat' => $a['carat'],
					'ti_weight' => $a['weight'],
					'ti_price' => $a['prices'],
					'ti_high_low' => strval($a['types']),
					'ti_price_total' => $a['priceTotal'],
					'ti_date_created' => $this->dateToday,
				);
				$this->TransactionModel->buyCheckoutItems($dataItems);
			}
			$this->session->unset_userdata('idCustomer');
			$this->cart->destroy();
			$data_session = array(
				'status' => 'success',
				'message' => "Checkout no order  <b>".$noOrderNew."</b> is success!!",
			);
			$this->session->set_userdata($data_session);
			redirect(base_url()."report/buy-print/$idTransaction/");
		}
		else {
			redirect(base_url());
		}	
	}
    function sell()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		$this->data["title"] = "TRANSACTION SELL";
		if ($authUser == true) {
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$type = $this->UserModel->userDataById($idUser)->row("u_rule");
			$id = $this->input->get('id');
			$this->data['data'] = $this->MaterialModel->materialData('Sell')->result();
			$this->data['content'] = $this->load->view('Sell', $this->data, true);
			$this->load->view("UserTemplate", $this->data);
		}
		else {
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
	function sellAddToCart()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idMaterial = $this->input->post('idMaterial');
			$materialType = $this->input->post('materialType');
			$carat = $this->input->post('carat');
			$weight = $this->input->post('weight');
			$this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
			$materialName = $this->MaterialModel->materialDataBy('m_id', $idMaterial,'Sell')->row("m_name");
			foreach($this->cart->contents() as $a) {
				$idLast = ($a['id']);
			}
			if (!empty($idLast)) {
				$idLast = $idLast + 1;
			}
			else {
				$idLast = 1;
			}
			$rtiAU = $this->MaterialModel->formulaData()->row("f_rti_au_sell");
			$rtiUbs = $this->MaterialModel->formulaData()->row("f_material_ubs_sell");
			$AUtambahAUG = $this->MasterModel->formulasData('material-au')->row('g');
			$AUPotongan = $this->MasterModel->formulasData('material-au')->row('a');
			$potongan_ubs = $this->MasterModel->formulasData('material-ubs')->row('a');
			$potongan_lm = $this->MasterModel->formulasData('lm')->row('potongan_lm');
			$rtiAG = $this->MaterialModel->formulaData()->row("f_rti_ag_sell");
			$LMpresentaseLMBaru = $this->MasterModel->formulasData('lm')->row('b');
			$LMpresentaseLMLama = $this->MasterModel->formulasData('lm')->row('a');
            if($idMaterial==16){
				$price = round($rtiAG);
				$priceTotal = round($price * $weight);
				$data = array(
					'id' => $idLast,
					'qty' => $weight,
					'price' => $price,
					'prices' => $price,
					'name' => 'T-Shirt',
					'materialName' => $materialName,
					'materialType' => '-',
					'carat' => '100%',
					'weight' => $weight,
					'priceTotal' => $priceTotal,
				);
            }else if($idMaterial == 18){
				$price = ($rtiUbs + $potongan_ubs);
				$priceTotal = $price*$weight;
				$data = array(
					'id' => $idLast,
					'qty' => $weight,
					'price' => $price,
					'prices' => $price,
					'name' => 'T-Shirt',
					'materialName' => $materialName,
					'materialType' => '-',
					'carat' => '',
					'weight' => $weight,
					'priceTotal' => $priceTotal,
				);
	        }else if($idMaterial==15){
				$pricePergram = $rtiAU + $AUPotongan;
				$priceTotal = $pricePergram * $weight;
				$data = array(
					'id' => $idLast,
					'qty' => $weight,
					'price' => $pricePergram,
					'prices' => $pricePergram,
					'name' => 'T-Shirt',
					'materialName' => $materialName,
					'materialType' => '-',
					'carat' => '24',
					'weight' => $weight,
					'priceTotal' => $priceTotal,
				);
                $price = $rtiAU;
	        }else if($idMaterial==14){
					if($weight==0.5){
						$price = $this->MaterialModel->formulaData()->row("f_nol5");
					}else if($weight==1){
						$price = $this->MaterialModel->formulaData()->row("f_1");
					}else if($weight==2){
						$price = $this->MaterialModel->formulaData()->row("f_2");
					}else if($weight==2.5){
						$price = $this->MaterialModel->formulaData()->row("f_2_coma_5");
					}else if($weight==3){
						$price = $this->MaterialModel->formulaData()->row("f_3");
					}else if($weight==5){
						$price = $this->MaterialModel->formulaData()->row("f_5");
					}else if($weight==10){
						$price = $this->MaterialModel->formulaData()->row("f_10");
					}else if($weight==25){
						$price = $this->MaterialModel->formulaData()->row("f_25");
					}else if($weight==50){
						$price = $this->MaterialModel->formulaData()->row("f_50");
					}else if($weight==100){
						$price = $this->MaterialModel->formulaData()->row("f_100");
					}else if($weight==250){
						$price = $this->MaterialModel->formulaData()->row("f_250");
					}else if($weight==500){
						$price = $this->MaterialModel->formulaData()->row("f_500");
					}else if($weight==1000){
						$price = $this->MaterialModel->formulaData()->row("f_1000");
					}else{
						$price = 1;	
					}
					if($weight == 0.5){
						// $weightTemp = 1;	
						// $price = $price - $LMpresentaseLMLama;
						// $priceTotal = ($price * $weightTemp);
						$priceTotal = $price + ($LMpresentaseLMLama*0.5);
						$price = $priceTotal;
						}else{
						$price = $price + $LMpresentaseLMLama;
						$priceTotal = round(($price * $weight));
					}
						$data = array(
						'id' => $idLast,
						'qty' => $weight,
						'price' => $price,
						'prices' => $price,
						'name' => 'T-Shirt',
						'materialName' => $materialName,
						'materialType' => '-',
						'carat' => '24',
						'weight' => $weight,
						'priceTotal' => $priceTotal,
					);
	        }else if($idMaterial==13){
				/*
				Rumus Lama
				if($weight==0.5){
					$price = $this->MaterialModel->formulaData()->row("f_nol5");
				}else if($weight==1){
					$price = $this->MaterialModel->formulaData()->row("f_1");
				}else if($weight==2){
					$price = $this->MaterialModel->formulaData()->row("f_2");
				}else if($weight==2.5){
					$price = $this->MaterialModel->formulaData()->row("f_2_coma_5");
				}else if($weight==3){
					$price = $this->MaterialModel->formulaData()->row("f_3");
				}else if($weight==5){
					$price = $this->MaterialModel->formulaData()->row("f_5");
				}else if($weight==10){
					$price = $this->MaterialModel->formulaData()->row("f_10");
				}else if($weight==25){
					$price = $this->MaterialModel->formulaData()->row("f_25");
				}else if($weight==50){
					$price = $this->MaterialModel->formulaData()->row("f_50");
				}else if($weight==100){
					$price = $this->MaterialModel->formulaData()->row("f_100");
				}else if($weight==250){
					$price = $this->MaterialModel->formulaData()->row("f_250");
				}else if($weight==500){
					$price = $this->MaterialModel->formulaData()->row("f_500");
				}else if($weight==1000){
					$price = $this->MaterialModel->formulaData()->row("f_1000");
				}else{
					$price = 1;	
				}
				if($weight == 0.5){
					// $weightTemp = 1;	
					// $price = $price - $LMpresentaseLMBaru;
					// $priceTotal = ($price * $weightTemp);
					$priceTotal = round($price - ($LMpresentaseLMBaru*0.5));
					$price = $priceTotal;
				}else{
					$price = $price - $LMpresentaseLMBaru;
					$priceTotal = round(($price * $weight));
				} 
				End Rumus Lama
				*/
				/* Rumus Baru */
				// $this->db->where('id', $this->input->post('id_potongan'));
				// $cek_harga = $this->db->get('tb_potongan')->row();
				// print_r($this->input->post('id_potongan'));
				// $pricepergram = $AUtambahAUG + $cek_harga->harga_buy;
				// $price = $pricepergram*$weight;
				if($weight==0.5){
					$get_price = $this->MaterialModel->formulaData()->row("f_nol5");
				}else if($weight==1){
					$get_price = $this->MaterialModel->formulaData()->row("f_1"); 
				}else if($weight==2){
					$get_price = $this->MaterialModel->formulaData()->row("f_2");
				}else if($weight==2.5){
					$get_price = $this->MaterialModel->formulaData()->row("f_2_coma_5");
				}else if($weight==3){
					$get_price = $this->MaterialModel->formulaData()->row("f_3");
				}else if($weight==5){
					$get_price = $this->MaterialModel->formulaData()->row("f_5");
				}else if($weight==10){
					$get_price = $this->MaterialModel->formulaData()->row("f_10");
				}else if($weight==25){
					$get_price = $this->MaterialModel->formulaData()->row("f_25");
				}else if($weight==50){
					$get_price = $this->MaterialModel->formulaData()->row("f_50");
				}else if($weight==100){
					$get_price = $this->MaterialModel->formulaData()->row("f_100");
				}else if($weight==250){
					$get_price = $this->MaterialModel->formulaData()->row("f_250");
				}else if($weight==500){
					$get_price = $this->MaterialModel->formulaData()->row("f_500");
				}else if($weight==1000){
					$get_price = $this->MaterialModel->formulaData()->row("f_1000");
				}else{
					$price = $rtiAU;	
				}
				$tahun_potongan = $this->input->post('tahun_potongan');
				$harga_potongan = json_decode($potongan_lm, true)[$tahun_potongan];
				$pricepergram = $get_price + $harga_potongan;


				
				// print_r($harga_potongan);
				// die();
				$price = $pricepergram*$weight;
				$priceTotal = round($price);

				/*End Rumus Baru */
				$data = array(
					'id' => $idLast,
					'qty' => $weight,
					'price' => $pricepergram,
					'prices' => $pricepergram,
					'name' => 'T-Shirt',
					'materialName' => $materialName,
					'materialType' => $tahun_potongan,
					'carat' => '24',
					'weight' => $weight,
					'priceTotal' => $priceTotal,
				);
            }else{
				$price = 1;
				$priceTotal = round($price * $weight);
				$qty = 1;
				$data = array(
					'id' => $idLast,
					'qty' => $qty,
					'price' => '',
					'prices' => $price,
					'name' => 'T-Shirt',
					'materialName' => $materialName,
					'materialType' => $materialType,
					'carat' => '',
					'weight' => $weight,
					'priceTotal' => $priceTotal,
				);
            }
            // echo "<pre>";
            // print_r ($data);
            // echo "</pre>";
			$this->cart->insert($data);
			// Add To Transaction Sell
			$idTransaction = $this->session->userdata("idTransaction");
			$total = 0;
			$qtt = 0;
			foreach($this->cart->contents() as $a){
				$total = $total + $a["priceTotal"];
				$qtt=$qtt+1;
			}
			if(!$idTransaction){
				$idCustomer = $this->session->userdata("idCustomer");
				if(empty($idCustomer)){
					$idCustomer = 7;
				}
				$this->data['nameCustomer'] = $this->MasterModel->customerDatas($idCustomer)->row("c_name");
				$this->data['phoneCustomer'] = $this->MasterModel->customerDatas($idCustomer)->row("c_phone");

				
				$year = date('Y',strtotime($this->dateToday));
				// $noOrder = $this->TransactionModel->lastDataSell($year)->row('t_id');
				$noOrder = $this->db->query("SELECT COUNT(*) as count FROM tb_transaction_sell WHERE YEAR(t_date_created)='$year'")->row('count');
				if(!empty($noOrder)){
					echo $noOrderNew = "PJ-".substr(date('Y',strtotime($this->dateToday)),2).date('m',strtotime($this->dateToday))."-".($noOrder+1);
				}else{
					$noOrderNew = "PJ-".substr(date('Y',strtotime($this->dateToday)),2).date('m',strtotime($this->dateToday))."-1";
				}
				$data = array(
					't_no_order' => $noOrderNew,
					't_date_created' => $this->dateToday,
					't_status' => 'PROSES',
					't_created_at' => date('H:i:s',strtotime($this->dateToday)),
					't_created_by' => $idUser,
					't_customer' => $idCustomer,
					't_phone' => $this->data['phoneCustomer'],
					't_note' => '',
					't_type' => 'SELL',
					't_paid_by' => $this->data['nameCustomer'],
					't_receive_by' => $idUser,
					't_price_total' => $total,
					't_visible' => 1,
					't_qtt' => $qtt,
				);
				$idTransaction = $this->TransactionModel->sellCheckout($data);
				$data_session = array(
					'idTransaction' => $idTransaction,
					'jenis_transaksi' => "sell"
				);
				$this->session->set_userdata($data_session);
			}
			else{
				$data = array(
					't_price_total' => $total,
					't_qtt' => $qtt,
				);
				$this->db->update('tb_transaction_sell', $data, ['t_id' => $idTransaction]);
			}
			$this->db->where('ti_t_id', $idTransaction);
			$this->db->delete('tb_transaction_items_sell');
			foreach($this->cart->contents() as $a){
				$dataItems = array(
					'ti_t_id' => $idTransaction,
					'ti_material' => $a['materialName'],
					'ti_material_type' => $a['materialType'],
					'ti_carat' => $a['carat'],
					'ti_weight' => $a['weight'],
					'ti_price' => $a['prices'],
					'ti_price_total' => $a['priceTotal'],
					'ti_date_created' => $this->dateToday,
				);
				$this->TransactionModel->sellCheckoutItems($dataItems);
			}
			//$this->session->unset_userdata('idCustomer');
			redirect(base_url()."transaction/sell/$idMaterial/");
		}
		else {
			redirect(base_url());
		}
	}
	function sellAddToCartReset()
	{
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idMaterial = $this->input->get('idMaterial');
			$idRow = $this->input->get('idRow');

			$idTransaction = $this->session->userdata("idTransaction");
			$this->db->where('ti_t_id', $idTransaction);
			$this->db->delete('tb_transaction_items_sell');
			
			
			if (!empty($idRow)) {
				$qty = 0;
				$array = array(
					'rowid' => $idRow,
					'qty' => $qty
				);
				print_r($array);
				$this->cart->update($array);
				
				foreach($this->cart->contents() as $a){
					$dataItems = array(
						'ti_t_id' => $idTransaction,
						'ti_material' => $a['materialName'],
						'ti_material_type' => $a['materialType'],
						'ti_carat' => $a['carat'],
						'ti_weight' => $a['weight'],
						'ti_price' => $a['prices'],
						'ti_price_total' => $a['priceTotal'],
						'ti_date_created' => $this->dateToday,
					);
					$this->TransactionModel->sellCheckoutItems($dataItems);
				}
			}
			else {
				$this->cart->destroy();
			}
			redirect(base_url() . "transaction/sell/$idMaterial/");
		}
		else {
			redirect(base_url());
		}
	}
	function sellCheckout(){
		$authUser = $this->session->userdata("authUser");
		$idUser = $this->session->userdata("idUser");
		if ($authUser == true) {
			$idCustomer = $this->session->userdata("idCustomer");
			$idTransaction = $this->session->userdata("idTransaction");
			$this->db->where('ti_t_id', $idTransaction);
			$this->db->delete('tb_transaction_items_sell');
			$biayaAdmin = $this->input->get('operator').''.$this->input->get('biayaAdmin');
			$total = 0;
			$qtt = 0;
			foreach($this->cart->contents() as $a){
				$total = $total + $a["priceTotal"];
				$qtt=$qtt+1;
			}
			
			$data = array(
				't_status' => 'CHECKOUT',
				't_price_total' => $total,
				't_price_admin' => $biayaAdmin,
				't_qtt' => $qtt,
			);
			$this->db->update('tb_transaction_sell', $data, ['t_id' => $idTransaction]);
			foreach($this->cart->contents() as $a){
				$dataItems = array(
					'ti_t_id' => $idTransaction,
					'ti_material' => $a['materialName'],
					'ti_material_type' => $a['materialType'],
					'ti_carat' => $a['carat'],
					'ti_weight' => $a['weight'],
					'ti_price' => $a['prices'],
					'ti_price_total' => $a['priceTotal'],
					'ti_date_created' => $this->dateToday,
				);
				$this->TransactionModel->sellCheckoutItems($dataItems);
			}
			$this->session->unset_userdata('idCustomer');
			$this->cart->destroy();
			$data_session = array(
				'status' => 'success',
				'message' => "Checkout no order  <b>".$noOrderNew."</b> is success!!",
			);
			$this->session->set_userdata($data_session);
			redirect(base_url()."report/sell-print/$idTransaction/");
		}
		else {
			redirect(base_url());
		}	
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
}
