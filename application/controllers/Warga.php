<?php defined('BASEPATH') or exit('No direct script access allowed');

class Warga extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Wargas';
		// $this->page_title = 'Manajemen Warga';
		$this->page_subtitle = 'Hanya admin yang dapat menambahkan warga baru';
		parent::__construct();
	}
}
