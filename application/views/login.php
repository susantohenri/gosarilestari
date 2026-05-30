<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bank Sampah — Masuk</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
  <script src="<?= base_url('assets/js/tailwindcss.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/tailwind.config.js') ?>"></script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4 text-slate-800">
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="inline-flex items-center gap-2 text-brand-600 mb-2">
        <i class="fa-solid fa-leaf text-3xl"></i>
        <span class="font-bold text-2xl text-slate-800">Bank Sampah</span>
      </div>
      <p class="text-sm text-slate-500">gosarilestari.com</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 md:p-8">
      <h1 class="text-lg font-bold text-slate-800 mb-1">Masuk</h1>
      <p class="text-sm text-slate-500 mb-6">Masuk ke akun Anda untuk melanjutkan.</p>

      <?php if ($this->session->flashdata('register_success')) : ?>
        <div class="mb-4 p-3 rounded-lg bg-brand-50 text-brand-800 text-sm border border-brand-100">
          <?= htmlspecialchars($this->session->flashdata('register_success')) ?>
        </div>
      <?php endif ?>

      <?php if (!empty($error)) : ?>
        <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif ?>

      <form method="post" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
          <div class="relative">
            <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="username" required class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none" placeholder="Username">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="password" name="password" required class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none" placeholder="Password">
          </div>
        </div>
        <button type="submit" class="w-full py-2.5 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors">
          Masuk
        </button>
      </form>

      <p class="text-center text-sm text-slate-500 mt-6">
        Belum punya akun?
        <a href="<?= site_url('Login/register') ?>" class="text-brand-600 font-medium hover:text-brand-700">Daftar di sini</a>
      </p>
    </div>
  </div>
</body>
</html>
