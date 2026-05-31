<?php defined('BASEPATH') or exit('No direct script access allowed');

class ProdukTukar extends MY_Controller
{

	function __construct()
	{
		$this->model = 'ProdukTukars';
		$this->page_subtitle = 'Kelola produk tukar yang bisa ditukar warga menggunakan saldo Bank Sampah';
		parent::__construct();
	}
}
