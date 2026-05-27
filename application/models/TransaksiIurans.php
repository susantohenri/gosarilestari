<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiIurans extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'transaksiiuran';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'ftanggal', 'sTitle' => 'Tanggal'),
			(object) array('mData' => 'fwarga', 'sTitle' => 'Warga'),
			(object) array('mData' => 'fpetugas', 'sTitle' => 'Petugas'),
			(object) array('mData' => 'fbulantahun', 'sTitle' => 'Bulan'),
			(object) array('mData' => 'fnominal', 'sTitle' => 'Nominal'),
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
				'name' => 'bulan',
				'width' => 2,
				'label' => 'Bulan',
				'options' => [
					['text' => 'Januari', 'value' => 'Januari'],
					['text' => 'Februari', 'value' => 'Februari'],
					['text' => 'Maret', 'value' => 'Maret'],
					['text' => 'April', 'value' => 'April'],
					['text' => 'Mei', 'value' => 'Mei'],
					['text' => 'Juni', 'value' => 'Juni'],
					['text' => 'Juli', 'value' => 'Juli'],
					['text' => 'Agustus', 'value' => 'Agustus'],
					['text' => 'September', 'value' => 'September'],
					['text' => 'Oktober', 'value' => 'Oktober'],
					['text' => 'November', 'value' => 'November'],
					['text' => 'Desember', 'value' => 'Desember'],
				]
			),
			array(
				'name' => 'tahun',
				'width' => 2,
				'label' => 'Tahun',
				// 'options' => array_map(function ($year) {
				// 	return ['text' => (string) $year, 'value' => $year];
				// }, range(date('Y') - 2, date('Y') + 1))
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
			->select("DATE_FORMAT(transaksiiuran.createdAt, '%d %b %Y') as ftanggal", false)
			->select("warga.nama as fwarga", false)
			->select("user.username as fpetugas", false)
			->select("CONCAT(bulan, ' ', tahun) as fbulantahun", false)
			->select("CONCAT('Rp ', FORMAT(nominal, 0, 'id_ID')) as fnominal", false)
			->select("'' as aksi", false)
			->join('warga', 'warga.uuid = transaksiiuran.warga', 'left')
			->join('user', 'user.uuid = transaksiiuran.petugas', 'left')
		;
		return parent::dt();
	}
}
