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
            'TANGGAL_PENGIRIMAN_NOTIFIKASI_WARGA' => $this->Konfigurasis->getNilai('TANGGAL_PENGIRIMAN_NOTIFIKASI_WARGA'),
            'TANGGAL_PENGIRIMAN_NOTIFIKASI_PETUGAS' => $this->Konfigurasis->getNilai('TANGGAL_PENGIRIMAN_NOTIFIKASI_PETUGAS')
        ];
        $todayDay = (int) date('j');

        $targetDate   = new DateTime('first day of last month');
        $currentStart = (new DateTime('first day of this month 00:00:00'))->format('Y-m-d H:i:s');
        $targetStart  = (new DateTime($targetDate->format('Y-m-01 00:00:00')))->format('Y-m-d H:i:s');

        $period = $targetDate->format('m-Y');
        $bulan  = $targetDate->format('m');
        $tahun  = (int) $targetDate->format('Y');
        $judul  = $targetDate->format('F Y');

        $result = [
            'period' => $period,
            'warga' => ['attempted' => 0, 'inserted' => 0],
            'petugas' => ['attempted' => 0, 'inserted' => 0],
        ];

        if ($todayDay >= (int) $config['TANGGAL_PENGIRIMAN_NOTIFIKASI_WARGA']) {
            $wargaRows = $this->Notifikasis->getUnnotifiedWargaSummary(
                $targetStart,
                $currentStart,
                $bulan,
                $tahun,
                $period
            );

            $notifRows = [];
            foreach ($wargaRows as $row) {
                $notifRows[] = [
                    'uuid' => $this->Notifikasis->generateUuid(),
                    'kode' => strtoupper(base_convert(time() + rand(), 10, 36)),
                    'user' => $row->user_uuid,
                    'jenis' => 'RINGKASAN_WARGA',
                    'period' => $period,
                    'judul' => 'Ringkasan sampah periode ' . $judul,
                    'informasi' => $this->Notifikasis->kontenNotifikasiWarga($row, $period),
                ];
            }

            $result['warga']['attempted'] = count($notifRows);
            if (!empty($notifRows)) {
                $result['warga']['inserted'] = $this->Notifikasis->bulkInsertIgnoreError($notifRows);
            }
        }

        if ($todayDay >= (int) $config['TANGGAL_PENGIRIMAN_NOTIFIKASI_PETUGAS']) {
            $petugas = $this->Notifikasis->getUnnotifiedPetugas($period);
            $belumBayar = $this->Notifikasis->getWargaBelumBayar(
                $targetStart,
                $currentStart,
                $bulan,
                $tahun
            );
            $belumSetor = $this->Notifikasis->getWargaBelumSetor(
                $targetStart,
                $currentStart
            );

            $notifRows = [];
            foreach ($petugas as $row) {
                $notifRows[] = [
                    'uuid' => $this->Notifikasis->generateUuid(),
                    'kode' => strtoupper(base_convert(time() + rand(), 10, 36)),
                    'user' => $row->user_uuid,
                    'jenis' => 'RINGKASAN_PETUGAS',
                    'period' => $period,
                    'judul' => 'Ringkasan operasional periode ' . $judul,
                    'informasi' => $this->Notifikasis->kontenNotifikasiPetugas($period, $belumBayar, $belumSetor),
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
