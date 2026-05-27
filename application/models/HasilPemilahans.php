<?php defined('BASEPATH') or exit('No direct script access allowed');

class HasilPemilahans extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'hasilpemilahan';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'status', 'sTitle' => 'Status'),
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
				'label' => 'Kategorisampah',
				'options' => array(),
				'width' => 2,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'KategoriSampahs'),
					array('data-field' => 'nama')
				)
			),
			array(
				'name' => 'berat',
				'label' => 'Berat',
				'width' => 2,
				'attributes' => array(
					array('data-number' => 'true')
				)
			),
			array(
				'name' => 'harga',
				'label' => 'Harga',
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
		);

		$this->childs = array();
	}

	function dt()
	{
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("hasilpemilahan.status");
		return parent::dt();
	}
}
