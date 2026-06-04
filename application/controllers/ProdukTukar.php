<?php defined('BASEPATH') or exit('No direct script access allowed');

class ProdukTukar extends MY_Controller
{

	function __construct()
	{
		$this->model = 'ProdukTukars';
		parent::__construct();
		$this->page_subtitle = 'Kelola produk tukar yang bisa ditukar warga menggunakan saldo Bank Sampah';
		$this->header_buttons = 'custom-header-buttons/produk-tukar-header-buttons';
	}
}
