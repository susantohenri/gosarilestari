<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>&nbsp;</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Courier New', 'Lucida Console', monospace;
      background: #e6e6e6;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    /* Gaya struk utama */
    .receipt {
      max-width: 320px;
      width: 100%;
      background: white;
      padding: 20px 16px 24px;
      border-radius: 4px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      transition: all 0.2s ease;
    }

    /* Header dengan logo / judul */
    .receipt-header {
      text-align: center;
      border-bottom: 1px dashed #aaa;
      padding-bottom: 12px;
      margin-bottom: 16px;
    }

    .store-name {
      font-size: 18px;
      font-weight: bold;
      letter-spacing: 2px;
      margin-bottom: 4px;
      color: #2c5a2e;
      text-transform: uppercase;
    }

    .store-sub {
      font-size: 10px;
      color: #5a5a5a;
      margin-bottom: 6px;
    }

    .divider-dash {
      border-top: 1px dashed #ccc;
      margin: 12px 0;
    }

    /* Info petugas & tanggal */
    .info-row {
      display: flex;
      justify-content: space-between;
      font-size: 11px;
      margin-bottom: 6px;
    }

    .info-label {
      font-weight: bold;
    }

    /* Body struk */
    .receipt-body {
      font-size: 12px;
      margin-bottom: 18px;
    }

    .trans-detail {
      background: #f9f9f2;
      padding: 12px 10px;
      border-left: 3px solid #2c5a2e;
      margin: 12px 0;
    }

    .detail-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 12px;
    }

    .detail-item:last-child {
      margin-bottom: 0;
    }

    .label {
      font-weight: 600;
      color: #2c3e2b;
    }

    .value {
      font-weight: 500;
      text-align: right;
      word-break: break-word;
      max-width: 60%;
    }

    .nilai-highlight {
      font-size: 16px;
      font-weight: bold;
      color: #1f5e23;
      background: #e9f3e6;
      padding: 4px 8px;
      border-radius: 4px;
      display: inline-block;
      margin-top: 4px;
    }

    /* Footer struk */
    .receipt-footer {
      text-align: center;
      border-top: 1px dashed #aaa;
      padding-top: 16px;
      margin-top: 8px;
    }

    .thankyou {
      font-size: 12px;
      font-weight: bold;
      color: #2c5a2e;
      margin-bottom: 8px;
      letter-spacing: 1px;
    }

    .footer-meta {
      font-size: 9px;
      color: #777;
    }

    /* pesan tambahan */
    .small-note {
      font-size: 9px;
      text-align: center;
      color: #888;
      margin-top: 12px;
    }

    /* efek tombol print (opsional, hanya untuk layar) */
    .print-btn {
      display: block;
      width: 100%;
      margin-top: 20px;
      padding: 8px;
      background: #2c5a2e;
      color: white;
      font-family: monospace;
      font-size: 14px;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      text-align: center;
      transition: 0.2s;
    }

    .print-btn:hover {
      background: #1d3e1f;
    }

    /* aturan cetak */
    @media print {
      body {
        background: white;
        padding: 0;
        margin: 0;
        display: block;
      }

      .receipt {
        max-width: 100%;
        box-shadow: none;
        padding: 8px 10px;
        margin: 0 auto;
        border-radius: 0;
      }

      .print-btn {
        display: none;
      }

      .receipt-header,
      .receipt-body,
      .receipt-footer {
        page-break-inside: avoid;
      }

      .nilai-highlight {
        background: #f1f1f1;
        border: 0.5px solid #ccc;
      }
    }
  </style>
</head>

<body>
  <div class="receipt">
    <!-- Header struk dengan gaya minimarket -->
    <div class="receipt-header">
      <div class="store-name">GO SARI Lestari</div>
      <div class="store-sub">Struk Transaksi Bank Sampah</div>
      <div class="divider-dash"></div>
      <div class="info-row">
        <span class="info-label">Petugas:</span>
        <span><?= htmlspecialchars($fpetugas) ?></span>
      </div>
      <div class="info-row">
        <span class="info-label">Tanggal:</span>
        <span><?= htmlspecialchars($fwaktu) ?></span>
      </div>
    </div>

    <!-- Body struk : detail transaksi -->
    <div class="receipt-body">
      <div class="info-row" style="margin-bottom: 6px;">
        <span class="info-label">Jenis Transaksi:</span>
        <strong><?= htmlspecialchars($tipe) ?></strong>
      </div>

      <div class="trans-detail">
        <div class="detail-item">
          <span class="label">ID Nasabah</span>
          <span class="value"><?= htmlspecialchars($warga_kode) ?></span>
        </div>
        <div class="detail-item">
          <span class="label">Nama Nasabah</span>
          <span class="value"><?= htmlspecialchars($fwarga) ?></span>
        </div>
        <div class="detail-item">
          <span class="label">Kode Transaksi</span>
          <span class="value"><?= htmlspecialchars($kode) ?></span>
        </div>
        <div class="detail-item" style="margin-top: 6px; border-top: 1px dotted #ddd; padding-top: 6px;">
          <span class="label">NILAI</span>
          <span class="value nilai-highlight"><?= htmlspecialchars($fnilai) ?></span>
        </div>
      </div>
    </div>

    <!-- Footer : ucapan terima kasih -->
    <div class="receipt-footer">
      <div class="thankyou">★ TERIMA KASIH ★</div>
      <div class="small-note">Simpan struk ini sebagai bukti transaksi</div>
    </div>
  </div>

  <!-- Tombol cetak manual (optional, untuk mempermudah pengguna) -->
  <button class="print-btn" onclick="window.print();">🖨️ Cetak Struk</button>

  <script>
    // Cetak otomatis saat halaman dimuat (jika diperlukan)
    window.onload = function() {
      // sedikit jeda agar konten benar-benar siap, lalu print
      setTimeout(() => {
        window.print();
      }, 200);
    };
  </script>
</body>

</html>