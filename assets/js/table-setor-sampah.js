window.onload = function () {

  var ajax = {
    url: current_controller_url + '/dt',
    type: 'POST',
    dataSrc: function (data) {
      footer = data.footer
      return data.data
    }
  }

  const params = new URLSearchParams(window.location.search);
  const mapFilter = params.get('mapFilter');
  if (mapFilter) {
    const [page_title, rtrw, kategori] = mapFilter.split('|');
    jQuery('#page_title').html(page_title);
    ajax.data = function (d) {
      d.mapFilter = { rtrw, kategori };
    };
  }

  var footer = []
  var dataTable = $('.table-model').DataTable({
    dom: 'rtip',
    processing: true,
    serverSide: true,
    ajax,
    columns: thead
  });

  $('.dataTables_info, .dataTables_paginate')
    .wrapAll('<div class="flex justify-between items-center w-full mt-3"></div>');
}