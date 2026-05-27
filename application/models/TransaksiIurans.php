<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiIurans extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'transaksiiuran';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'status', 'sTitle' => 'Status'),
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
				'name' => 'bulan',
				'width' => 2,
				'label' => 'Bulan',
			),
			array(
				'name' => 'tahun',
				'width' => 2,
				'label' => 'Tahun',
			),
			array(
				'name' => 'nominal',
				'label' => 'Nominal',
				'width' => 2,
				'attributes' => array(
					array('data-number' => 'true')
				)
			),
		);

		$this->childs = array();
	}

	function dt()
	{
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("transaksiiuran.status");
		return parent::dt();
	}
}
