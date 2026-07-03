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
    $this
      ->db
      ->order_by("{$this->table}.createdAt", 'DESC')
      ->order_by("{$this->table}.isRead", 'ASC');

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
    return $notif;
  }

  function reminderBulananPetugas()
  {
    $periode = date('F Y');
    $judul = "Pengingat bulan {$periode}";
    $informasis = [];

    // warga belum bayar tagihan
    $this->load->model('Wargas');
    $wargas = $this->Wargas->getSaldoMinus();
    $countWarga = count($wargas);
    if (0 < $countWarga) {
      $namas = array_map(function ($warga) {
        return $warga->nama;
      }, $wargas);
      $namas = implode(', ', $namas);
      $informasis[] = "<b>Berikut nama {$countWarga} warga yg belum membayar tagihan bulan lalu:</b> {$namas}";
    }

    // warga setor sampah tak tepilah
    $this->load->model('SetorSampahs');
    $wargas = $this->SetorSampahs->wargaSetorSampahTakTerpilahBulanLalu();
    $countWarga = count($wargas);
    if (0 < $countWarga) {
      $namas = array_map(function ($warga) {
        return $warga->nama;
      }, $wargas);
      $namas = implode(', ', $namas);
      $informasis[] = "<b>Berikut nama {$countWarga} warga yg belum memilah sampah bulan lalu:</b> {$namas}";
    }

    $informasi = 0 < count($informasis) ?
      implode("<br><br>", $informasis) :
      'Tidak ada warga yg perlu ditindaklanjuti, semua warga sudah memilah sampah & membayar tagihan';

    $petugas = $this
      ->db
      ->query("
				SELECT
					u.uuid
				FROM user u
				LEFT JOIN role r ON u.role = r.uuid
				LEFT JOIN notifikasi n ON n.user = u.uuid
					AND n.judul = '{$judul}'
				WHERE r.name = 'Petugas'
					AND n.uuid IS NULL
			")
      ->result();

    foreach ($petugas as $ptgs) {
      $this->create([
        'user' => $ptgs->uuid,
        'judul' => $judul,
        'informasi' => $informasi
      ]);
    }
  }

  function permohonanAktivasi($wargaUuid)
  {
    $this->load->model('Users');
    $warga = $this->Users->findOne($wargaUuid);
    $admins = $this->Users->getAdmins();
    $wargaUrl = site_url("Warga/Read/{$wargaUuid}");
    foreach ($admins as $admin) {
      $this->create([
        'user' => $admin->uuid,
        'judul' => 'Permohonan aktivasi warga baru - ' . $warga['nama'],
        'informasi' => "Silakan klik link berikut untuk melihat detail permohonan warga baru: <u><a href='{$wargaUrl}'>{$warga['nama']}</a></u>"
      ]);
    }
  }

  function informasiBaru($info)
  {
    $this->load->model('Wargas');
    $href = site_url("Informasi/Read/{$info['uuid']}");
    $wargas = $this->Wargas->getAllUuid();
    foreach ($wargas as $warga) {
      $this->Notifikasis->create([
        'user' => $warga->uuid,
        'judul' => "Informasi Baru: {$info['title']}",
        'informasi' => "Informasi terbaru, silakan klik link berikut: <u><a href='{$href}'>{$info['title']}</a></u>"
      ]);
    }
  }

  function perubahanSaldo($ledger)
  {
    $operasi = 'bertambah';
    if (0 > $ledger['nilai']) {
      $operasi = 'berkurang';
      $ledger['nilai'] *= -1;
    }
    $nilai = 'Rp ' . number_format($ledger['nilai'], 0, ',', '.');

    return $this->create([
      'user' => $ledger['warga'],
      'judul' => "Saldo {$operasi} {$nilai}",
      'informasi' => "{$ledger['keterangan']} berhasil, saldo anda {$operasi} sebesar {$nilai}"
    ]);
  }
}
