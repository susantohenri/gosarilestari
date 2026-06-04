<?php if (in_array("create_{$current['controller']}", $permission)) : ?>
<a href="<?= site_url($current['controller'] . '/create') ?>"
  class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 shadow-sm transition-colors">
  <i class="fa fa-plus"></i> Tambah
  <?= htmlspecialchars($page_title) ?>
</a>
<?php endif ?>