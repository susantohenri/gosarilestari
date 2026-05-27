<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiSampahs extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'transaksisampah';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'kode', 'sTitle' => '#'),
			(object) array('mData' => 'ftanggal', 'sTitle' => 'TANGGAL'),
			(object) array('mData' => 'fwarga', 'sTitle' => 'WARGA'),
			(object) array('mData' => 'fpetugas', 'sTitle' => 'PETUGAS'),
			(object) array('mData' => 'status', 'sTitle' => 'STATUS'),
			(object) array('mData' => 'fberat', 'sTitle' => 'BERAT'),
			(object) array('mData' => 'fpendapatan', 'sTitle' => 'PENDAPATAN'),
			(object) array('mData' => 'ftagihan', 'sTitle' => 'TAGIHAN'),
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
				'name' => 'kategori',
				'label' => 'Kategori',
				'width' => 2,
				'options' => array(
					array('text' => 'Merah (tidak terpilah)', 'value' => 'merah'),
					array('text' => 'Kuning (terpilah sebagian)', 'value' => 'kuning'),
					array('text' => 'Hijau (terpilah dg baik)', 'value' => 'hijau'),
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
			// array(
			// 	'name' => 'pendapatan',
			// 	'label' => 'Pendapatan',
			// 	'width' => 2,
			// 	'attributes' => array(
			// 		array('data-number' => 'true')
			// 	)
			// ),
			array(
				'name' => 'tagihan',
				'label' => 'Tagihan',
				'width' => 2,
				'attributes' => array(
					array('data-number' => 'true')
				)
			),
			[
				'name' => 'status',
				'label' => 'Status',
				'options' => [
					['text' => 'DISETOR', 'value' => 'DISETOR'],
					['text' => 'DIPILAH', 'value' => 'DIPILAH']
				]
			]
		);

		$this->childs[] = array('label' => 'Hasil Pemilahan', 'controller' => 'HasilPemilahan', 'model' => 'HasilPemilahans');
	}

	function dt()
	{
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("DATE_FORMAT(transaksisampah.createdAt, '%d %b %Y') as ftanggal", false)
			->select("transaksisampah.kode")
			->select("warga.nama as fwarga", false)
			->select("user.username as fpetugas", false)
			->select("transaksisampah.status")
			->select("CONCAT(FORMAT(berat, 0), ' KG') as fberat", false)
			->select("CONCAT('Rp ', FORMAT(pendapatan, 0, 'id_ID')) as fpendapatan", false)
			->select("CONCAT('Rp ', FORMAT(tagihan, 0, 'id_ID')) as ftagihan", false)
			->select('"" as aksi')
			->join('warga', 'warga.uuid = transaksisampah.warga', 'left')
			->join('user', 'user.uuid = transaksisampah.petugas', 'left')
		;
		return parent::dt();
	}
}
