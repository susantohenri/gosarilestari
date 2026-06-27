<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= !empty($page_title) ? htmlspecialchars($page_title) . ' — ' : '' ?>GO SARI Lestari</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">

  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <meta name="theme-color" content="#16a34a">
  <link rel="apple-touch-icon" href="<?= base_url('icon-192x192.png') ?>">

  <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/app-overrides.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/webfonts/all.min.css') ?>">

  <script src="<?= base_url('assets/js/tailwindcss.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/tailwind.config.js') ?>"></script>

</head>

<body class="bg-slate-50 flex h-screen overflow-y-auto text-slate-800">

  <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/40 z-30 md:hidden"></div>

  <aside id="sidebar" class="overflow-y-auto w-64 bg-white border-r border-slate-200 flex flex-col z-40 -translate-x-full md:translate-x-0 md:relative md:flex shrink-0">
    <div class="h-16 flex items-center px-6 border-b border-slate-200">
      <a href="<?= base_url() ?>" class="flex items-center gap-2 text-brand-600">
        <i class="fa-solid fa-leaf text-xl"></i>
        <span class="font-bold text-lg text-slate-800">GO SARI Lestari</span>
      </a>
    </div>

    <div class="flex-1 overflow-y-auto py-4">
      <?php
      $menuFile = 'warga';
      $role_name = $this->session->userdata('role_name');
      if ($role_name === 'Admin') {
        $menuFile = 'superadmin';
      } elseif ($role_name === 'Petugas') {
        $menuFile = 'petugas';
      }
      include "menus/{$menuFile}.php";
      ?>
    </div>

    <div class="mt-auto py-10 px-2 hidden md:block">
      <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-blue-700 p-4 text-white shadow-soft">
        <p class="text-xs text-white/80">Sampah terkumpul</p>
        <p class="mt-1 text-3xl font-extrabold tracking-tight"><?= $sampah_terkumpul['berat'] ?> kg</p>
        <p class="text-xs text-white/80">Bulan <?= $sampah_terkumpul['bulan_tahun'] ?></p>
        <div class="mt-4 h-2 rounded-full bg-white/20">
          <div class="h-2 w-[62%] rounded-full bg-white"></div>
        </div>
        <p class="mt-2 text-[11px] text-white/85"><?= $sampah_terkumpul['persen'] ?>% target <?= $sampah_terkumpul['target'] ?> kg</p>
      </div>
    </div>

    <div class="p-4 border-t border-slate-200">
      <a href="<?= site_url('Login/Logout') ?>" class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
        <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
        <span>Keluar</span>
      </a>
    </div>
  </aside>

  <main class="flex-1 flex flex-col h-screen overflow-hidden relative min-w-0">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 z-10 shrink-0">
      <div class="flex items-center gap-3 flex-1 min-w-0">
        <button id="menu-toggle" type="button" class="md:hidden w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-colors shrink-0" aria-label="Toggle menu">
          <i class="fa-solid fa-bars"></i>
        </button>
        <form action="<?= site_url('Ledger') ?>" method="GET" class="relative w-full max-w-md block pr-5">
          <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/select2.min.css') ?>">
          <select name="search" class="w-full pl-10 pr-4 py-2 bg-slate-100 border-none rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none transition-shadow"></select>
        </form>
      </div>
      <div class="flex items-center gap-3 md:gap-4 shrink-0">
        <a href="<?= site_url('Notifikasi') ?>" class="relative w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-colors" title="Notifikasi">
          <i class="fa-regular fa-bell"></i>
          <?php if (0 < $unread): ?>
            <span class="absolute top-2 right-2 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
            </span>
          <?php endif; ?>
        </a>
        <div class="flex items-center gap-3 pl-3 md:pl-4 border-l border-slate-200">
          <div class="text-right hidden sm:block">
            <div class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($this->session->userdata('nama')) ?></div>
            <div class="text-xs text-slate-500"><?= htmlspecialchars($role_name) ?></div>
          </div>
          <?php
          $nama = trim($this->session->userdata('nama'));
          $words = preg_split('/\s+/', $nama);

          $initials = strtoupper(
            mb_substr($words[0] ?? '', 0, 1) .
              (count($words) > 1 ? mb_substr(end($words), 0, 1) : '')
          );
          ?>
          <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm">
            <?= htmlspecialchars($initials) ?>
          </div>
        </div>
      </div>
    </header>

    <div class="flex-1 overflow-y-auto p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
          <h1 id="page_title" class="text-2xl font-bold text-slate-800 mb-1"><?= $page_title ?></h1>
          <p class="text-slate-500 text-sm"><?= $page_subtitle ?></p>
        </div>
        <div class="flex gap-3">
          <?php if (isset($header_buttons)) include "{$header_buttons}.php" ?>
        </div>
      </div>
      <?php include "{$page_name}.php" ?>
    </div>
  </main>

  <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
  <script type="text/javascript">
    var site_url = '<?= site_url('/') ?>'
    var current_controller = '<?= $current['controller'] ?>'
    var current_controller_url = '<?= site_url($current['controller']) ?>'
  </script>
  <?php if (isset($js)) : foreach ($js as $script) : ?>
      <script type="text/javascript" src="<?= base_url("assets/js/{$script}") ?>"></script>
  <?php endforeach;
  endif; ?>
  <script>
    if ('service-worker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
          .then(reg => console.log('Service Worker terdaftar!', reg))
          .catch(err => console.error('Gagal daftar Service Worker:', err));
      });
    }
  </script>
  <script src="<?= base_url('assets/js/shell.js') ?>"></script>
</body>

</html>