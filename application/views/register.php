<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bank Sampah — Daftar</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/webfonts/all.min.css') ?>">
  <script src="<?= base_url('assets/js/tailwindcss.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/tailwind.config.js') ?>"></script>
</head>

<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4 text-slate-800">
  <div class="w-full max-w-lg">
    <div class="text-center mb-8">
      <div class="inline-flex items-center gap-2 text-brand-600 mb-2">
        <i class="fa-solid fa-leaf text-3xl"></i>
        <span class="font-bold text-2xl text-slate-800">GO SARI Lestari</span>
      </div>
      <p class="text-sm text-slate-500">Aplikasi Pengelolaan Sampah Mandiri</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 md:p-8">

      <?php if (!empty($error)) : ?>
        <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif ?>

      <form method="post" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
          <input type="text" name="nama" required value="<?= htmlspecialchars($old['nama']) ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
          <input type="text" name="alamat" required value="<?= htmlspecialchars($old['alamat']) ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">RT/RW</label>
          <select name="rtrw" required class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none bg-white">
            <option value="">— Pilih RT/RW —</option>
            <?php foreach ($rtrws as $r) : ?>
              <option value="<?= $r->uuid ?>" <?= $old['rtrw'] === $r->uuid ? 'selected' : '' ?>><?= htmlspecialchars($r->nama) ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Kontak</label>
          <input type="text" name="kontak" required value="<?= htmlspecialchars($old['kontak']) ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none" placeholder="No. HP / WhatsApp">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
          <input type="text" name="username" required value="<?= htmlspecialchars($old['username']) ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
          <input type="password" name="password" required class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
          <input type="password" name="confirm_password" required class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
        <button type="submit" class="w-full py-2.5 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors">
          Daftar
        </button>
      </form>

      <p class="text-center text-sm text-slate-500 mt-6">
        Sudah punya akun?
        <a href="<?= site_url('Login') ?>" class="text-brand-600 font-medium hover:text-brand-700">Masuk di sini</a>
      </p>
    </div>
  </div>
</body>

</html>