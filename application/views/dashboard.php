<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl font-bold text-slate-800 mb-1">Selamat datang kembali, Pak Ahmad 👋</h1>
    <p class="text-slate-500 text-sm">Berikut ringkasan aktivitas Bank Sampah Kel. Sukamaju Minggu ke-3 Mei 2026.</p>
  </div>
  <div class="flex gap-3">
    <a href="<?= site_url('ExportImport/ExportLaporan') ?>" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 flex items-center gap-2 shadow-sm">
      <i class="fa-solid fa-download"></i> Export Laporan
    </a>
    <button class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 flex items-center gap-2 shadow-sm">
      <i class="fa-solid fa-plus"></i> Tambah Warga
    </button>
  </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
  <!-- Stat 1 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-users"></i>
      </div>
      <span class="flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> +3 minggu ini
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Total Warga Aktif</div>
    <div class="text-2xl font-bold text-slate-800">92</div>
  </div>

  <!-- Stat 2 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-wallet"></i>
      </div>
      <span class="flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> +8.2%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Saldo Beredar</div>
    <div class="text-2xl font-bold text-slate-800">Rp 452.700</div>
  </div>

  <!-- Stat 3 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-scale-balanced"></i>
      </div>
      <span class="flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> +12.4%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Setoran Bulan Ini</div>
    <div class="text-2xl font-bold text-slate-800">Rp 48.750</div>
  </div>

  <!-- Stat 4 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-basket-shopping"></i>
      </div>
      <span class="flex items-center gap-1 text-xs font-semibold text-red-500 bg-red-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-down"></i> -3.1%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Tukar Produk</div>
    <div class="text-2xl font-bold text-slate-800">Rp 89.000</div>
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
      <button class="text-sm text-brand-600 font-medium hover:text-brand-700">Detail &gt;</button>
    </div>

    <!-- Dummy Chart Representation -->
    <div class="h-64 flex items-end justify-between gap-2 pb-6 border-b border-slate-100 relative">
      <!-- Y Axis Labels -->
      <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-slate-400 pb-6">
        <span>100kg</span>
        <span>75kg</span>
        <span>50kg</span>
        <span>25kg</span>
      </div>

      <!-- Bars (Just visual representation) -->
      <div class="w-full flex justify-around pl-10 items-end h-full">
        <div class="w-8 bg-blue-400 rounded-t-sm h-[40%]"></div>
        <div class="w-8 bg-emerald-400 rounded-t-sm h-[60%]"></div>
        <div class="w-8 bg-amber-400 rounded-t-sm h-[30%]"></div>
        <div class="w-8 bg-purple-400 rounded-t-sm h-[80%]"></div>
        <div class="w-8 bg-rose-400 rounded-t-sm h-[50%]"></div>
        <div class="w-8 bg-blue-400 rounded-t-sm h-[90%]"></div>
        <div class="w-8 bg-emerald-400 rounded-t-sm h-[70%]"></div>
      </div>
    </div>
    <div class="flex justify-around pl-10 pt-3 text-xs text-slate-500 font-medium">
      <span>Sen</span>
      <span>Sel</span>
      <span>Rab</span>
      <span>Kam</span>
      <span>Jum</span>
      <span>Sab</span>
      <span>Min</span>
    </div>
    <div class="flex justify-center gap-4 mt-6 text-xs text-slate-600">
      <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-400"></span> Plastik</span>
      <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-400"></span> Kertas</span>
      <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400"></span> Logam</span>
      <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-purple-400"></span> Kaca</span>
      <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-rose-400"></span> Minyak</span>
    </div>
  </div>

  <!-- Right Side Widgets -->
  <div class="flex flex-col gap-6">

    <!-- Target Widget -->
    <div class="bg-brand-600 p-6 rounded-xl text-white shadow-md relative overflow-hidden">
      <i class="fa-solid fa-leaf absolute -right-4 -bottom-4 text-7xl text-brand-500 opacity-50"></i>
      <h3 class="text-brand-100 text-sm font-medium mb-1">Bulan Mei 2026</h3>
      <div class="text-xl font-bold mb-4">Sampah terkumpul</div>
      <div class="text-4xl font-black mb-2">1.247 <span class="text-lg font-medium">kg</span></div>

      <div class="w-full bg-brand-800 rounded-full h-2 mb-2">
        <div class="bg-white h-2 rounded-full" style="width: 62%"></div>
      </div>
      <div class="text-xs text-brand-100 flex justify-between">
        <span>62% dari target</span>
        <span>Target 2.000 kg</span>
      </div>
    </div>

    <!-- Top Kategori Widget -->
    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm flex-1">
      <h2 class="text-lg font-bold text-slate-800 mb-1">Top Kategori</h2>
      <p class="text-sm text-slate-500 mb-4">Berdasarkan volume bulan ini</p>

      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">#1</div>
            <span class="text-sm font-semibold text-slate-700">Plastik</span>
          </div>
          <span class="text-sm font-bold text-slate-800">420 kg</span>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">#2</div>
            <span class="text-sm font-semibold text-slate-700">Kertas</span>
          </div>
          <span class="text-sm font-bold text-slate-800">285 kg</span>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">#3</div>
            <span class="text-sm font-semibold text-slate-700">Kaca</span>
          </div>
          <span class="text-sm font-bold text-slate-800">166 kg</span>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">#4</div>
            <span class="text-sm font-semibold text-slate-700">Logam</span>
          </div>
          <span class="text-sm font-bold text-slate-800">98 kg</span>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">#5</div>
            <span class="text-sm font-semibold text-slate-700">Minyak Jelantah</span>
          </div>
          <span class="text-sm font-bold text-slate-800">37 L</span>
        </div>
      </div>
    </div>

  </div>
</div>