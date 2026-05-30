<div class="max-w-lg mx-auto">
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-6 md:p-8">
      <form action="<?= site_url($current['controller']) ?>" class="main-form" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="last_submit" value="<?= time() ?>">
        <input type="hidden" name="delete" value="<?= $uuid ?>">

        <div class="text-center mb-8">
          <div class="w-14 h-14 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-2xl mx-auto mb-4">
            <i class="fa fa-exclamation-triangle"></i>
          </div>
          <h2 class="text-xl font-bold text-slate-800 mb-2">Yakin ingin menghapus?</h2>
          <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
            <i class="fa fa-check"></i> Ya, Hapus
          </button>
          <a href="<?= site_url($current['controller']) ?>" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
            <i class="fa fa-arrow-left"></i> Batal
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
