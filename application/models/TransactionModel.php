<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

	class TransactionModel extends CI_Model
{
    /**
     * Mendapatkan data transaksi berdasarkan pagination dan pencarian
     * 
     * @param int $start Offset data (untuk paginasi)
     * @param int $length Jumlah data per halaman
     * @param string $search Kata kunci pencarian
     * @return array Daftar transaksi
     */
    public function getTransactions($start, $length, $search = '')
    {
        $this->db->from('all_transaction');
        $this->db->where('t_status !=', 'SELESAI'); // 🚀 filter status selesai

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('t_no_order', $search);
            $this->db->or_like('t_paid_by', $search);
            $this->db->or_like('t_status', $search);
            $this->db->group_end();
        }

		$this->db->order_by('t_date_created', 'DESC');
        $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan total semua data transaksi (tanpa filter pencarian)
     * 
     * @return int Total semua data
     */
    public function getTotalRecords()
    {
        $this->db->from('all_transaction');
        $this->db->where('t_status !=', 'SELESAI'); // 🚀 konsisten filter
        return $this->db->count_all_results();
    }

    /**
     * Mendapatkan total data transaksi berdasarkan pencarian
     * 
     * @param string $search Kata kunci pencarian
     * @return int Total data yang sesuai pencarian
     */
    public function getFilteredRecords($search = '')
    {
        $this->db->from('all_transaction');
        $this->db->where('t_status !=', 'SELESAI'); // 🚀 konsisten filter

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('t_no_order', $search);
            $this->db->or_like('t_paid_by', $search);
            $this->db->or_like('t_status', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }


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
	public function sellDeleteTransaction($idTransaction, $idUser)
	{
		$this->db->where('t_id', $idTransaction);
		return $this->db->update('tb_transaction_sell', [
			't_visible'   => 0,
			'is_delete'   => 1,            // opsional: supaya jelas statusnya
			'deleted_at'  => date('Y-m-d H:i:s'),
			'deleted_by'  => $idUser
		]);
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
	public function buyDeleteTransaction($idTransaction, $idUser)
	{
		return $this->db->update('tb_transaction', [
			't_visible'  => 0,
			'is_delete'  => 1, // supaya jelas status delete
			'deleted_at' => date('Y-m-d H:i:s'),
			'deleted_by' => $idUser
		], ['t_id' => $idTransaction]);
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

	public function updateAllToSelesai() {
        // Mulai transaksi
        $this->db->trans_begin();

        // Ambil semua data no_order dari tb_transaction
        $transactions = $this->db->select('t_no_order')->get('tb_transaction')->result_array();

        foreach ($transactions as $transaction) {
            $no_order = $transaction['t_no_order'];

            // Update tb_transaction
            $this->db->where('t_no_order', $no_order)
                     ->update('tb_transaction', ['t_status' => 'SELESAI']);

            // Update tb_transaction_sell
            $this->db->where('t_no_order', $no_order)
                     ->update('tb_transaction_sell', ['t_status' => 'SELESAI']);
        }

        // Cek apakah ada error selama transaksi
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            throw new Exception('Failed to update data.');
        }

        // Commit transaksi jika tidak ada error
        $this->db->trans_commit();
        return true;
    }

	// BUY
	public function buyVoidTransaction($idTransaction, $idUser)
	{
		return $this->db->query("
			UPDATE tb_transaction 
			SET t_status = 'VOID',
				is_delete = '1',
				deleted_at = NOW(),
				deleted_by = '$idUser'
			WHERE t_id = '$idTransaction'
		");
	}

	public function buyRevisionTransaction($idTransaction, $idUser, $reason)
	{
		return $this->db->query("
			UPDATE tb_transaction 
			SET t_status = 'REVISION',
				t_alasan = ".$this->db->escape($reason).",
				deleted_at = NOW(),
				deleted_by = '$idUser'
			WHERE t_id = '$idTransaction'
		");
	}

	// SELL
	public function sellVoidTransaction($idTransaction, $idUser)
	{
		return $this->db->query("
			UPDATE tb_transaction_sell 
			SET t_status = 'VOID',
				is_delete = '1',
				deleted_at = NOW(),
				deleted_by = '$idUser'
			WHERE t_id = '$idTransaction'
		");
	}

	public function sellRevisionTransaction($idTransaction, $idUser, $reason)
	{
		return $this->db->query("
			UPDATE tb_transaction_sell 
			SET t_status = 'REVISION',
				t_alasan = ".$this->db->escape($reason).",
				deleted_at = NOW(),
				deleted_by = '$idUser'
			WHERE t_id = '$idTransaction'
		");
	}

	public function updateTransactionStatus($type, $idTransaction, $status, $reason, $idUser)
	{
		$table = $type == 'buy' ? 'tb_transaction' : 'tb_transaction_sell';

		$this->db->where('t_id', $idTransaction);
		$this->db->update($table, [
			't_status' => $status,
			't_revision_reason' => $reason,
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => $idUser
		]);
	}
	
}
