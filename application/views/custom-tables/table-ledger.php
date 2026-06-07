<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/select2.min.css') ?>">

<?php if (0 < count($overview)): ?>
  <div class="my-6 grid gap-4 md:grid-cols-2 2xl:grid-cols-4">
    <?php foreach ($overview as $ov) : ?>
      <article class="rounded-2xl border border-line bg-white p-4 shadow-panel">
        <div class="flex items-start gap-3">
          <div class="bg-slate-100 flex h-12 w-12 items-center justify-center rounded-xl bg-brandSoft text-brand">
            <i class="fa-solid <?= $ov['icon'] ?> w-5 text-center"></i>
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-500"><?= $ov['label'] ?></p>
            <p class="text-3xl font-extrabold leading-none"><?= isset($ov['rp']) ? 'Rp ' : '' ?><?= number_format($ov['value'], 0, ',', '.') ?></p>
          </div>
        </div>
      </article>
    <?php endforeach ?>
  </div>
<?php endif ?>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="p-4 md:p-6">
    <div class="flex flex-col gap-3 border-b border-line p-4 lg:flex-row lg:items-center lg:justify-between">
      <form name="custom_table_filter" class="flex flex-1 flex-col gap-3 md:flex-row">
        <label class="flex items-center gap-3 rounded-xl border border-line bg-slate-50 px-4 py-3 md:w-[60%]">
          <span class="text-slate-400 fa-solid fa-magnifying-glass"></span>
          <input name="fnama" type="text" placeholder="Cari nama atau ID nasabah..." class="w-full bg-transparent text-sm text-slate-500 outline-none">
        </label>
        <div class="flex flex-1 flex-col gap-3 md:flex-row">

          <select class="form-control w-full" name="since" data-autocomplete="true" data-model="Rtrws" data-field="nama">
            <option value="">Semua Waktu</option>
            <option value="7">7 Hari</option>
            <option value="30">30 Hari</option>
            <option value="90">90 Hari</option>
          </select>
        </div>
        <div class="flex flex-1 flex-col gap-3 md:flex-row">
          <select name="tipe" class="rounded-xl border border-line bg-white px-4 py-3 text-sm text-slate-600 outline-none">
            <option value="">Semua Tipe</option>
            <option value="SETOR_SAMPAH">Setor Sampah</option>
            <option value="TUKAR_PRODUK">Tukar Produk</option>
            <option value="SETOR_TUNAI">Setor Tunai</option>
            <option value="POTONG_IURAN">Potong Iuran</option>
          </select>
        </div>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="datatable table-model w-full text-sm">
        <tfoot>
          <tr></tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<div id="modal-detail" class="hidden fixed inset-0 z-[60] bg-black/50 items-center justify-center p-4 backdrop-blur-sm transition-opacity">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[420px] overflow-hidden flex flex-col">

    <div class="p-5 pb-4 flex justify-between items-start">
      <div>
        <h2 class="text-lg font-bold text-slate-800 leading-tight">Detail Transaksi</h2>
        <p class="text-xs text-slate-500 font-medium mt-0.5" data-field="transaction-code"></p>
      </div>
      <button type="button" onclick="document.getElementById('modal-detail').classList.add('hidden'); document.getElementById('modal-detail').classList.remove('flex');" class="text-slate-400 hover:text-slate-700 transition-colors p-1">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <div class="mx-5 mb-5 bg-gradient-to-br from-[#e68a00] to-[#c77700] rounded-xl p-5 text-center text-white shadow-md">
      <div class="text-sm font-medium opacity-90 mb-1" data-field="transaction-saldo"></div>
      <div class="text-3xl font-bold tracking-tight mb-1" data-field="transaction-value"></div>
      <div class="text-sm font-medium opacity-90" data-field="transaction-keterangan"></div>
    </div>

    <div class="px-5 space-y-4 mb-6">
      <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-sm text-slate-500">Tipe Transaksi</span>
        <span class="text-sm font-semibold text-slate-700 flex items-center gap-2" data-field="transaction-type-display">
          <!-- <span class="w-2 h-2 rounded-full bg-amber-400"></span> -->
          <span data-field="transaction-type"></span>
        </span>
      </div>

      <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-sm text-slate-500">Warga</span>
        <span class="text-sm font-semibold text-slate-700 flex items-center gap-2" data-field="citizen-name"></span>
      </div>

      <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-sm text-slate-500">ID Nasabah</span>
        <span class="text-sm font-semibold text-slate-700" data-field="citizen-id"></span>
      </div>

      <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-sm text-slate-500">Petugas</span>
        <span class="text-sm font-semibold text-slate-700" data-field="officer-name"></span>
      </div>

      <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-sm text-slate-500">Waktu</span>
        <span class="text-sm font-semibold text-slate-700" data-field="transaction-time"></span>
      </div>

      <div class="flex justify-between items-center">
        <span class="text-sm text-slate-500">Kode Transaksi</span>
        <span class="text-sm font-semibold text-slate-700" data-field="transaction-code"></span>
      </div>
    </div>

    <div class="p-4 flex justify-end gap-3 border-t border-slate-100 bg-slate-50">
      <a target="_blank" id="print_button" class="flex items-center justify-center gap-2 px-4 py-2 border border-slate-300 text-slate-700 bg-white rounded-lg text-sm font-semibold hover:bg-slate-100 transition-colors shadow-sm">
        <i class="fa-solid fa-file-invoice"></i> Cetak Struk
      </a>
      <button onclick="document.getElementById('modal-detail').classList.add('hidden'); document.getElementById('modal-detail').classList.remove('flex');" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-semibold hover:bg-brand-700 transition-colors shadow-sm">
        Tutup
      </button>
    </div>

  </div>
</div>

<script type="text/javascript">
  var thead = <?= json_encode($thead) ?>;
  var allow_read = <?= in_array("read_{$current['controller']}", $permission) ? 1 : 0 ?>
</script>