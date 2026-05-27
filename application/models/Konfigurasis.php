<?php defined('BASEPATH') or exit('No direct script access allowed');

class Konfigurasis extends MY_Model
{

  function __construct()
  {
    parent::__construct();

    $this->table = 'konfigurasi';

    $this->thead = array(
      (object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
      (object) array('mData' => 'status', 'sTitle' => 'Status')
    );

    $this->form = array(
      array(
        'name' => 'nilai',
        'width' => 2,
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
      ->select('konfigurasi.status");
    return parent::dt();
  }
}
