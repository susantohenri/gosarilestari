<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
  <!-- Stat 1 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-users"></i>
      </div>
      <span class="flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> +<?= $card_warga['mendaftar_minggu_ini'] ?> minggu ini
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Total Warga Aktif</div>
    <div class="text-2xl font-bold text-slate-800"><?= $card_warga['warga_aktif'] ?></div>
  </div>

  <!-- Stat 2 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-wallet"></i>
      </div>
      <?php $progressColor = 0 <= $card_saldo['progress'] ? 'green' : 'red'  ?>
      <span class="flex items-center gap-1 text-xs font-semibold text-<?= $progressColor ?>-600 bg-<?= $progressColor ?>-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> <?= number_format($card_saldo['progress'], 1) ?>%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Saldo Beredar</div>
    <div class="text-2xl font-bold text-slate-800">Rp <?= number_format($card_saldo['beredar'], 0, ',', '.') ?></div>
  </div>

  <!-- Stat 3 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-scale-balanced"></i>
      </div>
      <?php $progressColor = 0 <= $card_setoran['progress'] ? 'green' : 'red'  ?>
      <span class="flex items-center gap-1 text-xs font-semibold text-<?= $progressColor ?>-600 bg-<?= $progressColor ?>-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> <?= number_format($card_setoran['progress'], 1) ?>%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Setoran Bulan Ini</div>
    <div class="text-2xl font-bold text-slate-800">Rp <?= number_format($card_setoran['bulan_ini'], 0, ',', '.') ?></div>
  </div>

  <!-- Stat 4 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-basket-shopping"></i>
      </div>
      <?php $progressColor = 0 <= $card_tukar_produk['progress'] ? 'green' : 'red'  ?>
      <span class="flex items-center gap-1 text-xs font-semibold text-<?= $progressColor ?>-500 bg-<?= $progressColor ?>-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-down"></i> <?= number_format($card_tukar_produk['progress'], 1) ?>%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Tukar Produk</div>
    <div class="text-2xl font-bold text-slate-800">Rp <?= number_format($card_tukar_produk['bulan_ini'], 0, ',', '.') ?></div>
  </div>
</div>

<!-- Bottom Section (Charts & Info) -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 pb-6">

  <!-- Chart Area -->
  <div class="xl:col-span-2 bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-lg font-bold text-slate-800">Volume Sampah Tersetor</h2>
        <p class="text-sm text-slate-500">7 hari terakhir, dipecah per kategori</p>
      </div>
      <a href="<?= site_url('SetorSampah') ?>" class="text-sm text-brand-600 font-medium hover:text-brand-700">Detail &gt;</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div style="height: 300px; width: 100%; position: relative;">
      <canvas id="volumeChart" style="height: 100%; width: 100%;"></canvas>
    </div>
    <script>
      const rawData = <?= json_encode($card_grafik['items']) ?>;
      const ctx = document.getElementById('volumeChart').getContext('2d');

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: rawData.labels,
          datasets: rawData.datasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.dataset.label + ': ' + context.raw + ' kg';
                }
              }
            }
          },
          scales: {
            x: {
              stacked: true,
            },
            y: {
              stacked: true,
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return value + ' kg';
                }
              }
            }
          }
        }
      });
    </script>
  </div>

  <!-- Right Side Widgets -->
  <div class="flex flex-col gap-6">

    <!-- Top Kategori Widget -->
    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm flex-1">
      <h2 class="text-lg font-bold text-slate-800 mb-1">Top Kategori</h2>
      <p class="text-sm text-slate-500 mb-4">Berdasarkan volume bulan ini</p>

      <div class="space-y-4">
        <?php foreach ($card_kategori['items'] as $index => $item): ?>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">#<?= $index + 1 ?></div>
              <span class="text-sm font-semibold text-slate-700"><?= $item->nama_kategori ?></span>
            </div>
            <span class="text-sm font-bold text-slate-800"><?= number_format($item->total_berat, 1) ?> kg</span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>