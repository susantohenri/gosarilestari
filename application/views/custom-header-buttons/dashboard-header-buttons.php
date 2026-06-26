<a href="<?= site_url('ExportImport/ExportLaporan') ?>"
  class="bg-yellow-500 text-white px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 flex items-center gap-2 shadow-sm">
  <i class="fa-solid fa-download"></i> Export Laporan
</a>
<?php if ('Warga' !== $this->session->userdata('role_name')) : ?>
  <a href="<?= site_url('Warga/create') ?>"
    class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 flex items-center gap-2 shadow-sm">
    <i class="fa-solid fa-plus"></i> Tambah Warga
  </a>
<?php endif; ?>