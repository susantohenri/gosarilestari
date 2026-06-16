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

	public function getTransactionDetails($uuid)
	{
		$query = $this->db
			->select('ledger.kode')
			->select("CASE ledger.tipe WHEN 'SETOR_SAMPAH' THEN 'Setor Sampah' WHEN 'TUKAR_PRODUK' THEN 'Tukar Produk' WHEN 'POTONG_IURAN' THEN 'Potong Iuran' WHEN 'SETOR_TUNAI' THEN 'Setor Tunai' END as tipe")
			->select('ledger.keterangan')
			->select("IF(0 < ledger.nilai, 'Saldo Bertambah', 'Saldo Berkurang') as saldo", false)
			->select('ledger.nilai')
			->select("IF(0 < ledger.nilai, CONCAT('Rp ', FORMAT(ledger.nilai, 0, 'id_ID')), CONCAT('- Rp ', FORMAT(ledger.nilai * -1, 0, 'id_ID'))) as fnilai")
			->select("CONCAT(warga.nama) as fwarga")
			->select('warga.kode as warga_kode')
			->select('user.nama as fpetugas')
			->select("DATE_FORMAT(ledger.createdAt, '%d %b %Y %H:%i') as fwaktu")
			->join("user warga", "warga.uuid = ledger.warga", "left")
			->join("user", "user.uuid = ledger.petugas", "left")
			->where('ledger.uuid', $uuid)
			->get($this->table);

		return $query->row_array();
	}

	public function progressSaldoPersen()
	{
		// Hitung total saldo
		$this->db->select('SUM(u.saldo) as total_saldo')
			->from('user u')
			->join('role r', 'r.uuid = u.role')
			->where('u.deletedAt IS NULL', NULL, false)
			->where('u.status', 1)
			->where('r.name', 'warga');
		$total_saldo = $this->db->get()->row()->total_saldo ?? 0;

		// Hitung perubahan minggu ini
		$this->db->select('SUM(nilai) as total_perubahan')
			->from('ledger')
			->where('deletedAt IS NULL', NULL, false)
			->where('createdAt >= DATE_SUB(NOW(), INTERVAL 1 MONTH)', NULL, false);
		$perubahan_minggu = $this->db->get()->row()->total_perubahan ?? 0;

		// Hitung manual
		$saldo_awal = $total_saldo - $perubahan_minggu;
		return ($saldo_awal == 0) ? 0 : ($perubahan_minggu / abs($saldo_awal)) * 100;
	}

	public function getTotalSetorTunaiBulanIni()
	{
		$this->db->select('COALESCE(SUM(nilai), 0) as total_setor_tunai')
			->from('ledger')
			->where('deletedAt IS NULL', NULL, false)
			->where('tipe', 'SETOR_TUNAI')
			->where('MONTH(createdAt)', date('m'))
			->where('YEAR(createdAt)', date('Y'));

		$result = $this->db->get()->row();
		return (float)($result->total_setor_tunai ?? 0);
	}

	public function progressSetorTunaiPersen()
	{
		// Total setor tunai bulan ini
		$sql_bulan_ini = "
        SELECT COALESCE(SUM(nilai), 0) as total
        FROM ledger
        WHERE deletedAt IS NULL
          AND tipe = 'SETOR_TUNAI'
          AND MONTH(createdAt) = MONTH(CURRENT_DATE())
          AND YEAR(createdAt) = YEAR(CURRENT_DATE())
    ";
		$total_bulan_ini = (float)$this->db->query($sql_bulan_ini)->row()->total;

		// Total setor tunai bulan lalu
		$sql_bulan_lalu = "
        SELECT COALESCE(SUM(nilai), 0) as total
        FROM ledger
        WHERE deletedAt IS NULL
          AND tipe = 'SETOR_TUNAI'
          AND MONTH(createdAt) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
          AND YEAR(createdAt) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
    ";
		$total_bulan_lalu = (float)$this->db->query($sql_bulan_lalu)->row()->total;

		// Hitung persentase perubahan (sama logic dengan saldo)
		if ($total_bulan_lalu == 0) {
			return 0;
		}

		$perubahan = $total_bulan_ini - $total_bulan_lalu;
		return ($perubahan / abs($total_bulan_lalu)) * 100;
	}

	public function getTotalTukarProdukBulanIni()
	{
		$this->db->select('COALESCE(SUM(ABS(nilai)), 0) as total')
			->from('ledger')
			->where('deletedAt IS NULL', NULL, false)
			->where('tipe', 'TUKAR_PRODUK')
			->where('MONTH(createdAt)', date('m'))
			->where('YEAR(createdAt)', date('Y'));

		$result = $this->db->get()->row();
		return (float)($result->total ?? 0);
	}

	public function progressTukarProdukPersen()
	{
		// Total bulan ini (pake ABS karena nilai di ledger negatif)
		$this->db->select('COALESCE(SUM(ABS(nilai)), 0) as total')
			->from('ledger')
			->where('deletedAt IS NULL', NULL, false)
			->where('tipe', 'TUKAR_PRODUK')
			->where('MONTH(createdAt)', date('m'))
			->where('YEAR(createdAt)', date('Y'));
		$bulan_ini = (float)($this->db->get()->row()->total ?? 0);

		// Total bulan lalu
		$this->db->select('COALESCE(SUM(ABS(nilai)), 0) as total')
			->from('ledger')
			->where('deletedAt IS NULL', NULL, false)
			->where('tipe', 'TUKAR_PRODUK')
			->where('MONTH(createdAt)', date('m', strtotime('-1 month')))
			->where('YEAR(createdAt)', date('Y', strtotime('-1 month')));
		$bulan_lalu = (float)($this->db->get()->row()->total ?? 0);

		if ($bulan_lalu == 0) {
			return 0;
		}

		$perubahan = $bulan_ini - $bulan_lalu;
		return ($perubahan / abs($bulan_lalu)) * 100;
	}
}
