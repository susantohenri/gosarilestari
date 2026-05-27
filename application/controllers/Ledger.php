<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ledger extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Ledgers';
		parent::__construct();
	}
}
