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

    public function BroadcastNotifikasi()
    {
        date_default_timezone_set('Asia/Jakarta');

        $this->load->model(['Konfigurasis', 'Notifikasis']);
        $config = [
            'TANGGAL_PENGIRIMAN_NOTIFIKASI_PETUGAS' => $this->Konfigurasis->getNilai('TANGGAL_PENGIRIMAN_NOTIFIKASI_PETUGAS')
        ];
        $todayDay = (int) date('j');

        $targetDate   = new DateTime('first day of last month');

        $period = $targetDate->format('m-Y');
        $judul  = $targetDate->format('F Y');

        $result = [
            'period' => $period,
            'petugas' => ['attempted' => 0, 'inserted' => 0],
        ];

        if ($todayDay >= (int) $config['TANGGAL_PENGIRIMAN_NOTIFIKASI_PETUGAS']) {
            $petugas = $this->Notifikasis->getUnnotifiedPetugas($period);
            $belumBayar = $this->Notifikasis->getWargaBelumBayar();

            $notifRows = [];
            foreach ($petugas as $row) {
                $notifRows[] = [
                    'uuid' => $this->Notifikasis->generateUuid(),
                    'kode' => strtoupper(base_convert(time() + rand(), 10, 36)),
                    'user' => $row->user_uuid,
                    'jenis' => 'RINGKASAN_PETUGAS',
                    'period' => $period,
                    'judul' => 'Ringkasan operasional periode ' . $judul,
                    'informasi' => $this->Notifikasis->kontenNotifikasiPetugas($judul, $belumBayar),
                ];
            }

            $result['petugas']['attempted'] = count($notifRows);
            if (!empty($notifRows)) {
                $result['petugas']['inserted'] = $this->Notifikasis->bulkInsertIgnoreError($notifRows);
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'message' => 'Cron executed',
                'data' => $result,
            ]));
    }
}
