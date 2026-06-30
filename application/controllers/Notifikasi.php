<?php defined('BASEPATH') or exit('No direct script access allowed');

class Notifikasi extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Notifikasis';
		parent::__construct();
	}

	function read($id)
	{
		$vars = [
			'page_name' => 'notifikasi',
			'notif' => $this->Notifikasis->read($id)
		];
		$this->loadview('index', $vars);
	}
}
