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
			(object) array('mData' => 'frtrw', 'sTitle' => 'RT/RW'),
			(object) array('mData' => 'fpetugas', 'sTitle' => 'PETUGAS'),
			(object) array('mData' => 'fberat', 'sTitle' => 'BERAT'),
			(object) array('mData' => 'fkategori', 'sTitle' => 'KATEGORI'),
			(object) array('mData' => 'fpendapatan', 'sTitle' => 'PENDAPATAN'),
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
			)
		);
	}

	function dt()
	{
		if ($mapFilter = $this->input->post('mapFilter')) {
			$this->db->where('rtrw.uuid', $mapFilter['rtrw']);
			$this->db->where('setorsampah.kategori', $mapFilter['kategori']);
		}
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("DATE_FORMAT(setorsampah.createdAt, '%d %b %Y') as ftanggal", false)
			->select("setorsampah.kode")
			->select("warga.nama as fwarga", false)
			->select("rtrw.nama as frtrw", false)
			->select("user.nama as fpetugas", false)
			->select("CONCAT(FORMAT(berat, 1), ' KG') as fberat", false)
			->select("CASE WHEN 'merah' = setorsampah.kategori THEN 'Tidak Terpilah' WHEN 'kuning' = setorsampah.kategori THEN 'Terpilah Sebagian' WHEN 'hijau' = setorsampah.kategori THEN 'Terpilah dg baik' END as fkategori", false)
			->select("CONCAT('Rp ', FORMAT(pendapatan, 0, 'id_ID')) as fpendapatan", false)
			->select('"" as aksi')
			->join('user warga', 'warga.uuid = setorsampah.warga', 'left')
			->join('rtrw', 'rtrw.uuid = warga.rtrw', 'left')
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

	function topKategori($wargaUuid)
	{
		if (null !== $wargaUuid) $this->db->where('ss.warga', $wargaUuid);
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

	public function getVolumeSampah7HariPerKategori($wargaUuid)
	{
		if (null !== $wargaUuid) $this->db->where('s.warga', $wargaUuid);
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

	public function peta()
	{
		$this->db->select('
        r.uuid,
        r.nama as nama_rtrw,
        r.kode,
        r.latitude,
        r.longitude,
        COUNT(DISTINCT u.uuid) as total_warga,
        COUNT(s.uuid) as total_setoran,
        COALESCE(SUM(s.berat), 0) as total_berat,
        MAX(s.createdAt) as last_update,
        
        -- Hitung jumlah per kategori
        SUM(CASE WHEN LOWER(TRIM(s.kategori)) = "merah" THEN 1 ELSE 0 END) as jml_merah,
        SUM(CASE WHEN LOWER(TRIM(s.kategori)) = "kuning" THEN 1 ELSE 0 END) as jml_kuning,
        SUM(CASE WHEN LOWER(TRIM(s.kategori)) IN ("hijau", "biru") THEN 1 ELSE 0 END) as jml_hijau
    ');

		$this->db->from('rtrw r');
		$this->db->join('user u', 'u.rtrw = r.uuid AND u.status = 1 AND u.deletedAt IS NULL', 'left');
		$this->db->join('setorsampah s', 's.warga = u.uuid AND s.status = 1 AND s.deletedAt IS NULL', 'left');

		$this->db->where('r.status', 1);
		$this->db->where('r.deletedAt IS NULL');
		$this->db->where('r.latitude !=', '');
		$this->db->where('r.longitude !=', '');
		$this->db->where('r.latitude !=', '0');
		$this->db->where('r.longitude !=', '0');

		$this->db->group_by('r.uuid, r.nama, r.kode, r.latitude, r.longitude');
		$this->db->order_by('r.nama', 'ASC');

		$query = $this->db->get();
		$rows = $query->result();

		$result = [];

		foreach ($rows as $row) {
			$total_setoran = (int) $row->total_setoran;

			// Tentukan status dominan
			if ($total_setoran == 0) {
				$status = 'Kosong';
				$kategori = '';
				$warna = '#9ca3af';
			} else {
				$persen_merah = ((int) $row->jml_merah / $total_setoran) * 100;
				$persen_hijau = ((int) $row->jml_hijau / $total_setoran) * 100;

				if ($persen_merah >= 50) {
					$status = 'Tidak Terpilah';
					$kategori = 'merah';
					$warna = '#ef4444';
				} elseif ($persen_hijau >= 50) {
					$status = 'Terpilah dg Baik';
					$kategori = 'hijau';
					$warna = '#22c55e';
				} else {
					$status = 'Tepilah Sebagian';
					$kategori = 'kuning';
					$warna = '#eab308';
				}
			}

			$result[] = (object) [
				'uuid_rtrw' => $row->uuid,
				'nama_rtrw' => $row->nama_rtrw,
				'kode' => $row->kode,
				'latitude' => floatval($row->latitude),
				'longitude' => floatval($row->longitude),
				'status' => $status,
				'kategori' => $kategori,
				'warna' => $warna,
				'total_setoran' => $total_setoran,
				'total_berat' => round((float) $row->total_berat, 2),
				'total_warga' => (int) $row->total_warga,
				'last_update' => $row->last_update ? date('d/m/Y', strtotime($row->last_update)) : '-'
			];
		}

		return $result;
	}

	public function getPopUp()
	{
		$sampahWarga = $this
			->db
			->select("FORMAT(SUM(berat), 2) AS berat", false)
			->select("kategori")
			->where('warga', $this->session->userdata('uuid'))
			->where("MONTH(createdAt)", date('m'))
			->where("YEAR(createdAt)", date('Y'))
			->group_by('kategori')
			->get($this->table)
			->result();

		$result = [
			'merah' => 0,
			'kuning' => 0,
			'hijau' => 0,
			'total' => 0,
		];
		foreach ($sampahWarga as $sampah) {
			$result['total'] += $sampah->berat;
			$result[$sampah->kategori] = $sampah->berat;
		}
		if (0 === $result['total']) return $result;

		$result['merah'] = $result['merah'] / $result['total'] * 100;
		$result['kuning'] = $result['kuning'] / $result['total'] * 100;
		$result['hijau'] = $result['hijau'] / $result['total'] * 100;
		return $result;
	}
}
