<?php if (!empty($error)) : ?>
  <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif ?>
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

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden p-6">
  <div class="flex gap-2 overflow-x-auto pb-4 filter-kategori">
    <button class="px-4 py-2 rounded-full bg-emerald-600 text-white text-sm font-medium whitespace-nowrap">
      Semua (<?= count($products) ?>)
    </button>
    <?php foreach ($categories as $cat): ?>
      <button data-kategori="<?= $cat->kategori ?>" class="px-4 py-2 rounded-full bg-slate-100 text-slate-600 text-sm whitespace-nowrap">
        <?= $cat->kategori ?> (<?= $cat->product_count ?>)
      </button>
    <?php endforeach ?>
  </div>

  <div class="grid grid-cols-4 gap-5 mt-2 products">

    <?php foreach ($products as $prod): ?>
      <div class="border border-slate-200 rounded-2xl overflow-hidden" data-kategori="<?= $prod->kategori ?>">
        <div class="bg-emerald-50 h-36 flex items-center justify-center relative">
          <span class="absolute top-3 left-3 text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full">
            ● Stok <?= $prod->stok ?>
          </span>
          <div class="text-5xl">🛢️</div>
        </div>

        <div class="p-4">
          <p class="text-xs text-slate-400 uppercase">
            <?= $prod->kategori ?>
          </p>
          <h3 class="font-bold mt-1">
            <?= $prod->nama ?>
          </h3>
          <p class="text-emerald-600 font-bold text-lg mt-2">
            Rp <?= number_format($prod->harga, 0, ',', '.') ?>
          </p>

          <div class="flex justify-between items-center mt-4">
            <span class="text-xs text-slate-500">
              Terjual <?= $prod->terjual ?>
            </span>
            <div class="flex gap-2">
              <a href="<?= site_url("ProdukTukar/Read/{$prod->uuid}") ?>" class="flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200">
                <i class="fa fa-file text-yellow-500"></i>
              </a>
              <a href="<?= site_url("ProdukTukar/Delete/{$prod->uuid}") ?>" class="flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200">
                <i class="fa fa-trash text-red-700"></i>
              </a>
            </div>
          </div>
        </div>

      </div>
    <?php endforeach ?>

  </div>
</div>

<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-kategori button');
    const productCards = document.querySelectorAll('.products [data-kategori]');

    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        // Hapus kelas aktif dari semua button
        filterButtons.forEach(b => b.classList.remove('bg-emerald-600', 'text-white'));
        filterButtons.forEach(b => b.classList.add('bg-slate-100', 'text-slate-600'));

        // Tambahkan kelas aktif ke button yang diklik
        btn.classList.remove('bg-slate-100', 'text-slate-600');
        btn.classList.add('bg-emerald-600', 'text-white');

        const kategori = btn.dataset.kategori;

        productCards.forEach(card => {
          if (!kategori || card.dataset.kategori === kategori) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  });
</script>