<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/select2.min.css') ?>">
<form enctype="multipart/form-data" action="<?= site_url($current['controller']) ?>" method="POST" class="main-form">
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="flex flex-wrap items-center justify-end gap-2 p-4 md:p-6 border-b border-slate-100">
      <?php if ((empty($uuid) && in_array("create_{$current['controller']}", $permission)) || (!empty($uuid) && in_array("update_{$current['controller']}", $permission))) : ?>
        <button type="submit" class="btn-save inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors">
          <i class="fa fa-save"></i> Simpan
        </button>
      <?php endif ?>
      <?php if (!empty($uuid) && in_array("delete_{$current['controller']}", $permission)) : ?>
        <a href="<?= site_url($current['controller'] . "/delete/$uuid") ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
          <i class="fa fa-trash"></i> Hapus
        </a>
      <?php endif ?>
      <a href="<?= site_url($current['controller']) ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
        <i class="fa fa-arrow-left"></i> Batal
      </a>
    </div>

    <div class="p-4 md:p-6" data-controller="<?= $current['controller'] ?>">
      <input type="hidden" name="last_submit" value="<?= time() ?>">

      <?php if (!empty($error)) : ?>
        <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif ?>

      <div class="space-y-4">
        <?php foreach ($form as $field) : ?>
          <?php switch ($field['type']):
              case 'hidden': ?>
              <input class="form-control" type="<?= $field['type'] ?>" value="<?= $field['value'] ?>" name="<?= $field['name'] ?>" <?= $field['attr'] ?>>
              <?php break; ?>
            <?php case 'select': ?>
              <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-4 items-start">
                <label class="md:col-span-3 text-sm font-medium text-slate-700 pt-2"><?= htmlspecialchars($field['label']) ?></label>
                <div class="md:col-span-9">
                  <?php if (preg_match('/(multiple)/', $field['attr']) > 0) : ?>
                    <input type="hidden" name="<?= str_replace('[]', '', $field['name']) ?>">
                  <?php endif ?>
                  <select class="form-control w-full" name="<?= $field['name'] ?>" <?= $field['attr'] ?>>
                    <?php foreach ($field['options'] as $opt) : ?>
                      <option <?= $opt['value'] === $field['value'] || (is_array($field['value']) && in_array($opt['value'], $field['value'])) ? 'selected="selected"' : '' ?> value="<?= $opt['value'] ?>"><?= htmlspecialchars($opt['text']) ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              <?php break; ?>
            <?php case 'textarea': ?>
              <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-4 items-start">
                <label class="md:col-span-3 text-sm font-medium text-slate-700 pt-2"><?= htmlspecialchars($field['label']) ?></label>
                <div class="md:col-span-9">
                  <textarea class="form-control w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none" name="<?= $field['name'] ?>" <?= $field['attr'] ?>><?= htmlspecialchars($field['value']) ?></textarea>
                </div>
              </div>
              <?php break; ?>
            <?php case 'file': ?>
              <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-4 items-start">
                <label class="md:col-span-3 text-sm font-medium text-slate-700 pt-2"><?= htmlspecialchars($field['label']) ?></label>
                <div class="md:col-span-9 space-y-2">
                  <?php if (strlen($field['value']) > 0 && '0' !== $field['value']) : ?>
                    <img src="<?= base_url($field['value']) ?>" height="100" width="100" class="rounded-lg border border-slate-200">
                    <a href="<?= base_url($field['value']) ?>" target="_blank" class="inline-flex items-center gap-1 text-sm text-brand-600 hover:text-brand-700">Buka di tab baru</a>
                  <?php endif ?>
                  <input accept="image/*" capture class="form-control w-full px-3 py-2 border border-slate-200 rounded-lg" type="<?= $field['type'] ?>" name="<?= $field['name'] ?>" <?= $field['attr'] ?>>
                </div>
              </div>
              <?php break; ?>
            <?php default: ?>
              <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-4 items-start">
                <label class="md:col-span-3 text-sm font-medium text-slate-700 pt-2"><?= htmlspecialchars($field['label']) ?></label>
                <div class="md:col-span-9">
                  <input class="form-control w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none" type="<?= $field['type'] ?>" value="<?= htmlentities($field['value']) ?>" name="<?= $field['name'] ?>" <?= $field['attr'] ?>>
                </div>
              </div>
              <?php break; ?>
          <?php endswitch; ?>
        <?php endforeach ?>
      </div>
    </div>
  </div>
</form>
