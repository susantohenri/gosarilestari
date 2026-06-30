const curr_url = window.location.href
const activation = curr_url.replace('Read', 'Activation')
const activate = curr_url.replace('Read', 'Activate')
jQuery.get(activation, (isActivated) => {
  if (!isActivated) {
    jQuery(`[type="submit"]`).before(`
        <a href="${activate}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-medium hover:bg-yellow-600 transition-colors">
          <i class="fa fa-check"></i> Aktivasi
        </a>
      `);
  }
});