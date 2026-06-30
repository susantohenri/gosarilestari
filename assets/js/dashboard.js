jQuery('#warga_minus').DataTable({
  dom: 'rtp',
  processing: true,
  serverSide: true,
  ajax: {
    url: current_controller_url + '/dt',
    type: 'POST',
  },
  columnDefs: [
    { targets: [0], className: 'text-left' }
  ]
});