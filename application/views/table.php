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