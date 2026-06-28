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
      (object) ['mData' => 'judul', 'sTitle' => 'INFORMASI'],
      (object) ['mData' => 'dibaca', 'sTitle' => 'STATUS'],
      (object) ['mData' => 'aksi', 'sTitle' => 'BACA'],
    ];

    $this->form = [
      [
        'name' => 'informasi',
        'label' => 'Informasi',
        'type' => 'textarea',
        'attributes' => [
          ['rows' => 7]
        ]
      ],
    ];
  }

  public function dt()
  {

    $controller = $this->router->class;
    $edit = site_url("{$controller}/Read/");

    $this
      ->db
      ->select("CONCAT(
                '<a class=\"mr-1 border p-1 rounded-sm\" href=\"{$edit}', {$this->table}.uuid, '\"><i class=\"fa fa-envelope-open text-yellow-500\"></i></a>'
            ) as aksi", false);
    $this
      ->datatables
      ->select("{$this->table}.uuid")
      ->select("{$this->table}.orders")
      ->select("{$this->table}.judul")
      ->select("IF(isRead = 1, 'Sudah Dibaca', 'Belum Dibaca') as dibaca", false)
      ->where('user', $this->session->userdata('uuid'));

    return $this
      ->datatables
      ->from($this->table)
      ->where("{$this->table}.deletedAt", null)
      ->generate();
  }

  public function getUnreadCountByUserId($userUuid)
  {
    return $this
      ->db
      ->where('deletedAt', null)
      ->where('status', 1)
      ->where('user', $userUuid)
      ->where('isRead', 0)
      ->count_all_results($this->table);
  }

  public function read($uuid)
  {
    $notif = $this->findOne($uuid);
    $notif['isRead'] = 1;
    $this->update($notif);
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
  public function getWargaBelumBayar()
  {
    $this->load->model('Wargas');
    $role = $this->Wargas->getRoleWarga();
    return $this
      ->db
      ->select("uuid AS warga_uuid", false)
      ->select("nama AS warga_nama", false)
      ->select("-1 * saldo AS total_tagihan", false)
      ->select("0 AS total_bayar", false)
      ->select("-1 * saldo AS sisa_tagihan", false)
      ->where('status', 1)
      ->where('deletedAt', null)
      ->where('role', $role)
      ->where('saldo <', '0', false)
      ->get('user')
      ->result();
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
   * Menyusun pesan teks informasi untuk notifikasi ringkasan petugas.
   * 
   * @param string $period Periode (MM-YYYY).
   * @param array $belumBayar Daftar warga belum bayar.
   * @return string Pesan teks terformat.
   * 
   * Sample Output: "Periode 05-2024: 2 warga belum bayar (Ani, Budi); 1 warga belum setor sampah (Iwan). Silakan tindak lanjuti melalui dashboard petugas."
   */
  function kontenNotifikasiPetugas($period, $belumBayar)
  {
    $jumlahBelumBayar = count($belumBayar);

    if ($jumlahBelumBayar === 0) {
      return "Periode {$period}: tidak ada warga yang perlu ditindaklanjuti.\nSemua warga terdata tanpa tunggakan dan tidak ada daftar warga yang belum setor sampah pada periode ini.";
    }

    $namaBelumBayar = [];
    foreach (array_slice($belumBayar, 0, 10) as $item) {
      $namaBelumBayar[] = $item->warga_nama;
    }

    $textBelumBayar = $jumlahBelumBayar > 0
      ? $jumlahBelumBayar . ' warga belum bayar' . (count($namaBelumBayar) ? ' (' . implode(', ', $namaBelumBayar) . ')' : '')
      : 'tidak ada warga yang menunggak';

    return "Periode {$period}:\n{$textBelumBayar};\nSilakan tindak lanjuti melalui dashboard petugas.";
  }
}
