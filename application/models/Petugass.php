<?php defined('BASEPATH') or exit('No direct script access allowed');

class Petugass extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'user';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'fnama', 'sTitle' => 'NAMA PETUGAS'),
			(object) array('mData' => 'fstatus', 'sTitle' => 'STATUS'),
			(object) array('mData' => 'aksi', 'sTitle' => 'AKSI'),
		);

		$this->form = array(
			array(
				'name' => 'nama',
				'width' => 2,
				'label' => 'Nama',
				'attributes' => [['required' => true]]
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

		$this->form[] = [
			'name' => 'username',
			'label' => 'Username'
		];

		$this->form[] = [
			'type' => 'password',
			'name' => 'password',
			'label' => 'Kata Sandi'
		];

		$this->form[] = [
			'type' => 'password',
			'name' => 'confirm_password',
			'label' => 'Ulang Kata Sandi'
		];
	}

	function getRolePetugas()
	{
		$this->load->model('Roles');
		$role = $this->Roles->findOne(['name' => 'Petugas']);
		return $role['uuid'];
	}

	function dt()
	{
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("CONCAT(user.nama, '<br>', user.kode) as fnama", false)
			->select("IF(user.status = 1, 'Aktif', 'Tidak Aktif') as fstatus", false)
			->select("'' as aksi", false)
			->join('role', 'role.uuid = user.role', 'left')
			->where('role.name', 'Petugas')
		;
		return parent::dt();
	}

	public function findOne($param)
	{
		$record = parent::findOne($param);
		$record['confirm_password'] = '';
		return $record;
	}

	public function save($data)
	{
		if (strlen($data['password']) > 0) {
			if ($data['password'] !== $data['confirm_password']) {
				return ['error' => ['message' => 'Password tidak sesuai']];
			} else {
				$data['password'] = md5($data['password']);
			}
		} else {
			unset($data['password']);
		}
		unset($data['confirm_password']);
		return parent::save($data);
	}

	function create($record)
	{
		$record['role'] = $this->getRolePetugas();
		return parent::create($record);
	}
}
