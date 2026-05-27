<?php defined('BASEPATH') or exit('No direct script access allowed');

class HasilPemilahans extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'hasilpemilahan';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'ftransaksisampah', 'sTitle' => 'Transaksi Sampah'),
			(object) array('mData' => 'fkategorisampah', 'sTitle' => 'Kategori Sampah'),
			(object) array('mData' => 'fberat', 'sTitle' => 'Berat'),
			(object) array('mData' => 'fharga', 'sTitle' => 'Harga'),
			(object) array('mData' => 'fpendapatan', 'sTitle' => 'Pendapatan'),
		);

		$this->form = array(
			// array(
			// 	'name' => 'transaksisampah',
			// 	'label' => 'Transaksisampah',
			// 	'options' => array(),
			// 	'width' => 2,
			// 	'attributes' => array(
			// 		array('data-autocomplete' => 'true'),
			// 		array('data-model' => 'TransaksiSampahs'),
			// 		array('data-field' => 'kode')
			// 	)
			// ),
			array(
				'name' => 'kategorisampah',
				'label' => 'Kategori Sampah',
				'options' => array(),
				'width' => 5,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'KategoriSampahs'),
					array('data-field' => 'nama')
				)
			),
			array(
				'name' => 'berat',
				'label' => 'Berat',
				'width' => 3,
				'attributes' => array(
					array('data-number' => 'true')
				)
			),
			// array(
			// 	'name' => 'harga',
			// 	'label' => 'Harga',
			// 	'width' => 3,
			// 	'attributes' => array(
			// 		array('data-number' => 'true')
			// 	)
			// ),
			// array(
			// 	'name' => 'pendapatan',
			// 	'label' => 'Pendapatan',
			// 	'width' => 2,
			// 	'attributes' => array(
			// 		array('data-number' => 'true')
			// 	)
			// ),
		);

		$this->childs = array();
	}

	function dt()
	{
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("transaksisampah.kode as ftransaksisampah", false)
			->select("kategorisampah.nama as fkategorisampah", false)
			->select("CONCAT(FORMAT(hasilpemilahan.berat, 0), ' KG') as fberat", false)
			->select("CONCAT('Rp ', FORMAT(hasilpemilahan.harga, 0, 'id_ID')) as fharga", false)
			->select("CONCAT('Rp ', FORMAT(hasilpemilahan.pendapatan, 0, 'id_ID')) as fpendapatan", false)
			->join('transaksisampah', 'transaksisampah.uuid = hasilpemilahan.transaksisampah', 'left')
			->join('kategorisampah', 'kategorisampah.uuid = hasilpemilahan.kategorisampah', 'left')
		;
		return parent::dt();
	}
}
