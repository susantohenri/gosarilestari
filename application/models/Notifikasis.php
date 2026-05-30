<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notifikasis extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Konfigurasis');

    $this->table = 'notifikasi';

    $this->thead = [
      (object) ['mData' => 'orders', 'sTitle' => 'No', 'visible' => false],
      (object) ['mData' => 'judul', 'sTitle' => '&nbsp;'],
    ];

    $this->form = [
      [
        'name' => 'informasi',
        'label' => 'Notifikasi',
        'type' => 'textarea',
      ],
    ];
  }

  public function dt()
  {
    $this
      ->datatables
      ->select("{$this->table}.uuid")
      ->select("{$this->table}.orders")
      ->select("{$this->table}.judul")
      ->where('user', $this->session->userdata('uuid'));
    return parent::dt();
  }

  public function updateUserdataUnreadNotification($userUuid)
  {
    $unread = $this
      ->db
      ->where('deletedAt', null)
      ->where('status', 1)
      ->where('user', $userUuid)
      ->where('isRead', 0)
      ->count_all_results($this->table);
    $this->session->set_userdata('unread', $unread);
  }

  public function read($uuid)
  {
    $notif = $this->findOne($uuid);
    $notif['isRead'] = 1;
    $this->update($notif);
    $this->updateUserdataUnreadNotification($notif['user']);
  }

  /**
   * Mendapatkan daftar ringkasan data warga yang belum menerima notifikasi pada periode tertentu.
   * Digunakan untuk proses broadcast bulanan.
   * 
   * @param string $targetStart Awal bulan target (Y-m-d H:i:s).
   * @param string $currentStart Awal bulan berjalan (Y-m-d H:i:s).
   * @param string $bulan Angka bulan target.
   * @param string $tahun Angka tahun target.
   * @param string $period Format MM-YYYY.
   * @return array Daftar objek ringkasan warga.
   * 
   * Sample Output:
   * [
   *   {
   *     "user_uuid": "...", "nama_warga": "Budi", "total_berat": 10.5,
   *     "total_pendapatan": 25000, "total_tagihan": 15000, "total_bayar": 15000,
   *     "sisa_tagihan": 0, "kelebihan_bayar": 10000
   *   }
   * ]
   */
  public function getUnnotifiedWargaSummary($targetStart, $currentStart, $bulan, $tahun, $period)
  {
    $sql = "
            SELECT
              u.uuid AS user_uuid,
              u.nama AS nama_warga,
              COALESCE(s.total_berat, 0) AS total_berat,
              COALESCE(s.total_pendapatan, 0) AS total_pendapatan,
              COALESCE(s.total_tagihan, 0) AS total_tagihan,
              COALESCE(b.total_bayar, 0) AS total_bayar,
              GREATEST(COALESCE(s.total_tagihan, 0) - COALESCE(b.total_bayar, 0), 0) AS sisa_tagihan,
              GREATEST(COALESCE(b.total_bayar, 0) - COALESCE(s.total_tagihan, 0), 0) AS kelebihan_bayar
            FROM user u
            INNER JOIN role r ON r.uuid = u.role
            LEFT JOIN (
              SELECT
                ss.warga,
                SUM(ss.berat) AS total_berat,
                SUM(ss.pendapatan) AS total_pendapatan,
                SUM(ss.tagihan) AS total_tagihan
              FROM setorsampah ss
              WHERE ss.status = 1
                AND ss.deletedAt IS NULL
                AND ss.createdAt >= ?
                AND ss.createdAt < ?
              GROUP BY ss.warga
            ) s ON s.warga = u.uuid
            LEFT JOIN (
              SELECT
                st.warga,
                SUM(st.nominal) AS total_bayar
              FROM setortunai st
              WHERE st.status = 1
                AND st.deletedAt IS NULL
                AND st.bulan = ?
                AND st.tahun = ?
              GROUP BY st.warga
            ) b ON b.warga = u.uuid
            WHERE r.name = 'warga'
              AND u.status = 1
              AND u.deletedAt IS NULL
              AND NOT EXISTS (
                SELECT 1
                FROM notifikasi n
                WHERE n.user = u.uuid
                  AND n.jenis = 'RINGKASAN_WARGA'
                  AND n.period = ?
                  AND n.deletedAt IS NULL
              )
        ";

    return $this->db->query($sql, [$targetStart, $currentStart, $bulan, $tahun, $period])->result();
  }

  /**
   * Mendapatkan daftar petugas yang belum menerima notifikasi ringkasan operasional pada periode tertentu.
   * 
   * @param string $period Format MM-YYYY.
   * @return array Daftar objek petugas.
   * 
   * Sample Output:
   * [
   *   {
   *     "user_uuid": "...", "nama_petugas": "Agus"
   *   }
   * ]
   */
  public function getUnnotifiedPetugas($period)
  {
    $sql = "
            SELECT
              u.uuid AS user_uuid,
              u.nama AS nama_petugas
            FROM user u
            INNER JOIN role r ON r.uuid = u.role
            WHERE r.name = 'petugas'
              AND u.status = 1
              AND u.deletedAt IS NULL
              AND NOT EXISTS (
                SELECT 1
                FROM notifikasi n
                WHERE n.user = u.uuid
                  AND n.jenis = 'RINGKASAN_PETUGAS'
                  AND n.period = ?
                  AND n.deletedAt IS NULL
              )
        ";

    return $this->db->query($sql, [$period])->result();
  }

  /**
   * Mendapatkan daftar warga yang memiliki sisa tagihan (belum bayar lunas) pada periode tertentu.
   * 
   * @param string $targetStart Awal bulan target.
   * @param string $currentStart Awal bulan berjalan.
   * @param string $bulan Angka bulan target.
   * @param string $tahun Angka tahun target.
   * @return array Daftar objek warga belum bayar.
   * 
   * Sample Output:
   * [
   *   {
   *     "warga_uuid": "...", "warga_nama": "Ani",
   *     "total_tagihan": 20000, "total_bayar": 5000,
   *     "sisa_tagihan": 15000
   *   }
   * ]
   */
  public function getWargaBelumBayar($targetStart, $currentStart, $bulan, $tahun)
  {
    $sql = "
            SELECT
              x.warga_uuid,
              x.warga_nama,
              x.total_tagihan,
              x.total_bayar,
              x.sisa_tagihan
            FROM (
              SELECT
                u.uuid AS warga_uuid,
                u.nama AS warga_nama,
                COALESCE(s.total_tagihan, 0) AS total_tagihan,
                COALESCE(b.total_bayar, 0) AS total_bayar,
                GREATEST(COALESCE(s.total_tagihan, 0) - COALESCE(b.total_bayar, 0), 0) AS sisa_tagihan
              FROM user u
              INNER JOIN role r ON r.uuid = u.role
              LEFT JOIN (
                SELECT warga, SUM(tagihan) AS total_tagihan
                FROM setorsampah
                WHERE status = 1
                  AND deletedAt IS NULL
                  AND createdAt >= ?
                  AND createdAt < ?
                GROUP BY warga
              ) s ON s.warga = u.uuid
              LEFT JOIN (
                SELECT warga, SUM(nominal) AS total_bayar
                FROM setortunai
                WHERE status = 1
                  AND deletedAt IS NULL
                  AND bulan = ?
                  AND tahun = ?
                GROUP BY warga
              ) b ON b.warga = u.uuid
              WHERE r.name = 'warga'
                AND u.status = 1
                AND u.deletedAt IS NULL
            ) x
            WHERE x.sisa_tagihan > 0
            ORDER BY x.sisa_tagihan DESC, x.warga_nama ASC
        ";

    return $this->db->query($sql, [$targetStart, $currentStart, $bulan, $tahun])->result();
  }

  /**
   * Mendapatkan daftar warga yang sama sekali belum menyetorkan sampah pada periode waktu tertentu.
   * 
   * @param string $targetStart Awal range waktu.
   * @param string $currentStart Akhir range waktu.
   * @return array Daftar objek warga belum setor.
   * 
   * Sample Output:
   * [
   *   {
   *     "warga_uuid": "...", "warga_nama": "Iwan", "alamat": "Blok A", "kontak": "081..."
   *   }
   * ]
   */
  public function getWargaBelumSetor($targetStart, $currentStart)
  {
    $sql = "
            SELECT
              u.uuid AS warga_uuid,
              u.nama AS warga_nama,
              u.alamat,
              u.kontak
            FROM user u
            INNER JOIN role r ON r.uuid = u.role
            LEFT JOIN setorsampah ss
              ON ss.warga = u.uuid
             AND ss.status = 1
             AND ss.deletedAt IS NULL
             AND ss.createdAt >= ?
             AND ss.createdAt < ?
            WHERE r.name = 'warga'
              AND u.status = 1
              AND u.deletedAt IS NULL
              AND ss.uuid IS NULL
            ORDER BY u.nama ASC
        ";

    return $this->db->query($sql, [$targetStart, $currentStart])->result();
  }

  /**
   * Melakukan insert notifikasi secara massal dengan mengabaikan data duplikat (INSERT IGNORE).
   * 
   * @param array $rows Array of array data notifikasi.
   * @return int Jumlah baris yang berhasil diinsert.
   */
  public function bulkInsertIgnoreError($rows)
  {
    if (empty($rows)) {
      return 0;
    }

    $placeholders = [];
    $bindings = [];

    foreach ($rows as $row) {
      $placeholders[] = '(?, ?, ?, ?, ?, ?, ?, 0, 1, NOW(), NOW())';
      $bindings[] = $row['uuid'];
      $bindings[] = $row['kode'];
      $bindings[] = $row['user'];
      $bindings[] = $row['jenis'];
      $bindings[] = $row['period'];
      $bindings[] = $row['judul'];
      $bindings[] = $row['informasi'];
    }

    $sql = "
            INSERT IGNORE INTO notifikasi
            (
              uuid, kode, user, jenis, period, judul, informasi, isRead, status, createdAt, updatedAt
            ) VALUES " . implode(', ', $placeholders);

    $this->db->query($sql, $bindings);
    return $this->db->affected_rows();
  }

  /**
   * Membuat UUID v4 secara manual.
   * 
   * @return string String UUID.
   * 
   * Sample Output: "550e8400-e29b-41d4-a716-446655440000"
   */
  public function generateUuid()
  {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }

  /**
   * Menyusun pesan teks informasi untuk notifikasi ringkasan warga.
   * 
   * @param object $row Objek data ringkasan warga.
   * @param string $period Periode (MM-YYYY).
   * @return string Pesan teks terformat.
   * 
   * Sample Output: "Periode 05-2024: total sampah terpilah 10,00 kg, pendapatan Rp20.000, total tagihan Rp5.000, pembayaran Rp5.000, tidak ada sisa tagihan."
   */
  function kontenNotifikasiWarga($row, $period)
  {
    $totalBerat = number_format((float) $row->total_berat, 2, ',', '.');
    $pendapatan = 'Rp' . number_format((float) $row->total_pendapatan, 0, ',', '.');
    $tagihan = 'Rp' . number_format((float) $row->total_tagihan, 0, ',', '.');
    $bayar = 'Rp' . number_format((float) $row->total_bayar, 0, ',', '.');
    $sisa = 'Rp' . number_format((float) $row->sisa_tagihan, 0, ',', '.');
    $lebih = 'Rp' . number_format((float) $row->kelebihan_bayar, 0, ',', '.');

    if ((float) $row->total_berat <= 0 && (float) $row->total_tagihan <= 0) {
      return "Periode {$period}: belum ada data setor sampah, pendapatan Rp0, total tagihan Rp0, tidak ada sisa tagihan.";
    }

    if ((float) $row->kelebihan_bayar > 0) {
      return "Periode {$period}: total sampah terpilah {$totalBerat} kg, pendapatan {$pendapatan}, total tagihan {$tagihan}, pembayaran {$bayar}, tidak ada sisa tagihan, kelebihan bayar {$lebih}.";
    }

    if ((float) $row->sisa_tagihan > 0) {
      return "Periode {$period}: total sampah terpilah {$totalBerat} kg, pendapatan {$pendapatan}, total tagihan {$tagihan}, pembayaran {$bayar}, sisa tagihan {$sisa}.";
    }

    return "Periode {$period}: total sampah terpilah {$totalBerat} kg, pendapatan {$pendapatan}, total tagihan {$tagihan}, pembayaran {$bayar}, tidak ada sisa tagihan.";
  }

  /**
   * Menyusun pesan teks informasi untuk notifikasi ringkasan petugas.
   * 
   * @param string $period Periode (MM-YYYY).
   * @param array $belumBayar Daftar warga belum bayar.
   * @param array $belumSetor Daftar warga belum setor.
   * @return string Pesan teks terformat.
   * 
   * Sample Output: "Periode 05-2024: 2 warga belum bayar (Ani, Budi); 1 warga belum setor sampah (Iwan). Silakan tindak lanjuti melalui dashboard petugas."
   */
  function kontenNotifikasiPetugas($period, $belumBayar, $belumSetor)
  {
    $jumlahBelumBayar = count($belumBayar);
    $jumlahBelumSetor = count($belumSetor);

    if ($jumlahBelumBayar === 0 && $jumlahBelumSetor === 0) {
      return "Periode {$period}: tidak ada warga yang perlu ditindaklanjuti. Semua warga terdata tanpa tunggakan dan tidak ada daftar warga yang belum setor sampah pada periode ini.";
    }

    $namaBelumBayar = [];
    foreach (array_slice($belumBayar, 0, 10) as $item) {
      $namaBelumBayar[] = $item->warga_nama;
    }

    $namaBelumSetor = [];
    foreach (array_slice($belumSetor, 0, 10) as $item) {
      $namaBelumSetor[] = $item->warga_nama;
    }

    $textBelumBayar = $jumlahBelumBayar > 0
      ? $jumlahBelumBayar . ' warga belum bayar' . (count($namaBelumBayar) ? ' (' . implode(', ', $namaBelumBayar) . ')' : '')
      : 'tidak ada warga yang menunggak';

    $textBelumSetor = $jumlahBelumSetor > 0
      ? $jumlahBelumSetor . ' warga belum setor sampah' . (count($namaBelumSetor) ? ' (' . implode(', ', $namaBelumSetor) . ')' : '')
      : 'tidak ada warga yang belum setor sampah';

    return "Periode {$period}: {$textBelumBayar}; {$textBelumSetor}. Silakan tindak lanjuti melalui dashboard petugas.";
  }
}
