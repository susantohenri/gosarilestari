(function () {
  var toggle = document.getElementById('menu-toggle')
  var sidebar = document.getElementById('sidebar')
  var overlay = document.getElementById('sidebar-overlay')

  if (!toggle || !sidebar || !overlay) return

  function openSidebar() {
    sidebar.classList.remove('-translate-x-full')
    overlay.classList.remove('hidden')
    document.body.classList.add('overflow-hidden', 'md:overflow-hidden')
  }

  function closeSidebar() {
    sidebar.classList.add('-translate-x-full')
    overlay.classList.add('hidden')
    document.body.classList.remove('overflow-hidden', 'md:overflow-hidden')
  }

  toggle.addEventListener('click', function () {
    if (sidebar.classList.contains('-translate-x-full')) openSidebar()
    else closeSidebar()
  })

  overlay.addEventListener('click', closeSidebar)

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 768) closeSidebar()
  })
})()
