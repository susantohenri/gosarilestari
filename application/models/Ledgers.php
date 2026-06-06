<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ledgers extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'ledger';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'kode', 'sTitle' => 'TRANSAKSI'),
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
		if ($customFilter = $this->input->post('customFilter')) {
			parse_str($customFilter, $params);
			if ('' !== $params['fnama']) {
				$this->db->like("CONCAT(warga.nama, warga.kode)", $params['fnama'], false);
			}
			if ('' !== $params['since']) {
				$this->db->where('ledger.createdAt >=', date('Y-m-d H:i:s', strtotime("-{$params['since']} days")));
			}
			if ('' !== $params['tipe']) {
				$this->db->where('ledger.tipe', $params['tipe']);
			}
		}

		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("ledger.kode")
			->select("CONCAT(warga.nama, '<br>', warga.kode) as fwarga", false)
			->select("CASE ledger.tipe WHEN 'SETOR_SAMPAH' THEN 'Setor Sampah' WHEN 'TUKAR_PRODUK' THEN 'Tukar Produk' WHEN 'POTONG_IURAN' THEN 'Potong Iuran' WHEN 'SETOR_TUNAI' THEN 'Setor Tunai' END as tipe")
			->select("ledger.keterangan")
			->select("user.nama as fpetugas", false)
			->select("DATE_FORMAT(ledger.createdAt, '%d %b %Y %H:%i') as fwaktu", false)
			->select("CONCAT('Rp ', FORMAT(ledger.nilai, 0, 'id_ID')) as fnilai", false)
			->select("'' as aksi", false)
			->join("user warga", "warga.uuid = ledger.warga", "left")
			->join("user", "user.uuid = ledger.petugas", "left")
		;
		return parent::dt();
	}

	function exportCsvPdf()
	{
		if ('Warga' === $this->session->userdata('role_name')) {
			$this->db->where('warga.uuid', $this->session->userdata('uuid'));
		}
		return $this
			->db
			->select("ledger.kode")
			->select("CONCAT(warga.nama, ' - ', warga.kode) as fwarga", false)
			->select("CASE ledger.tipe WHEN 'SETOR_SAMPAH' THEN 'Setor Sampah' WHEN 'TUKAR_PRODUK' THEN 'Tukar Produk' WHEN 'POTONG_IURAN' THEN 'Potong Iuran' WHEN 'SETOR_TUNAI' THEN 'Setor Tunai' END as tipe")
			->select("ledger.keterangan")
			->select("user.nama as fpetugas", false)
			->select("DATE_FORMAT(ledger.createdAt, '%d %b %Y %H:%i') as fwaktu", false)
			->select("CONCAT('Rp ', FORMAT(ledger.nilai, 0, 'id_ID')) as fnilai", false)
			->join("user warga", "warga.uuid = ledger.warga", "left")
			->join("user", "user.uuid = ledger.petugas", "left")
			->get($this->table)
			->result();
	}

	function save($record)
	{
		$uuid = parent::save($record);
		$flows = $this->find(['warga' => $record['warga'], 'status' => 1, 'deletedAt' => null]);
		$saldo = 0;
		foreach ($flows as $flow) $saldo += $flow->nilai;
		$this->load->model('Wargas');
		$this->Wargas->updateSaldo($record['warga'], $saldo);
		return $uuid;
	}

	public function create($record)
	{
		$generate = $this->db->select('UUID() uuid', false)->get()->row_array();
		$record['uuid'] = $generate['uuid'];
		$record['createdAt'] = date('Y-m-d H:i:s');
		$record['updatedAt'] = date('Y-m-d H:i:s');
		$this->db->insert($this->table, $record);
		return $record['uuid'];
	}

	public function getOverView()
	{
		$transaksi = $this
			->db
			->select('tipe')
			->select('nilai')
			->where('deletedAt', null)
			->get($this->table)
			->result();

		$setorSampah = 0;
		$tukarProduk = 0;
		$potongIuran = 0;

		foreach ($transaksi as $trx) {
			switch ($trx->tipe) {
				case 'SETOR_SAMPAH':
					$setorSampah += $trx->nilai;
					break;
				case 'TUKAR_PRODUK':
					$tukarProduk += $trx->nilai;
					break;
				case 'POTONG_IURAN':
					$potongIuran += $trx->nilai;
					break;
			}
		}

		return [
			[
				'icon' => 'fa-clock',
				'label' => 'Total Transaksi',
				'value' => count($transaksi),
			],
			[
				'icon' => 'fa-recycle',
				'label' => 'Setor Sampah',
				'rp' => true,
				'value' => $setorSampah,
			],
			[
				'icon' => 'fa-gift',
				'label' => 'Tukar Produk',
				'rp' => true,
				'value' => $tukarProduk * -1,
			],
			[
				'icon' => 'fa-file-text',
				'label' => 'Potong Iuran',
				'rp' => true,
				'value' => $potongIuran * -1,
			],
		];
	}
}
