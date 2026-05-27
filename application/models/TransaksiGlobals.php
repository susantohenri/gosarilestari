<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiGlobals extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'transaksiglobal';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'kode', 'sTitle' => 'KODE'),
			(object) array('mData' => 'fwarga', 'sTitle' => 'WARGA'),
			(object) array('mData' => 'tipe', 'sTitle' => 'TIPE'),
			(object) array('mData' => 'keterangan', 'sTitle' => 'KETERANGAN'),
			(object) array('mData' => 'fpetugas', 'sTitle' => 'PETUGAS'),
			(object) array('mData' => 'fwaktu', 'sTitle' => 'WAKTU'),
			(object) array('mData' => 'fnilai', 'sTitle' => 'NILAI'),
			(object) array('mData' => 'aksi', 'sTitle' => '')
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
				'name' => 'tipe',
				'width' => 2,
				'label' => 'Tipe',
			),
			array(
				'name' => 'keterangan',
				'width' => 2,
				'label' => 'Keterangan',
			),
			array(
				'name' => 'nilai',
				'label' => 'Nilai',
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
			->select("transaksiglobal.kode")
			->select("warga.nama as fwarga", false)
			->select("transaksiglobal.tipe")
			->select("transaksiglobal.keterangan")
			->select("user.username as fpetugas", false)
			->join("warga", "warga.uuid = transaksiglobal.warga", "left")
			->join("user", "user.uuid = transaksiglobal.petugas", "left")
		;
		return parent::dt();
	}
}
