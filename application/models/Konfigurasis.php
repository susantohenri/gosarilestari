<?php defined('BASEPATH') or exit('No direct script access allowed');

class Konfigurasis extends MY_Model
{

  function __construct()
  {
    parent::__construct();

    $this->table = 'konfigurasi';

    $this->thead = array(
      (object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
      (object) array('mData' => 'nama', 'sTitle' => 'NAMA'),
      (object) array('mData' => 'nilai', 'sTitle' => 'NILAI')
    );

    $this->form = array(
      array(
        'name' => 'nama',
        'label' => 'Nama',
        'attributes' => [['readonly' => true]]
      ),
      array(
        'name' => 'nilai',
        'label' => 'Nilai',
      ),
    );

    $this->childs = array();
  }

  function dt()
  {
    $this->datatables
      ->select("{$this->table}.uuid")
      ->select("{$this->table}.orders")
      ->select("konfigurasi.nama")
      ->select("konfigurasi.nilai");
    return parent::dt();
  }

  function getNilai($nama)
  {
    $found = $this->findOne(['nama' => $nama]);
    return $found['nilai'];
  }

  function updateSampahTerkumpul($berat)
  {
    return $this
      ->db
      ->where('nama', 'SAMPAH_TERKUMPUL')
      ->set('nilai', $berat)
      ->update($this->table);
  }

  function getSampahTerkumpul()
  {
    $konfigs = $this
      ->db
      ->select('nama')
      ->select('nilai')
      ->where_in('nama', ['SAMPAH_TERKUMPUL', 'TARGET_SAMPAH_BULAN_INI'])
      ->get($this->table)
      ->result();

    $berat = 0;
    $target = 0;
    foreach ($konfigs as $konf) {
      switch ($konf->nama) {
        case 'SAMPAH_TERKUMPUL':
          $berat = (float) $konf->nilai;
          break;
        case 'TARGET_SAMPAH_BULAN_INI':
          $target = (float) $konf->nilai;
          break;
      }
    }

    return [
      'berat' => number_format($berat, 2),
      'bulan_tahun' => date('F Y'),
      'persen' => number_format($berat / $target * 100, 2),
      'target' => number_format($target, 0)
    ];
  }
}
