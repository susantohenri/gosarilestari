window.onload = function () {

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

  const params = new URLSearchParams(window.location.search);
  const search = params.get('search');
  if (search) {
    ajax.data = function (d) {
      d.search.value = search;
    };
  }

  var footer = []
  var dataTable = $('.table-model').DataTable({
    dom: 'rtip',
    processing: true,
    serverSide: true,
    ajax,
    columns: thead,
    fnRowCallback: function (nRow, aData, iDisplayIndex) {
      $(nRow).css('cursor', 'pointer').click(function () {
        if (!allow_read) return false;
        fetchTransactionDetails(aData.uuid);
      });
    }
  });

  function fetchTransactionDetails(uuid) {
    $.ajax({
      url: current_controller_url + '/detail/' + uuid,
      type: 'GET',
      success: function (response) {
        const data = JSON.parse(response);
        data.uuid = uuid;
        populateModal(data);
        $('#modal-detail').removeClass('hidden').addClass('flex');
      },
      error: function () {
        alert('Failed to fetch transaction details.');
      }
    });
  }

  function populateModal(data) {
    $('#modal-detail #print_button').attr('href', `${site_url}ExportImport/PrintReceipt/${data.uuid}`);
    $('#modal-detail [data-field="transaction-code"]').text(data.kode);
    $('#modal-detail [data-field="transaction-value"]').text(data.fnilai);
    $('#modal-detail [data-field="transaction-saldo"]').text(data.saldo);
    $('#modal-detail [data-field="transaction-keterangan"]').text(data.keterangan);
    $('#modal-detail [data-field="transaction-type"]').text(data.tipe);
    $('#modal-detail [data-field="citizen-name"]').html(data.fwarga);
    $('#modal-detail [data-field="citizen-id"]').text(data.warga_kode);
    $('#modal-detail [data-field="officer-name"]').text(data.fpetugas);
    $('#modal-detail [data-field="transaction-time"]').text(data.fwaktu);
    $('#modal-detail [data-field="transaction-code"]').text(data.kode);
  }

  $('.dataTables_info, .dataTables_paginate')
    .wrapAll('<div class="flex justify-between items-center w-full mt-3"></div>');

  $('form[name="custom_table_filter"] select').select2().change(() => {
    dataTable.ajax.reload()
  });

  $('form[name="custom_table_filter"] input[name="fnama"]').keyup(() => {
    dataTable.ajax.reload()
  });
}