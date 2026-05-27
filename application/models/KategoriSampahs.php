<?php defined('BASEPATH') or exit('No direct script access allowed');

class KategoriSampahs extends MY_Model
{

  function __construct()
  {
    parent::__construct();

    $this->table = 'kategorisampah';

    $this->thead = array(
      (object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
      (object) array('mData' => 'kode', 'sTitle' => '#'),
      (object) array('mData' => 'fnama', 'sTitle' => 'KATEGORI'),
      (object) array('mData' => 'fharga', 'sTitle' => 'HARGA/KG'),
      (object) array('mData' => 'fstatus', 'sTitle' => 'STATUS'),
      (object) array('mData' => 'aksi', 'sTitle' => 'AKSI')
    );

    $this->form = array(
      array(
        'name' => 'nama',
        'label' => 'Nama',
      ),
      array(
        'name' => 'contoh',
        'label' => 'Contoh',
      ),
      array(
        'name' => 'harga',
        'label' => 'Harga',
        'width' => 2,
        'attributes' => array(
          array('data-number' => 'true')
        )
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
      ->select("kategorisampah.kode")
      ->select("CONCAT(kategorisampah.nama, '<br>', kategorisampah.contoh) as fnama", false)
      ->select("CONCAT('Rp ', FORMAT(kategorisampah.harga, 0, 'id_ID')) as fharga", false)
      ->select("IF(kategorisampah.status = 1, 'Aktif', 'Tidak Aktif') as fstatus", false)
      ->select('"" as aksi')
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
}
