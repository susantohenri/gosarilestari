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
  <h2>
    GO SARI Lestari per <?= date('d/m/Y') ?><br>
    <?= "{$warga['nama']}, {$warga['rtrw']}" ?>
  </h2>

  <h3>1. Sampah yang dihasilkan berdasarkan jenis</h3>

  <table>
    <thead>
      <tr>
        <th width="8%">No</th>
        <th>Jenis Sampah</th>
        <th width="20%">Berat (Kg)</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      foreach ($berat_kategori_sampah as $idx => $item):
        $total += $item->berat;
      ?>
        <tr>
          <td class="center"><?= $idx + 1 ?></td>
          <td><?= $item->nama ?></td>
          <td class="right"><?= number_format($item->berat, 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th class="right" colspan="2">Total</th>
        <th class="right"><?= number_format($total, 2) ?></th>
      </tr>
    </tfoot>
  </table>

  <h3>2. Penghasilan yang diperoleh dari pilah sampah</h3>

  <table>
    <thead>
      <tr>
        <th width="8%">No</th>
        <th>Jenis Sampah</th>
        <th width="20%">Pendapatan (Rp)</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      foreach ($pendapatan_kategori_sampah as $idx => $item):
        $total += $item->pendapatan;
      ?>
        <tr>
          <td class="center"><?= $idx + 1 ?></td>
          <td><?= $item->nama ?></td>
          <td class="right"><?= number_format($item->pendapatan, 0) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th class="right" colspan="2">Total</th>
        <th class="right"><?= number_format($total, 0) ?></th>
      </tr>
    </tfoot>
  </table>

  <h3>3. Iuran yang telah dilakukan</h3>

  <table>
    <thead>
      <tr>
        <th width="8%">No</th>
        <th>Bulan</th>
        <th width="20%">Iuran (Rp)</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      foreach ($iuran as $idx => $item):
        $total += $item->nominal;
      ?>
        <tr>
          <td class="center"><?= $idx + 1 ?></td>
          <td><?= $item->bulan ?></td>
          <td class="right"><?= number_format($item->nominal, 0) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th class="right" colspan="2">Total</th>
        <th class="right"><?= number_format($total, 0) ?></th>
      </tr>
    </tfoot>
  </table>

</body>

</html>