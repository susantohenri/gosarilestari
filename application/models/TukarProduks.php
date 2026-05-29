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
			(object) array('mData' => 'aksi', 'sTitle' => ''),
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
					array('data-field' => 'nama')
				)
			),
			// array(
			// 	'name' => 'petugas',
			// 	'label' => 'Petugas',
			// 	'options' => array(),
			// 	'width' => 2,
			// 	'attributes' => array(
			// 		array('data-autocomplete' => 'true'),
			// 		array('data-model' => 'Users'),
			// 		array('data-field' => 'username')
			// 	)
			// ),
			array(
				'name' => 'produktukar',
				'label' => 'Produk Tukar',
				'options' => array(),
				'width' => 2,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'ProdukTukars'),
					array('data-field' => 'nama')
				)
			),
			// array(
			// 	'name' => 'harga',
			// 	'label' => 'Harga',
			// 	'width' => 2,
			// 	'attributes' => array(
			// 		array('data-number' => 'true')
			// 	)
			// ),
			array(
				'name' => 'qty',
				'label' => 'Qty',
				'width' => 2,
				'attributes' => array(
					array('data-number' => 'true')
				)
			),
			// array(
			// 	'name' => 'total',
			// 	'label' => 'Total',
			// 	'width' => 2,
			// 	'attributes' => array(
			// 		array('data-number' => 'true')
			// 	)
			// ),
			[
				'name' => 'status',
				'label' => 'Status',
				'options' => [
					['text' => 'DIBAYAR', 'value' => 'DIBAYAR'],
					['text' => 'DIAMBIL', 'value' => 'DIAMBIL'],
				]
			]
		);

		$this->childs = array();
	}

	function dt()
	{
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

	function create($record)
	{
		$record['petugas'] = $this->session->userdata('uuid');

		$this->load->model(['ProdukTukars', 'Ledgers']);
		$produk = $this->ProdukTukars->findOne($record['produktukar']);
		$record['harga'] = $produk['harga'];
		$record['total'] = $record['harga'] * $record['qty'];
		$uuid = parent::create($record);
		$created = $this->findOne($uuid);

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
