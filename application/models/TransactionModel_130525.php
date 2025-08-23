<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class TransactionModel extends CI_Model
{

	function buyCheckout($data)
	{
		$this->db->insert('tb_transaction', $data);
		return $this->db->insert_id();
	}
	function buyCheckoutItems($data)
	{
		$this->db->insert('tb_transaction_items', $data);
	}
	function sellCheckout($data)
	{
		$this->db->insert('tb_transaction_sell', $data);
		return $this->db->insert_id();
	}
	function sellCheckoutItems($data)
	{
		$this->db->insert('tb_transaction_items_sell', $data);
	}
	function lastData($year)
	{
		$query = $this->db->query("SELECT a.t_id
		FROM tb_transaction a
		WHERE YEAR(a.t_date_created) = '$year'
		ORDER BY a.t_id DESC
		LIMIT 1");
		return $query;
	}
	function lastDataSell($year)
	{
		$query = $this->db->query("SELECT a.t_id
		FROM tb_transaction_sell a
		WHERE YEAR(a.t_date_created) = '$year'
		ORDER BY a.t_id DESC
		LIMIT 1");
		return $query;
	}
	function buyTransaction($dateStart, $dateEnd)
	{
		if (!empty($dateStart) && !empty($dateEnd)) {
			$query = $this->db->query("SELECT a.*, b.u_name as nameCreator, c.u_name as nameReceive, d.c_name as nameCustomer
			FROM tb_transaction a
			LEFT OUTER JOIN tb_user b
			ON a.t_created_by = b.u_id
			LEFT OUTER JOIN tb_user c
			ON a.t_receive_by = c.u_id
			LEFT OUTER JOIN tb_customer d
			ON a.t_customer = d.c_id
			WHERE a.t_visible=1 AND DATE(a.t_date_created) >= '$dateStart' AND DATE(a.t_date_created) <= '$dateEnd' 
			ORDER BY a.t_id DESC");
		} else {
			$query = $this->db->query("SELECT a.*, b.u_name as nameCreator, c.u_name as nameReceive, d.c_name as nameCustomer
			FROM tb_transaction a
			LEFT OUTER JOIN tb_user b
			ON a.t_created_by = b.u_id
			LEFT OUTER JOIN tb_user c
			ON a.t_receive_by = c.u_id
			LEFT OUTER JOIN tb_customer d
			ON a.t_customer = d.c_id
			WHERE a.t_visible=1 
			ORDER BY a.t_id DESC");
		}
		return $query;
	}
	function sellTransaction($dateStart, $dateEnd)
	{
		if (!empty($dateStart) && !empty($dateEnd)) {
			$query = $this->db->query("SELECT a.*, b.u_name as nameCreator, c.u_name as nameReceive, d.c_name as nameCustomer
			FROM tb_transaction_sell a
			LEFT OUTER JOIN tb_user b
			ON a.t_created_by = b.u_id
			LEFT OUTER JOIN tb_user c
			ON a.t_receive_by = c.u_id
			LEFT OUTER JOIN tb_customer d
			ON a.t_customer = d.c_id
			WHERE a.t_visible=1 AND DATE(a.t_date_created) >= '$dateStart' AND DATE(a.t_date_created) <= '$dateEnd' 
			ORDER BY a.t_id DESC");
		} else {
			$query = $this->db->query("SELECT a.*, b.u_name as nameCreator, c.u_name as nameReceive, d.c_name as nameCustomer
			FROM tb_transaction_sell a
			LEFT OUTER JOIN tb_user b
			ON a.t_created_by = b.u_id
			LEFT OUTER JOIN tb_user c
			ON a.t_receive_by = c.u_id
			LEFT OUTER JOIN tb_customer d
			ON a.t_customer = d.c_id
			WHERE a.t_visible=1  
			ORDER BY a.t_id DESC");
		}
		return $query;
	}
	function sellDeleteTransaction($idTransaction)
	{
		$query = $this->db->query("UPDATE
		tb_transaction_sell a
		SET a.t_visible=0
		WHERE a.t_id='$idTransaction'");
		return $query;
	}
	function buyTransactionGraph($year, $material)
	{
		$query = $this->db->query("SELECT *
			FROM
			(SELECT *
			FROM tb_month) x
			LEFT OUTER JOIN
			(SELECT a.m_id, SUM(b.ti_price_total) as priceTotal
			FROM tb_month a
			LEFT OUTER JOIN tb_transaction_items b
			ON a.m_id = MONTH(b.ti_date_created)
			WHERE b.ti_material LIKE '%$material%' AND YEAR(b.ti_date_created) LIKE '%$year%'
			GROUP BY a.m_name) y
			ON x.m_id = y.m_id");
		return $query;
	}
	function buyTransactionCustomerGraph($year)
	{
		$bulan = $this->db->query("SELECT a.m_id, a.m_name as month FROM tb_month a")->result();
		$data = array();
		foreach ($bulan as $key => $value) {
			$query = $this->db->query("SELECT COUNT(tb_transaction.t_customer) as countTransaction,m_id FROM `tb_transaction` inner join `tb_customer` right outer join `tb_month` ON tb_transaction.t_customer = tb_customer.c_id AND YEAR(`t_date_created`) = '$year' AND MONTH(tb_transaction.t_date_created) =" . $value->m_id)->row();
			array_push($data, [
				'm_id' => $query->m_id,
				'countTransaction' => $query->countTransaction,
				'month' => $value->m_id,
				'year' => $year
			]);
		}
		return $data;
	}
	function sellTransactionGraph($year, $material)
	{
		$query = $this->db->query("SELECT *
			FROM
			(SELECT *
			FROM tb_month) x
			LEFT OUTER JOIN
			(SELECT a.m_id, SUM(b.ti_price_total) as priceTotal
			FROM tb_month a
			LEFT OUTER JOIN tb_transaction_items_sell b
			ON a.m_id = MONTH(b.ti_date_created)
			WHERE b.ti_material LIKE '%$material%' AND YEAR(b.ti_date_created) LIKE '%$year%'
			GROUP BY a.m_name) y
			ON x.m_id = y.m_id");
		return $query;
	}
	function sellTransactionCustomerGraph($year)
	{
		$bulan = $this->db->query("SELECT a.m_id, a.m_name as month FROM tb_month a")->result();
		$data = array();
		foreach ($bulan as $key => $value) {
			$query = $this->db->query("SELECT COUNT(tb_transaction_sell.t_customer) as countTransaction,m_id FROM `tb_transaction_sell` inner join `tb_customer` right outer join `tb_month` ON tb_transaction_sell.t_customer = tb_customer.c_id AND YEAR(`t_date_created`) = '$year' AND MONTH(tb_transaction_sell.t_date_created) =" . $value->m_id)->row();
			array_push($data, [
				'm_id' => $query->m_id,
				'countTransaction' => $query->countTransaction,
				'month' => $value->m_id,
				'year' => $year
			]);
		}
		return $data;
	}
	function buyDeleteTransaction($idTransaction)
	{
		$query = $this->db->query("UPDATE
		tb_transaction a
		SET a.t_visible=0
		WHERE a.t_id='$idTransaction'");
		return $query;
	}
	function buyTransactionData($idTransaction)
	{
		/*
		$query = $this->db->query("SELECT a.*, b.u_name as nameCreator, c.u_name as nameReceive, d.c_name as nameCustomer, d.*
		FROM tb_transaction a
		LEFT OUTER JOIN tb_user b
		ON a.t_created_by = b.u_id
		LEFT OUTER JOIN tb_user c
		ON a.t_receive_by = c.u_id
		LEFT OUTER JOIN tb_customer d
		ON a.t_customer = d.c_id
		WHERE a.t_id='$idTransaction' AND a.t_visible=1");
		*/

		$query = $this->db->query("SELECT a.*, b.u_name as nameCreator, c.u_name as nameReceive, d.c_name as nameCustomer, d.*
		FROM tb_transaction a
		LEFT OUTER JOIN tb_user b
		ON a.t_created_by = b.u_id
		LEFT OUTER JOIN tb_user c
		ON a.t_receive_by = c.u_id
		LEFT OUTER JOIN tb_customer d
		ON a.t_customer = d.c_id
		WHERE a.t_id='$idTransaction'");

		return $query;
	}
	function buyTransactionItemsData($idTransaction)
	{
		$query = $this->db->query("SELECT a.*
		FROM tb_transaction_items a
		LEFT OUTER JOIN tb_transaction b
		ON a.ti_t_id = b.t_id
		WHERE a.ti_t_id='$idTransaction'  AND b.t_visible=1");
		return $query;
	}
	function sellTransactionData($idTransaction)
	{
		/*
		$query = $this->db->query("SELECT a.*, b.u_name as nameCreator, c.u_name as nameReceive, d.c_name as nameCustomer, d.*
		FROM tb_transaction_sell a
		LEFT OUTER JOIN tb_user b
		ON a.t_created_by = b.u_id
		LEFT OUTER JOIN tb_user c
		ON a.t_receive_by = c.u_id
		LEFT OUTER JOIN tb_customer d
		ON a.t_customer = d.c_id
		
		WHERE a.t_id='$idTransaction' AND a.t_visible=1");
		*/

		$query = $this->db->query("SELECT a.*, b.u_name as nameCreator, c.u_name as nameReceive, d.c_name as nameCustomer, d.*
		FROM tb_transaction_sell a
		LEFT OUTER JOIN tb_user b
		ON a.t_created_by = b.u_id
		LEFT OUTER JOIN tb_user c
		ON a.t_receive_by = c.u_id
		LEFT OUTER JOIN tb_customer d
		ON a.t_customer = d.c_id
		
		WHERE a.t_id='$idTransaction'");
		return $query;
	}
	function sellTransactionItemsData($idTransaction)
	{
		$query = $this->db->query("SELECT a.*
		FROM tb_transaction_items_sell a
		LEFT OUTER JOIN tb_transaction_sell b
		ON a.ti_t_id = b.t_id
		WHERE a.ti_t_id='$idTransaction'  AND b.t_visible=1");
		return $query;
	}
}
