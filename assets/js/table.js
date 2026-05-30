window.onload = function () {

  for (var th in thead) {
    $('.table-model tfoot tr').append('<th></th>')
  }

  var ajax = {
    url: current_controller_url + '/dt',
    type: 'POST',
    dataSrc:function (data) {
    footer = data.footer
    return data.data
  }}

  if (current_controller_url.indexOf('/Asset') > -1) {
    $('#filter_asset_status').change (function () {
      dataTable.draw()
    })
    ajax.data = function (data) {
      data.Active = $('#filter_asset_status').val()
    }
  }

  var footer = []
  var dataTable = $('.table-model').DataTable( {
    processing: true,
    serverSide: true,
    ajax,
    columns: thead,
    createdRow: function( row, data, dataIndex){
      if (data.prosentase && parseInt(data.prosentase.replace('%', '').split(',').join('')) > 100) $(row).css('background-color', '#ffcccc')
    },
    fnRowCallback: function(nRow, aData, iDisplayIndex ) {
      $(nRow).css('cursor', 'pointer').click( function () {
        if (!allow_read) return false
        else window.location.href = current_controller_url + '/read/' + aData.uuid
      })
    },
    drawCallback: function( settings ) {
      var api = this.api()
      for (var f in footer) $(api.column(f).footer()).html(footer[f])
    }
  })
}