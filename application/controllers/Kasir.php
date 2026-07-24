<?php defined('BASEPATH') or exit('No direct script access allowed');

class Kasir extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Kasirs';
		parent::__construct();
	}
}
