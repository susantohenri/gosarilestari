<?php defined('BASEPATH') or exit('No direct script access allowed');

class ProdukTukars extends MY_Model
{

	function __construct()
	{
		parent::__construct();

		$this->table = 'produktukar';

		$this->thead = array(
			(object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
			(object) array('mData' => 'kode', 'sTitle' => '#'),
			(object) array('mData' => 'nama', 'sTitle' => 'PRODUK'),
			(object) array('mData' => 'kategori', 'sTitle' => 'KATEGORI'),
			(object) array('mData' => 'fharga', 'sTitle' => 'HARGA'),
			(object) array('mData' => 'fstok', 'sTitle' => 'STOK'),
			(object) array('mData' => 'fterjual', 'sTitle' => 'TERJUAL'),
			(object) array('mData' => 'aksi', 'sTitle' => 'AKSI')
		);

		$this->form = array(
			array(
				'name' => 'nama',
				'width' => 2,
				'label' => 'Nama',
				'attributes' => [['required' => true]]
			),
			array(
				'name' => 'kategori',
				'width' => 2,
				'label' => 'Kategori',
			),
			array(
				'name' => 'harga',
				'label' => 'Harga',
				'width' => 2,
				'attributes' => array(
					array('data-number' => 'true')
				)
			),
			array(
				'name' => 'stok',
				'label' => 'Stok',
				'width' => 2,
				'attributes' => array(
					array('data-number' => 'true')
				)
			),
			array(
				'name' => 'terjual',
				'label' => 'Terjual',
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
		$this->datatables
			->select("{$this->table}.uuid")
			->select("{$this->table}.orders")
			->select("produktukar.kode")
			->select("produktukar.nama")
			->select("produktukar.kategori")
			->select("CONCAT('Rp ', FORMAT(produktukar.harga, 0, 'id_ID')) as fharga", false)
			->select("FORMAT(produktukar.stok, 0) as fstok", false)
			->select("FORMAT(produktukar.terjual, 0) as fterjual", false)
			->select('"" as aksi')
		;
		return parent::dt();
	}

	public function sold($uuid, $qty)
	{
		return $this->db
			->where('uuid', $uuid)
			->set('stok', 'stok - ' . $qty, FALSE)
			->set('terjual', 'terjual + ' . $qty, FALSE)
			->update($this->table);
	}

	public function getOverView()
	{
		$this->load->model('Konfigurasis');
		$rendah = $this->Konfigurasis->getNilai('BATAS_MINIMUM_STOK_RENDAH');

		$produks = $this
			->db
			->select('status')
			->select('stok')
			->where('deletedAt', null)
			->get($this->table)
			->result();

		$aktif = 0;
		$stokRendah = 0;
		$habis = 0;
		foreach ($produks as $produk) {
			if (1 == $produk->status) {
				$aktif++;
			}

			if ($produk->stok <= 0) {
				$habis++;
			} else if ($produk->stok <= $rendah) {
				$stokRendah++;
			}
		}

		return [
			[
				'icon' => 'fa-cube',
				'label' => 'Total Produk',
				'value' => count($produks),
			],
			[
				'icon' => 'fa-check',
				'label' => 'Aktif',
				'value' => $aktif,
			],
			[
				'icon' => 'fa-bell',
				'label' => 'Stok Rendah',
				'value' => $stokRendah,
			],
			[
				'icon' => 'fa-times',
				'label' => 'Habis',
				'value' => $habis,
			],
		];
	}

	public function getCategories()
	{
		return $this
			->db
			->select('kategori')
			->select("COUNT(uuid) AS product_count", false)
			->group_by('kategori')
			->get($this->table)
			->result();
	}
}
