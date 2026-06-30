<?php defined('BASEPATH') or exit('No direct script access allowed');

class TukarProduks extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'tukarproduk';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'ftanggal', 'sTitle' => 'TANGGAL'),
			(object) array('mData' => 'fwarga', 'sTitle' => 'WARGA'),
			(object) array('mData' => 'fpetugas', 'sTitle' => 'PETUGAS'),
			(object) array('mData' => 'fproduktukar', 'sTitle' => 'PRODUK TUKAR'),
			(object) array('mData' => 'fharga', 'sTitle' => 'HARGA'),
			(object) array('mData' => 'fqty', 'sTitle' => 'QTY'),
			(object) array('mData' => 'ftotal', 'sTitle' => 'TOTAL'),
			(object) array('mData' => 'fstatus', 'sTitle' => 'STATUS'),
		);

		$this->form = array(
			array(
				'name' => 'warga',
				'label' => 'Warga',
				'options' => array(),
				'width' => 2,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'Wargas'),
					array('data-field' => 'nama'),
					array('required' => true),
				)
			),
			array(
				'name' => 'produktukar',
				'label' => 'Produk Tukar',
				'options' => array(),
				'width' => 2,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'ProdukTukars'),
					array('data-field' => 'nama'),
					array('required' => true),
				)
			),
			array(
				'name' => 'qty',
				'label' => 'Qty',
				'width' => 2,
				'attributes' => array(
					array('data-number' => 'true'),
					['required' => true]
				)
			),
			[
				'name' => 'status',
				'label' => 'Status',
				'options' => [
					['text' => 'DIBAYAR', 'value' => 'DIBAYAR'],
					['text' => 'DIAMBIL', 'value' => 'DIAMBIL'],
				]
			]
		);
	}

	function dt()
	{
		if ('Warga' === $this->session->userdata('role_name')) {
			$this->db->where('warga.uuid', $this->session->userdata('uuid'));
		}
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("DATE_FORMAT(tukarproduk.createdAt, '%d %b %Y') as ftanggal", false)
			->select("warga.nama as fwarga", false)
			->select("user.nama as fpetugas", false)
			->select("produktukar.nama as fproduktukar", false)
			->select("CONCAT('Rp ', FORMAT(tukarproduk.harga, 0, 'id_ID')) as fharga", false)
			->select("FORMAT(tukarproduk.qty, 0) as fqty", false)
			->select("CONCAT('Rp ', FORMAT(tukarproduk.total, 0, 'id_ID')) as ftotal", false)
			->select("tukarproduk.status as fstatus", false)
			->select("'' as aksi", false)
			->join('user warga', 'warga.uuid = tukarproduk.warga', 'left')
			->join('user', 'user.uuid = tukarproduk.petugas', 'left')
			->join('produktukar', 'produktukar.uuid = tukarproduk.produktukar', 'left')
		;
		return parent::dt();
	}

	public function validate_stok($produktukar_uuid, $qty)
	{
		if (!is_numeric($qty) || intval($qty) <= 0) {
			throw new RuntimeException('Qty harus lebih besar dari 0');
		}

		$produk = $this->ProdukTukars->findOne($produktukar_uuid);
		if (!$produk) {
			throw new RuntimeException('Produk tukar tidak ditemukan');
		}

		if (intval($qty) > intval($produk['stok'])) {
			throw new RuntimeException('Stok tidak cukup. Tersedia: ' . intval($produk['stok']) . ', diminta: ' . intval($qty));
		}

		return true;
	}

	public function validate_saldo($warga_uuid, $total)
	{
		if (empty($warga_uuid)) {
			throw new RuntimeException('Warga tidak ditemukan');
		}

		if (!is_numeric($total) || $total < 0) {
			throw new RuntimeException('Total transaksi tidak valid');
		}

		$this->load->model('Ledgers');
		$flows = $this->Ledgers->find(['warga' => $warga_uuid, 'status' => 1]);
		$saldo = 0;
		foreach ($flows as $flow) {
			$saldo += $flow->nilai;
		}

		if ($saldo < $total) {
			throw new RuntimeException('Saldo tidak cukup. Saldo: Rp ' . number_format($saldo, 0, ',', '.') . ', total: Rp ' . number_format($total, 0, ',', '.'));
		}

		return true;
	}

	function create($record)
	{
		$record['petugas'] = $this->session->userdata('uuid');
		if ('Warga' === $this->session->userdata('role_name')) {
			$record['warga'] = $this->session->userdata('uuid');
		}

		$this->load->model(['ProdukTukars', 'Ledgers']);
		$produk = $this->ProdukTukars->findOne($record['produktukar']);
		if (!$produk) {
			throw new RuntimeException('Produk tukar tidak ditemukan');
		}

		$record['qty'] = intval($record['qty']);
		$record['harga'] = $produk['harga'];
		$record['total'] = $record['harga'] * $record['qty'];

		$this->validate_stok($record['produktukar'], $record['qty']);
		$this->validate_saldo($record['warga'], $record['total']);

		$uuid = parent::create($record);
		$created = $this->findOne($uuid);

		$this->ProdukTukars->sold($record['produktukar'], $record['qty']);
		$this->Ledgers->save([
			'kode' => $created['kode'],
			'transaksi' => $created['uuid'],
			'warga' => $created['warga'],
			'petugas' => $created['petugas'],
			'tipe' => 'TUKAR_PRODUK',
			'keterangan' => $produk['nama'],
			'nilai' => $record['total'] * -1
		]);
		return $uuid;
	}
}
