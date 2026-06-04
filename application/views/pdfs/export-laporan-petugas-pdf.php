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

  <h1>LAPORAN BANK SAMPAH</h1>
  <h2>GO SARI Lestari per <?= date('d/m/Y') ?></h2>

  <h3>1. Sebaran Wilayah yang Belum Pilah Sampah</h3>

  <table>
    <thead>
      <tr>
        <th width="8%">No</th>
        <th>Wilayah</th>
        <th width="20%">Setoran Sampah</th>
        <th width="20%">Belum Pilah</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($belum_pilah as $idx => $item): ?>
        <tr>
          <td class="center"><?= $idx + 1 ?></td>
          <td><?= $item->wilayah ?></td>
          <td class="center"><?= number_format($item->jumlahKK, 0) ?></td>
          <td class="center"><?= number_format($item->belum_pilah, 0) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h3>2. Jumlah Sampah Terkelola</h3>

  <table>
    <thead>
      <tr>
        <th width="8%">No</th>
        <th>Wilayah</th>
        <th width="30%">Jumlah Sampah</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      foreach ($sampah_terkelola as $idx => $item):
        $total += $item->berat;
      ?>
        <tr>
          <td class="center"><?= $idx + 1 ?></td>
          <td><?= $item->wilayah ?></td>
          <td class="right"><?= number_format($item->berat, 2) ?> Kg</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th class="right" colspan="2">Total</th>
        <th class="right"><?= number_format($total, 2) ?> Kg</th>
      </tr>
    </tfoot>
  </table>

  <h3>3. Jenis Sampah yang Masuk</h3>

  <table>
    <thead>
      <tr>
        <th width="8%">No</th>
        <th>Jenis Sampah</th>
        <th width="30%">Berat</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($jenis_sampah as $idx => $item): ?>
        <tr>
          <td class="center"><?= $idx + 1 ?></td>
          <td><?= $item->kategori ?></td>
          <td class="right"><?= number_format($item->berat, 2) ?> Kg</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h3>4. Penghasilan Warga dari Penjualan Sampah yang Dipilah</h3>

  <table>
    <thead>
      <tr>
        <th width="8%">No</th>
        <th>Nama Warga</th>
        <th width="30%">Penghasilan</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      foreach ($penghasilan_warga as $idx => $item):
        $total += $item->penghasilan;
      ?>
        <tr>
          <td class="center"><?= $idx + 1 ?></td>
          <td><?= $item->nama ?></td>
          <td class="right">Rp <?= number_format($item->penghasilan, 0) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="2" class="right">Total</th>
        <th class="right">Rp <?= number_format($total, 0) ?></th>
      </tr>
    </tfoot>
  </table>

</body>

</html>