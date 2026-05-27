<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiPembelians extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'transaksipembelian';

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
			array(
				'name' => 'petugas',
				'label' => 'Petugas',
				'options' => array(),
				'width' => 2,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'Users'),
					array('data-field' => 'username')
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
			->select("DATE_FORMAT(transaksipembelian.createdAt, '%d %b %Y') as ftanggal", false)
			->select("warga.nama as fwarga", false)
			->select("user.username as fpetugas", false)
			->select("produktukar.nama as fproduktukar", false)
			->select("CONCAT('Rp ', FORMAT(transaksipembelian.harga, 0, 'id_ID')) as fharga", false)
			->select("FORMAT(transaksipembelian.qty, 0) as fqty", false)
			->select("CONCAT('Rp ', FORMAT(transaksipembelian.total, 0, 'id_ID')) as ftotal", false)
			->select("transaksipembelian.status as fstatus", false)
			->select("'' as aksi", false)
			->join('warga', 'warga.uuid = transaksipembelian.warga', 'left')
			->join('user', 'user.uuid = transaksipembelian.petugas', 'left')
			->join('produktukar', 'produktukar.uuid = transaksipembelian.produktukar', 'left')
		;
		return parent::dt();
	}
}
