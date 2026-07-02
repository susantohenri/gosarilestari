window.onload = function () {

  var ajax = {
    url: current_controller_url + '/dt',
    type: 'POST',
    data: (d) => {
      d.customFilter = $('form[name="custom_table_filter"]').serialize()
    }
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

  $('form[name="custom_table_filter"] input[name="kode"]').keyup(() => {
    dataTable.ajax.reload()
  });
}