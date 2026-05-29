<?php defined('BASEPATH') or exit('No direct script access allowed');

class SetorSampahs extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		// rename tabel menjadi setorsampah
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
					array('data-field' => 'nama')
				)
			),
			// array(
			// 	'name' => 'petugas',
			// 	'label' => 'Petugas',
			// 	'options' => array(),
			// 	'width' => 2,
			// 	'attributes' => array(
			// 		array('data-autocomplete' => 'true'),
			// 		array('data-model' => 'Users'),
			// 		array('data-field' => 'username')
			// 	)
			// ),
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
				// 'attributes' => array(
				// 	array('data-number' => 'true')
				// )
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
			$this->load->model(['Ledgers', 'KategoriSampahs']);
			$kategoriSampah = $this->KategoriSampahs->findOne($created['kategorisampah']);
			$this->Ledgers->save(array_merge($ledgerObj, [
				'tipe' => 'SETOR_SAMPAH',
				'keterangan' => "{$kategoriSampah['nama']} {$created['berat']} Kg",
				'nilai' => $created['pendapatan']
			]));
		}

		// ledger tagihan (potong iuran)
		if (0 < $created['tagihan']) {
			$this->load->model(['Ledgers']);
			$bulan = date('M');
			$tahun = date('Y');
			$this->Ledgers->save(array_merge($ledgerObj, [
				'tipe' => 'POTONG_IURAN',
				'keterangan' => "Iuran {$bulan} {$tahun}",
				'nilai' => $created['tagihan'] * -1
			]));
		}

		return $uuid;
	}
}
