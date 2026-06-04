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
<script type="text/javascript">
  var thead = <?= json_encode($thead) ?>;
  var allow_read = <?= in_array("read_{$current['controller']}", $permission) ? 1 : 0 ?>
</script>