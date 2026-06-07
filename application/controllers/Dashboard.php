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
        $this->page_subtitle = "Ringkasan Aktivitas Bank Sampah Minggu ke-$mingguKe $bulan $tahun";

        $vars = [];
        $vars['page_name'] = 'dashboard';
        $vars['header_buttons'] = 'custom-header-buttons/dashboard-header-buttons';

        $this->load->model(['Wargas', 'Ledgers', 'SetorSampahs']);
        $roleWarga = $this->Wargas->getRoleWarga();
        $vars = array_merge($vars, [
            'card_warga' => [
                'warga_aktif' => $this->Wargas->wargaAktif($roleWarga),
                'mendaftar_minggu_ini' => $this->Wargas->mendaftarMingguIni($roleWarga)
            ],
            'card_saldo' => [
                'beredar' => $this->Wargas->saldoBeredar($roleWarga),
                'progress' => $this->Ledgers->progressSaldoPersen()
            ],
            'card_setoran' => [
                'bulan_ini' => $this->Ledgers->getTotalSetorTunaiBulanIni(),
                'progress' => $this->Ledgers->progressSetorTunaiPersen()
            ],
            'card_tukar_produk' => [
                'bulan_ini' => $this->Ledgers->getTotalTukarProdukBulanIni(),
                'progress' => $this->Ledgers->progressTukarProdukPersen()
            ],
            'card_grafik' => [
                'items' => $this->SetorSampahs->getVolumeSampah7HariPerKategori()
            ],
            'card_kategori' => [
                'items' => $this->SetorSampahs->topKategori()
            ]
        ]);

        $this->loadview('index', $vars);
    }
}
