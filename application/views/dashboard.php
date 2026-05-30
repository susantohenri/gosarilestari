<div class="max-w-3xl">
  <div class="bg-white p-6 md:p-8 rounded-xl border border-slate-100 shadow-sm">
    <h2 class="text-xl font-bold text-slate-800 mb-2">
      Selamat datang, <?= htmlspecialchars($this->session->userdata('nama') ?: $this->session->userdata('username')) ?> 👋
    </h2>
    <p class="text-slate-500 text-sm mb-6">
      Gunakan menu di sidebar untuk mengelola aktivitas Bank Sampah.
    </p>
    <div class="flex items-center gap-3 text-sm text-slate-600">
      <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
        <i class="fa-solid fa-leaf"></i>
      </div>
      <div>
        <div class="font-medium text-slate-800">gosarilestari.com</div>
        <div class="text-slate-500">Anda masuk sebagai <?= htmlspecialchars($this->session->userdata('role_name')) ?></div>
      </div>
    </div>
  </div>
</div>
