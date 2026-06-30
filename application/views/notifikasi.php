<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="flex flex-wrap items-center justify-end gap-2 p-4 md:p-6 border-b border-slate-100">
    <a href="<?= site_url($current['controller']) ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>
  <div class="p-4 md:p-6">
    <?= $notif['informasi'] ?>
  </div>
</div>