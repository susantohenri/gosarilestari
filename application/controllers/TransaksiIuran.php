<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiIuran extends MY_Controller
{

	function __construct()
	{
		$this->model = 'TransaksiIurans';
		parent::__construct();
	}
}
