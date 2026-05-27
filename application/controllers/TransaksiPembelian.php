<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiPembelian extends MY_Controller
{

	function __construct()
	{
		$this->model = 'TransaksiPembelians';
		parent::__construct();
	}
}
