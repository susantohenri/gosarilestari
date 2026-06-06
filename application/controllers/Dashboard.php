<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->page_title = '';
		$this->header_buttons = 'custom-header-buttons/dashboard-header-buttons';
    }

    public function index()
    {
        $this->page_title = 'Selamat datang kembali, ' . $this->session->userdata('nama');

        $today = date("Y-m-d");
        $bulan = date("F", strtotime($today));
        $tahun = date("Y", strtotime($today));
        $mingguKe = ceil(date("j", strtotime($today)) / 7);
        $this->page_subtitle = "Ringkasan Aktivitas Bank Sampah Kel. Sukamaju Minggu ke-$mingguKe $bulan $tahun";

        $vars = [];
        $vars['page_name'] = 'dashboard';
        $vars['header_buttons'] = 'custom-header-buttons/dashboard-header-buttons';
        $this->loadview('index', $vars);
    }
}
