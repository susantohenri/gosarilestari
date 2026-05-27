<?php defined('BASEPATH') or exit('No direct script access allowed');

class Wargas extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'warga';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'fnama', 'sTitle' => 'NAMA WARGA'),
			(object) array('mData' => 'fkontak', 'sTitle' => 'KONTAK'),
			(object) array('mData' => 'frtrw', 'sTitle' => 'RT/RW'),
			(object) array('mData' => 'fsaldo', 'sTitle' => 'SALDO'),
			(object) array('mData' => 'bergabung', 'sTitle' => 'BERGABUNG'),
			(object) array('mData' => 'fstatus', 'sTitle' => 'STATUS'),
			(object) array('mData' => 'aksi', 'sTitle' => 'AKSI'),
		);

		$this->form = array(
			array(
				'name' => 'nama',
				'width' => 2,
				'label' => 'Nama',
			),
			array(
				'name' => 'kontak',
				'width' => 2,
				'label' => 'Kontak',
			),
			array(
				'name' => 'alamat',
				'width' => 2,
				'label' => 'Alamat',
			),
			array(
				'name' => 'rtrw',
				'label' => 'Rtrw',
				'options' => array(),
				'width' => 2,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'Rtrws'),
					array('data-field' => 'nama')
				)
			),
			array(
				'name' => 'saldo',
				'width' => 2,
				'label' => 'Saldo',
			),
			[
				'name' => 'status',
				'label' => 'Status',
				'options' => [
					['text' => 'Aktif', 'value' => '1'],
					['text' => 'Tidak Aktif', 'value' => '0']
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
			->select("CONCAT(warga.nama, '<br>', warga.kode) as fnama", false)
			->select("CONCAT(warga.kontak, '<br>', warga.alamat) as fkontak", false)
			->select("rtrw.nama as frtrw", false)
			->select("CONCAT('Rp ', FORMAT(saldo, 0, 'id_ID')) as fsaldo", false)
			->select("DATE_FORMAT(warga.createdAt, '%d %b %Y') as bergabung", false)
			->select("IF(warga.status = 1, 'Aktif', 'Tidak Aktif') as fstatus", false)
			->select("'' as aksi", false)
			->join('rtrw', 'rtrw.uuid = warga.rtrw', 'left')
		;
		return parent::dt();
	}

	public function select2($field, $term)
	{
		return $this->db
			->select("uuid as id", false)
			->select("$field as text", false)
			->where('deletedAt', null)
			->where('status', 1)
			->limit(10)
			->like($field, $term ?? '')->get($this->table)->result();
	}

	function updateSaldo($uuid, $saldo)
	{
		return $this
			->db
			->where('uuid', $uuid)
			->set('saldo', $saldo)
			->update($this->table);
	}
}
