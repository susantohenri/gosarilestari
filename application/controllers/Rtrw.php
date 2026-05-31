<?php defined('BASEPATH') or exit('No direct script access allowed');

class Rtrw extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Rtrws';
		$this->page_title = 'RT/RW';
		$this->page_subtitle = 'Menentukan letak RT/RW (atau pedukuhan) dalam peta';
		parent::__construct();
	}
}
