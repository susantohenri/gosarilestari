<div class="mt-auto py-10 px-2 hidden md:block">
  <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-blue-700 p-4 text-white shadow-soft">
    <p class="text-xs text-white/80">Sampah terkumpul</p>
    <p class="mt-1 text-3xl font-extrabold tracking-tight"><?= $sampah_terkumpul['total'] ?> kg</p>
    <p class="text-xs text-white/80">Bulan <?= date('m Y') ?></p>
    <div class="mt-2 h-2 rounded-full bg-white/20">
      <div class="h-2 w-[<?= $sampah_terkumpul['hijau'] ?>%] rounded-full bg-green-500"></div>
    </div>
    <small>Terpilah dg baik</small>
    <div class="mt-2 h-2 rounded-full bg-white/20">
      <div class="h-2 w-[<?= $sampah_terkumpul['kuning'] ?>%] rounded-full bg-yellow-500"></div>
    </div>
    <small>Terpilah sebagian</small>
    <div class="mt-2 h-2 rounded-full bg-white/20">
      <div class="h-2 w-[<?= $sampah_terkumpul['merah'] ?>%] rounded-full bg-red-500"></div>
    </div>
    <small>Tidak terpilah</small>
  </div>
</div>