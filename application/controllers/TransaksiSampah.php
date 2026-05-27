<?php defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiSampah extends MY_Controller
{

	function __construct()
	{
		$this->model = 'TransaksiSampahs';
		parent::__construct();
	}
}
