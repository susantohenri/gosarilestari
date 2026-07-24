<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CLI extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!is_cli()) {
            show_404();
        }
    }

    public function Migrate($version = null)
    {
        $this->load->library('migration');
        $success = !is_null($version) ? $this->migration->version($version) : $this->migration->latest();
        if (!$success) {
            show_error($this->migration->error_string());
        }
    }

    public function KirimTagihanBulananWarga()
    {
        $this->load->model(['Ledgers', 'Konfigurasis']);
        $nominal = $this->Konfigurasis->getNilai('SETORAN_BULANAN');
        $fnominal = number_format($nominal, 0, ',', '.');
        $bulanTahun = date('F Y');
        $wargas = $this->Ledgers->getWargaTertagih();
        foreach ($wargas as $warga) {
            $this->Ledgers->save([
                'kode' => strtoupper(base_convert(time() + rand(), 10, 36)),
                'warga' => $warga->uuid,
                'petugas' => 'SYSTEM',
                'tipe' => 'POTONG_IURAN',
                'keterangan' => "Potong iuran {$bulanTahun} senilai Rp {$fnominal}",
                'nilai' => $nominal * -1
            ]);
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Cron executed',
            ]));
    }

    public function KirimNotifikasiBulananAgen()
    {
        $this->load->model('Notifikasis');
        $this->Notifikasis->reminderBulananAgen();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Cron executed',
            ]));
    }
}
