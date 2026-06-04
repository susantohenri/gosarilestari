<div class="relative inline-block text-left">
  <button id="dropdownBtn" type="button"
    class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 flex items-center gap-2 shadow-sm"
    onclick="toggleDropdown()">
    <i class="fa-solid fa-download"></i> Import/Export CSV
    <svg class="w-4 h-4 ml-2" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
    </svg>
  </button>
  <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-lg shadow-lg z-10">
    <a href="<?= site_url('ExportImport/TemplateImportWarga') ?>"
      class="block px-4 py-2 text-slate-700 hover:bg-slate-50 text-sm">Download Template</a>
    <form method="POST" enctype="multipart/form-data" action="<?= site_url('ExportImport/ImportWarga') ?>">
      <label class="block px-4 py-2 text-slate-700 hover:bg-slate-50 text-sm cursor-pointer">
        Upload CSV
        <input type="file" name="csv_file" accept=".csv"
          class="hidden"
          onchange="this.form.submit()">
      </label>
    </form>
  </div>
</div>
<a href="<?= site_url('Warga/create') ?>"
  class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 flex items-center gap-2 shadow-sm">
  <i class="fa-solid fa-plus"></i> Tambah Warga
</a>
<script>
  function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    menu.classList.toggle('hidden');
  }

  // Optional: close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const btn = document.getElementById('dropdownBtn');
    const menu = document.getElementById('dropdownMenu');
    if (!btn.contains(event.target) && !menu.contains(event.target)) {
      menu.classList.add('hidden');
    }
  });
</script>