window.onload = function () {
  for (var th in thead) {
    $('.table-model tfoot tr').append('<th></th>')
  }

  var ajax = {
    url: current_controller_url + '/dt',
    type: 'POST',
    data: (d) => {
      d.customFilter = $('form[name="custom_table_filter"]').serialize()
    },
    dataSrc: function (data) {
      footer = data.footer
      return data.data
    }
  }

  var footer = []
  var dataTable = $('.table-model').DataTable({
    dom: 'rtip',
    processing: true,
    serverSide: true,
    ajax,
    columns: thead,
    createdRow: function (row, data, dataIndex) {
      if (data.prosentase && parseInt(data.prosentase.replace('%', '').split(',').join('')) > 100) $(row).css('background-color', '#ffcccc')
    },
    fnRowCallback: function (nRow, aData, iDisplayIndex) {
      $(nRow).css('cursor', 'pointer').click(function () {
        if (!allow_read) return false
        else window.location.href = current_controller_url + '/read/' + aData.uuid
      })
    },
    drawCallback: function (settings) {
      var api = this.api()
      for (var f in footer) $(api.column(f).footer()).html(footer[f])
    }
  });

  $('.dataTables_info, .dataTables_paginate')
    .wrapAll('<div class="flex justify-between items-center w-full mt-3"></div>');

  $('form[name="custom_table_filter"]')
    .find('select').not('.select2-hidden-accessible').each(function () {
      if ($(this).is('[data-autocomplete]')) {
        var model = $(this).attr('data-model')
        var field = $(this).attr('data-field')
        $(this).select2({
          ajax: {
            url: current_controller_url + '/select2/' + model + '/' + field,
            type: 'POST',
            dataType: 'json'
          }
        })
      } else if ($(this).is('[data-suggestion]')) {
        $(this).select2({
          tags: true,
          createTag: function (params) {
            return {
              id: params.term,
              text: params.term,
              newOption: true
            }
          },
          templateResult: function (data) {
            var $result = $('<span></span>')
            $result.text(data.text)
            if (data.newOption) $result.append('<em> (add new)</em>')
            return $result
          }
        })
      } else {
        $(this).select2()
      }
      $(this).change(() => {
        dataTable.ajax.reload()
      })
    })

  $('form[name="custom_table_filter"] input[name="fnama"]').keyup(() => {
    dataTable.ajax.reload()
  });

}