(function () {
  var toggle = document.getElementById('menu-toggle')
  var sidebar = document.getElementById('sidebar')
  var overlay = document.getElementById('sidebar-overlay')

  if (!toggle || !sidebar || !overlay) return

  function openSidebar() {
    sidebar.classList.remove('-translate-x-full')
    overlay.classList.remove('hidden')
    document.body.classList.add('overflow-y-auto', 'md:overflow-y-auto')
  }

  function closeSidebar() {
    sidebar.classList.add('-translate-x-full')
    overlay.classList.add('hidden')
    document.body.classList.remove('overflow-y-auto', 'md:overflow-y-auto')
  }

  toggle.addEventListener('click', function () {
    if (sidebar.classList.contains('-translate-x-full')) openSidebar()
    else closeSidebar()
  })

  overlay.addEventListener('click', closeSidebar)

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 768) closeSidebar()
  })

  // global search
  const model = `Wargas`;
  const field = `nama`;
  jQuery('[name="search"]').select2({
    minimumInputLength: 2,
    placeholder: 'Cari transaksi dg nama warga',
    ajax: {
      url: current_controller_url + '/select2/' + model + '/' + field,
      type: 'POST',
      dataType: 'json'
    }
  }).on('change', function () {
    jQuery(this).closest('form').submit();
  });
})()
