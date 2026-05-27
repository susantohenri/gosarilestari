<?php defined('BASEPATH') or exit('No direct script access allowed');

class Rtrws extends MY_Model
{

  function __construct()
  {
    parent::__construct();

    $this->table = 'rtrw';

    $this->thead = array(
      (object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
      (object) array('mData' => 'rt', 'sTitle' => 'RT'),
      (object) array('mData' => 'rw', 'sTitle' => 'RW'),
      (object) array('mData' => 'latitude', 'sTitle' => 'LATITUDE'),
      (object) array('mData' => 'longitude', 'sTitle' => 'LONGITUDE'),
			(object) array('mData' => 'aksi', 'sTitle' => 'AKSI'),
    );

    $this->form = array(
      array(
        'name' => 'rt',
        'width' => 2,
        'label' => 'RT',
      ),
      array(
        'name' => 'rw',
        'width' => 2,
        'label' => 'RW',
      ),
      array(
        'name' => 'latitude',
        'width' => 2,
        'label' => 'Latitude',
      ),
      array(
        'name' => 'longitude',
        'width' => 2,
        'label' => 'Longitude',
      ),
    );

    $this->childs = array();
  }

  function dt()
  {
    $this->datatables
      ->select("{$this->table}.uuid")
      ->select("{$this->table}.orders")
      ->select('rtrw.rt')
      ->select('rtrw.rw')
      ->select('rtrw.latitude')
      ->select('rtrw.longitude')
			->select('"" as aksi')
    ;
    return parent::dt();
  }

  public function select2($field, $term)
  {
    return $this->db
      ->select("uuid as id", false)
      ->select("CONCAT('RT', rt, '/RW', rw) as text", false)
      ->where('deletedAt', null)
      ->limit(10)
      ->like('rt', $term ?? '')
      ->or_like('rw', $term ?? '')
      ->get($this->table)->result();
  }
}
