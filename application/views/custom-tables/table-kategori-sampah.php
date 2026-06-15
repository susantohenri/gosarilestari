<?php if (!empty($error)) : ?>
  <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif ?>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="p-4 md:p-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h2 class="text-lg font-bold text-slate-800">Daftar Kategori Aktif</h2>
            <p class="text-sm text-slate-500"><?= count($pratinjau) ?> kategori harga berlaku <?= date('d M Y') ?></p>
          </div>
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
  </div>
  <div class="lg:col-span-1">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden my-2">
      <div class="bg-white rounded-3xl border border-slate-200 p-6">

        <h3 class="text-xl font-bold">
          Pratinjau di Aplikasi Warga
        </h3>

        <p class="text-sm text-slate-500 mt-1 border-b pb-3">
          Tampilan harga yang dilihat warga
        </p>

        <div class="mt-6 border rounded-2xl p-4">

          <div class="flex justify-between items-center mb-4 border-b pb-3">
            <h4 class="font-semibold">Harga Sampah Hari Ini</h4>
            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm">
              LIVE
            </span>
          </div>

          <div class="space-y-4">

            <?php foreach ($pratinjau as $item): ?>
              <div class="flex justify-between">
                <span><?= $item->nama ?></span>
                <span class="font-bold text-emerald-600">Rp <?= number_format($item->harga, 0, ',', '.') ?></span>
              </div>
            <?php endforeach ?>

          </div>

        </div>

      </div>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden my-2">
      <div class="p-4 md:p-6">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
          <div>
            <h2 class="text-lg font-bold text-slate-800">Panduan Harga</h2>
          </div>
        </div>
        <div>
          <ul class="text-sm text-blue-800 space-y-2 list-disc pl-4 marker:text-blue-400">
            <li>Tinjau harga setiap minggu untuk menyesuaikan dengan harga pasar pengepul.</li>
            <li>Logam (kaleng/aluminium) biasanya berharga tertinggi.</li>
            <li>Pastikan sampah disetor dalam kondisi bersih &amp; kering.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var thead = <?= json_encode($thead) ?>;
  var allow_read = <?= in_array("read_{$current['controller']}", $permission) ? 1 : 0 ?>
</script>