<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="p-4 md:p-6">
    <?php if (in_array("create_{$current['controller']}", $permission)) : ?>
      <div class="flex justify-end mb-4">
        <a href="<?= site_url($current['controller'] . '/create') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 shadow-sm transition-colors">
          <i class="fa fa-plus"></i> <?= htmlspecialchars($page_title) ?>
        </a>
      </div>
    <?php endif ?>

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
