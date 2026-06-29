<div class="mt-auto py-10 px-2 hidden md:block">
  <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-blue-700 p-4 text-white shadow-soft">
    <p class="text-xs text-white/80">Sampah terkumpul</p>
    <p class="mt-1 text-3xl font-extrabold tracking-tight"><?= $sampah_terkumpul['berat'] ?> kg</p>
    <p class="text-xs text-white/80">Bulan <?= $sampah_terkumpul['bulan_tahun'] ?></p>
    <div class="mt-4 h-2 rounded-full bg-white/20">
      <div class="h-2 w-[<?= $sampah_terkumpul['persen'] ?>%] rounded-full bg-white"></div>
    </div>
    <p class="mt-2 text-[11px] text-white/85"><?= $sampah_terkumpul['persen'] ?>% target <?= $sampah_terkumpul['target'] ?> kg</p>
  </div>
</div>