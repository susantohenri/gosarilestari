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
}
