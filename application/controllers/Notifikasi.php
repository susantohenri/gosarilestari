<?php defined('BASEPATH') or exit('No direct script access allowed');

class Notifikasi extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Notifikasis';
		parent::__construct();
	}

	function read ($uuid) {
		$this->Notifikasis->read($uuid);
		return parent::read($uuid);
	}
}
