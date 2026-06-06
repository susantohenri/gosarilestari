<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Laporan Pengelolaan Sampah</title>

  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 11px;
      margin: 20px;
    }

    h1 {
      text-align: center;
      margin-bottom: 5px;
    }

    h2 {
      text-align: center;
      font-size: 14px;
      margin-top: 0;
      margin-bottom: 25px;
    }

    h3 {
      margin-top: 25px;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 6px;
    }

    th {
      background-color: #eeeeee;
      text-align: center;
    }

    .center {
      text-align: center;
    }

    .right {
      text-align: right;
    }
  </style>
</head>

<body>

  <h1>TRANSAKSI BANK SAMPAH</h1>
  <h2>
    GO SARI Lestari per <?= date('d/m/Y') ?>
    <?= isset($warga) ? "<br>{$warga['nama']}, {$warga['rtrw']}" : '' ?>
  </h2>

  <table>
    <thead>
      <tr>
        <th width="8%">Kode</th>
        <th>Warga</th>
        <th>Tipe</th>
        <th>Keterangan</th>
        <th>Petugas</th>
        <th>Waktu</th>
        <th width="12%">Nilai</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= $row->kode ?></td>
          <td><?= $row->fwarga ?></td>
          <td><?= $row->tipe ?></td>
          <td><?= $row->keterangan ?></td>
          <td><?= $row->fpetugas ?></td>
          <td><?= $row->fwaktu ?></td>
          <td class="right"><?= $row->fnilai ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>

</html>