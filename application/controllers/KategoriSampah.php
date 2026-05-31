<?php defined('BASEPATH') or exit('No direct script access allowed');

class KategoriSampah extends MY_Controller
{

	function __construct()
	{
		$this->model = 'KategoriSampahs';
		$this->page_subtitle = 'Atur jenis sampah yang diterima dan harga per-kilogram-nya';
		parent::__construct();
	}
}
