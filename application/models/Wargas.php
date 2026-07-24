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
				'attributes' => [['required' => true]]
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
				'name' => 'agen',
				'label' => 'Agen',
				'options' => array(),
				'width' => 2,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'Agens'),
					array('data-field' => 'nama')
				)
			),
			array(
				'name' => 'saldo',
				'width' => 2,
				'label' => 'Saldo',
				'attributes' => [['readonly' => true]]
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

	function getRoleWarga()
	{
		$this->load->model('Roles');
		$role = $this->Roles->findOne(['name' => 'Warga']);
		return $role['uuid'];
	}

	function dt()
	{
		parse_str($this->input->post('customFilter'), $params);
		if ('' !== $params['fnama']) {
			$this->datatables->like("CONCAT(user.nama, user.kode)", $params['fnama'], false);
		}
		if ('' !== $params['rtrw']) {
			$this->datatables->where('rtrw.uuid', $params['rtrw']);
		}
		if ('' !== $params['agen']) {
			$this->datatables->where("{$this->table}.agen", $params['agen']);
		}
		if ('' !== $params['status']) {
			$this->datatables->where('user.status', $params['status']);
		}
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
			->where('activatedAt <>', null, false)
		;

		$controller = $this->router->class;
		$edit = site_url("{$controller}/Read/");
		$delete = site_url("{$controller}/Delete/");
		$printQr = site_url("ExportImport/printQrWarga/");

		if ('Admin' === $this->session->userdata('role_name')) {
			$this
				->db
				->select("CONCAT(
                '<div class=\"flex flex-wrap gap-2\">',
                '<a target=\"_blank\" class=\"px-2 py-1 text-xs text-white bg-blue-500 rounded hover:bg-blue-600\" href=\"{$printQr}', {$this->table}.uuid, '\"><i class=\"fa fa-qrcode\"></i></a>'
                '<a class=\"px-2 py-1 text-xs text-white bg-yellow-500 rounded hover:bg-yellow-600\" href=\"{$edit}', {$this->table}.uuid, '\"><i class=\"fa fa-file-lines\"></i></a>'
                '<a class=\"px-2 py-1 text-xs text-white bg-red-500 rounded hover:bg-red-600\" href=\"{$delete}', {$this->table}.uuid, '\"><i class=\"fa fa-trash\"></i></a>',
                '</div>'
            ) as aksi", false);
		} else {
			$this
				->db
				->select("CONCAT(
                '<div class=\"flex flex-wrap gap-2\">',
                '<a target=\"_blank\" class=\"px-2 py-1 text-xs text-white bg-blue-500 rounded hover:bg-blue-600\" href=\"{$printQr}', {$this->table}.uuid, '\"><i class=\"fa fa-qrcode\"></i></a>'
                '<a class=\"px-2 py-1 text-xs text-white bg-yellow-500 rounded hover:bg-yellow-600\" href=\"{$edit}', {$this->table}.uuid, '\"><i class=\"fa fa-file-lines\"></i></a>'
                '</div>'
            ) as aksi", false);
		}

		$this->db->order_by("{$this->table}.createdAt", 'desc');
		return $this
			->datatables
			->from($this->table)
			->where("{$this->table}.deletedAt", null)
			->generate();
	}

	public function find($param = [])
	{
		$this
			->db
			->select("{$this->table}.*")
			->join('role', 'user.role = role.uuid')
			->where('role.name', 'Warga');
		return parent::find($param);
	}

	public function getAllUuid()
	{
		$this->db->select("{$this->table}.uuid");
		return $this->find();
	}

	public function getSaldoMinus()
	{
		$this
			->db
			->select("{$this->table}.nama")
			->where("{$this->table}.saldo <", 0, false);
		return $this->find();
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
		$record['role'] = $this->getRoleWarga();
		return parent::create($record);
	}

	public function select2($field, $term)
	{
		if ('Agen' === $this->session->userdata('role_name')) {
			$this->db->where('agen', $this->session->userdata('uuid'));
		}

		return $this
			->db
			->select("{$this->table}.uuid as id", false)
			->select("{$this->table}.{$field} as text", false)
			->join('role', 'role.uuid = user.role', 'left')
			->where('role.name', 'Warga')
			->where('user.deletedAt', null)
			->where('user.status', 1)
			->where('activatedAt <>', null, false)
			->limit(10)
			->like($field, $term ?? '')
			->order_by("{$this->table}.createdAt", 'desc')
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

	function getOverView()
	{
		$role = $this->getRoleWarga();
		$wargas = $this
			->db
			->select('status')
			->select('saldo')
			->where('role', $role)
			->where('deletedAt', null)
			->where('activatedAt <>', null, false)
			->get($this->table)
			->result();

		$saldo = 0;
		$wargaAktif = 0;
		$wargTidakAktif = 0;
		foreach ($wargas as $warga) {
			$saldo += $warga->saldo;
			if ($warga->status == 1) {
				$wargaAktif++;
			} else {
				$wargTidakAktif++;
			}
		}

		return [
			[
				'color' => 'green',
				'icon' => 'fa-user',
				'label' => 'Total Warga',
				'value' => count($wargas),
			],
			[
				'color' => 'blue',
				'icon' => 'fa-check',
				'label' => 'Warga Aktif',
				'value' => $wargaAktif,
			],
			[
				'color' => 'red',
				'icon' => 'fa-times',
				'label' => 'Tidak Aktif',
				'value' => $wargTidakAktif,
			],
			[
				'color' => 'yellow',
				'icon' => 'fa-wallet',
				'label' => 'Total Saldo',
				'rp' => true,
				'value' => $saldo,
			],
		];
	}

	function wargaAktif($roleWarga)
	{
		return $this
			->db
			->where('status', 1)
			->where('deletedAt', null)
			->where('activatedAt <>', null, false)
			->where('role', $roleWarga)
			->count_all_results($this->table);
	}

	function mendaftarMingguIni($roleWarga)
	{
		$startOfWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
		$endOfWeek = date('Y-m-d 23:59:59', strtotime('sunday this week'));

		return $this
			->db
			->where('status', 1)
			->where('deletedAt', null)
			->where('role', $roleWarga)
			->where('activatedAt >=', "'{$startOfWeek}'", false)
			->where('activatedAt <=', "'{$endOfWeek}'", false)
			->count_all_results($this->table);
	}

	function saldoBeredar($roleWarga, $wargaUuid)
	{
		if (null !== $wargaUuid) $this->db->where('u.uuid', $wargaUuid);
		$this
			->db
			->select('SUM(u.saldo) as total_saldo', false)
			->from('user u')
			->where('u.deletedAt IS NULL', NULL, false)
			->where('u.status', 1)
			->where('role', $roleWarga);
		return $this->db->get()->row()->total_saldo ?? 0;
	}

	function activate($uuid)
	{
		$warga = $this->findOne($uuid);
		if (!$warga) return false;
		if (null !== $warga['activatedAt']) return false;
		$warga['activatedAt'] = date('Y-m-d H:i:s');
		unset($warga['confirm_password']);
		return parent::update($warga);
	}
}
