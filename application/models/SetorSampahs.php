<?php defined('BASEPATH') or exit('No direct script access allowed');

class SetorSampahs extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'setorsampah';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'kode', 'sTitle' => '#'),
			(object) array('mData' => 'ftanggal', 'sTitle' => 'TANGGAL'),
			(object) array('mData' => 'fwarga', 'sTitle' => 'WARGA'),
			(object) array('mData' => 'fpetugas', 'sTitle' => 'PETUGAS'),
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
					array('data-field' => 'nama'),
					array('required' => true)
				)
			),
			array(
				'name' => 'kategori',
				'label' => 'Kategori Pemilahan',
				'width' => 2,
				'options' => array(
					array('text' => 'Merah (tidak terpilah)', 'value' => 'merah'),
					array('text' => 'Kuning (terpilah sebagian)', 'value' => 'kuning'),
					array('text' => 'Hijau (terpilah dg baik)', 'value' => 'hijau'),
				)
			),
			array(
				'name' => 'kategorisampah',
				'label' => 'Kategori Sampah',
				'options' => array(),
				'width' => 3,
				'attributes' => array(
					array('data-autocomplete' => 'true'),
					array('data-model' => 'KategoriSampahs'),
					array('data-field' => 'nama')
				)
			),
			array(
				'name' => 'berat',
				'label' => 'Berat (Kg)',
				'width' => 2,
			),
			array(
				'name' => 'tagihan',
				'label' => 'Tagihan (Rp)',
				'width' => 2,
				'attributes' => array(
					array('data-number' => 'true')
				)
			)
		);
	}

	function dt()
	{
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("DATE_FORMAT(setorsampah.createdAt, '%d %b %Y') as ftanggal", false)
			->select("setorsampah.kode")
			->select("warga.nama as fwarga", false)
			->select("user.nama as fpetugas", false)
			->select("CONCAT(FORMAT(berat, 1), ' KG') as fberat", false)
			->select("CONCAT('Rp ', FORMAT(pendapatan, 0, 'id_ID')) as fpendapatan", false)
			->select("CONCAT('Rp ', FORMAT(tagihan, 0, 'id_ID')) as ftagihan", false)
			->select('"" as aksi')
			->join('user warga', 'warga.uuid = setorsampah.warga', 'left')
			->join('user', 'user.uuid = setorsampah.petugas', 'left')
		;
		return parent::dt();
	}

	public function save($record)
	{
		if (isset($record['kategorisampah'])) {
			$this->load->model('KategoriSampahs');
			$kategoriSampah = $this->KategoriSampahs->findOne($record['kategorisampah']);
			if (isset($kategoriSampah['harga'])) {
				$record['pendapatan'] = $record['berat'] * $kategoriSampah['harga'];
			}
		}
		return parent::save($record);
	}

	function create($record)
	{
		// create transaction
		$record['petugas'] = $this->session->userdata('uuid');
		$uuid = parent::create($record);

		$this->load->model(['Ledgers', 'Konfigurasis']);

		// basic ledger object
		$created = $this->findOne($uuid);
		$ledgerObj = [
			'kode' => $created['kode'],
			'transaksi' => $created['uuid'],
			'warga' => $created['warga'],
			'petugas' => $created['petugas'],
		];

		// ledger pendapatan (tukar sampah)
		if (0 < $created['pendapatan']) {
			$this->load->model('KategoriSampahs');
			$kategoriSampah = $this->KategoriSampahs->findOne($created['kategorisampah']);
			$this->Ledgers->save(array_merge($ledgerObj, [
				'tipe' => 'SETOR_SAMPAH',
				'keterangan' => "{$kategoriSampah['nama']} {$created['berat']} Kg",
				'nilai' => $created['pendapatan']
			]));
		}

		// ledger tagihan (potong iuran)
		if (0 < $created['tagihan']) {
			$bulan = date('M');
			$tahun = date('Y');
			$this->Ledgers->save(array_merge($ledgerObj, [
				'tipe' => 'POTONG_IURAN',
				'keterangan' => "Iuran {$bulan} {$tahun}",
				'nilai' => $created['tagihan'] * -1
			]));
		}

		$sumBerat = $this->sampahTerkumpulBulanIni();
		$this->Konfigurasis->updateSampahTerkumpul($sumBerat);

		return $uuid;
	}

	private function sampahTerkumpulBulanIni()
	{
		$sum = $this->db
			->select_sum('berat', 'total_berat')
			->where('MONTH(createdAt)', date('m'), false)
			->where('YEAR(createdAt)', date('Y'), false)
			->get($this->table)
			->row_array();
		return number_format($sum['total_berat'], 2);
	}

	function topKategori()
	{
		$this
			->db
			->select('k.nama as nama_kategori, COALESCE(SUM(ss.berat), 0) as total_berat')
			->from('setorsampah ss')
			->join('kategorisampah k', 'k.uuid = ss.kategorisampah')
			->where('ss.deletedAt IS NULL', NULL, false)
			->where('ss.status', 1)
			->where('MONTH(ss.createdAt)', date('m'))
			->where('YEAR(ss.createdAt)', date('Y'))
			->group_by('ss.kategorisampah')
			->order_by('total_berat', 'DESC');

		$query = $this->db->get();
		return $query->result();
	}

	public function getVolumeSampah7HariPerKategori()
	{
		$startDate = date('Y-m-d', strtotime('-6 days'));
		$endDate = date('Y-m-d');

		$this->db->select('DATE(s.createdAt) as tanggal, k.nama as kategori, SUM(s.berat) as total_berat');
		$this->db->from('setorsampah s');
		$this->db->join('kategorisampah k', 's.kategorisampah = k.uuid', 'left');
		$this->db->where('DATE(s.createdAt) >=', $startDate);
		$this->db->where('DATE(s.createdAt) <=', $endDate);
		$this->db->where('s.deletedAt IS NULL');
		$this->db->where('k.deletedAt IS NULL');
		$this->db->where('k.nama IS NOT NULL'); // Filter kategori yang terdaftar
		$this->db->group_by('DATE(s.createdAt), k.nama');
		$this->db->order_by('tanggal', 'ASC');

		$query = $this->db->get();
		$rows = $query->result_array();

		return $this->formatChartData($rows);
	}

	private function formatChartData($rawData)
	{
		// Ambil semua tanggal dalam 7 hari terakhir
		$dates = array();
		for ($i = 6; $i >= 0; $i--) {
			$dates[] = date('D', strtotime("-$i days"));
		}

		// Ambil semua kategori unik
		$categories = array_unique(array_column($rawData, 'kategori'));
		$categories = array_filter($categories, function ($cat) {
			return !empty($cat) && $cat !== null;
		});
		$categories = array_values($categories);

		// Jika tidak ada kategori, return data kosong
		if (empty($categories)) {
			return array(
				'labels' => $dates,
				'datasets' => array()
			);
		}

		// Warna untuk tiap kategori (sama seperti contoh)
		$colors = array(
			'rgba(54, 162, 235, 0.7)',  // biru (Plastik)
			'rgba(255, 99, 132, 0.7)',  // merah (Kertas)
			'rgba(255, 206, 86, 0.7)',  // kuning (Logam)
			'rgba(75, 192, 192, 0.7)',  // teal (Kaca)
			'rgba(153, 102, 255, 0.7)', // ungu (Minyak Jelantah)
			'rgba(255, 159, 64, 0.7)'   // oranye
		);

		$chartData = array(
			'labels' => $dates,
			'datasets' => array()
		);

		$colorIndex = 0;

		foreach ($categories as $cat) {
			$dataset = array(
				'label' => $cat,
				'data' => array_fill(0, 7, 0),
				'backgroundColor' => $colors[$colorIndex % count($colors)],
				'borderRadius' => 4
			);

			// Isi data sesuai tanggal
			foreach ($rawData as $row) {
				if ($row['kategori'] === $cat) {
					$dayIndex = array_search(date('D', strtotime($row['tanggal'])), $dates);
					if ($dayIndex !== false) {
						$dataset['data'][$dayIndex] = (float) $row['total_berat'];
					}
				}
			}

			$chartData['datasets'][] = $dataset;
			$colorIndex++;
		}

		return $chartData;
	}
}
