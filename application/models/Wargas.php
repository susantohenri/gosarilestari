<?php defined('BASEPATH') or exit('No direct script access allowed');

class Wargas extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'user';

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

	function getRoleWarga()
	{
		$this->load->model('Roles');
		$role = $this->Roles->findOne(['name' => 'Warga']);
		return $role['uuid'];
	}

	function dt()
	{
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("CONCAT(user.nama, '<br>', user.kode) as fnama", false)
			->select("CONCAT(user.kontak, '<br>', user.alamat) as fkontak", false)
			->select("rtrw.nama as frtrw", false)
			->select("CONCAT('Rp ', FORMAT(saldo, 0, 'id_ID')) as fsaldo", false)
			->select("DATE_FORMAT(user.createdAt, '%d %b %Y') as bergabung", false)
			->select("IF(user.status = 1, 'Aktif', 'Tidak Aktif') as fstatus", false)
			->select("'' as aksi", false)
			->join('rtrw', 'rtrw.uuid = user.rtrw', 'left')
			->join('role', 'role.uuid = user.role', 'left')
			->where('role.name', 'Warga')
		;
		return parent::dt();
	}

	function create($record)
	{
		$record['role'] = $this->getRoleWarga();
		return parent::create($record);
	}

	public function select2($field, $term)
	{
		return $this
			->db
			->select("{$this->table}.uuid as id", false)
			->select("{$this->table}.{$field} as text", false)
			->join('role', 'role.uuid = user.role', 'left')
			->where('role.name', 'Warga')
			->where('user.deletedAt', null)
			->where('user.status', 1)
			->limit(10)
			->like($field, $term ?? '')
			->get($this->table)
			->result();
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
