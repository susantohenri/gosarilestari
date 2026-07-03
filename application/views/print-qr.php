<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>&nbsp;</title>
  <script src="<?= base_url('assets/js/qrcode.min.js') ?>"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: #e5e5e5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      font-family: Arial, Helvetica, sans-serif;
    }

    .card {

      width: 320px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
      padding: 20px;
      text-align: center;

    }

    .logo {

      font-size: 22px;
      font-weight: bold;
      color: #2c5a2e;

    }

    .sub {

      font-size: 12px;
      color: #666;
      margin-top: 3px;

    }

    hr {

      border: none;
      border-top: 1px dashed #aaa;
      margin: 15px 0;

    }

    #qrcode {

      display: flex;
      justify-content: center;
      margin: 15px 0;

    }

    .nama {

      font-size: 20px;
      font-weight: bold;
      color: #333;

    }

    .kode {

      font-size: 16px;
      margin-top: 8px;
      color: #2c5a2e;
      font-weight: bold;
    }

    .alamat {

      margin-top: 10px;
      font-size: 12px;
      color: #666;
    }

    .note {

      margin-top: 15px;
      font-size: 11px;
      color: #888;
    }

    @media print {

      body {

        background: white;
        padding: 0;
        margin: 0;

      }

      .card {

        box-shadow: none;
        border-radius: 0;
        width: 100%;

      }

    }
  </style>
</head>

<body>
  <div class="card">

    <div class="logo">
      GO SARI LESTARI
    </div>

    <div class="sub">
      QR CODE WARGA
    </div>

    <hr>

    <div id="qrcode"></div>

    <div class="nama">
      <?= $warga['nama'] ?>
    </div>

    <div class="kode">
      <?= $warga['kode'] ?>
    </div>

    <div class="alamat">
      <?= $rtrw['nama'] ?>
    </div>

    <div class="note">
      Tempelkan QR ini di depan rumah.<br>
      Petugas cukup melakukan scan saat pengambilan sampah.
    </div>

  </div>

  <script>
    let isiQR = '<?= site_url("SetorSampah/create?warga={$warga['uuid']}") ?>';
    new QRCode(document.getElementById("qrcode"), {
      text: isiQR,
      width: 180,
      height: 180,
      correctLevel: QRCode.CorrectLevel.H
    });

    window.onload = function() {
      setTimeout(function() {
        window.print();
      }, 300);
    };
  </script>
</body>

</html>