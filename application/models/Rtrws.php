<?php defined('BASEPATH') or exit('No direct script access allowed');

class Rtrws extends MY_Model
{

  function __construct()
  {
    parent::__construct();

    $this->table = 'rtrw';

    $this->thead = array(
      (object) array('mData' => 'orders', 'sTitle' => 'No', 'visible' => false),
      (object) array('mData' => 'nama', 'sTitle' => 'RT/RW'),
      (object) array('mData' => 'latitude', 'sTitle' => 'LATITUDE'),
      (object) array('mData' => 'longitude', 'sTitle' => 'LONGITUDE'),
			(object) array('mData' => 'aksi', 'sTitle' => 'AKSI'),
    );

    $this->form = array(
      array(
        'name' => 'nama',
        'label' => 'RT/RW',
				'attributes' => [['required' => true]]
      ),
      array(
        'name' => 'latitude',
        'label' => 'Latitude',
      ),
      array(
        'name' => 'longitude',
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
      ->select('rtrw.nama')
      ->select('rtrw.latitude')
      ->select('rtrw.longitude')
			->select('"" as aksi')
    ;
    return parent::dt();
  }
}
