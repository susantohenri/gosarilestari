window.onload = function () {

  formInit($(`[data-controller="${current_controller}"]`))
  $('.main-form').submit(function () {
    $('[data-number]').each(function () {
      $(this).val(getNumber($(this)))
    })
    return true
  })

  $('.select2-selection__rendered .select2-selection__choice').each(function () {
    var atr = this.getAttribute('title')
    if (atr === '' || atr === null) $(this).remove()
  })

  if (window.location.href.indexOf('ChangePassword') > -1) {
    $('form a[href*="ChangePassword/delete"]').hide()
  }
}

function formInit(scope) {
  scope.find('select').not('.select2-hidden-accessible').each(function () {
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
  })

  scope.find('[data-number="true"]').keyup(function () {
    $(this).val(currency(getNumber($(this))))
  })

  const textareas = scope.find('textarea');
  if (textareas.length > 0) {
    textareas.each(function () {
      const name = $(this).attr('name');
      tinymce.init({
        selector: `[name="${name}"]`,
        license_key: 'gpl',
        suffix: '.min',
        base_url: 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.9',
        plugins: 'advlist autolink lists link image charmap print preview anchor paste',
        toolbar: 'undo redo | fontselect fontsizeselect | bold italic underline backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        // 3. Konfigurasi ekstra untuk handle Copy-Paste dari Google Docs
        paste_data_images: true,      // Mengizinkan paste gambar lokal/blob sebagai Base64
        paste_as_text: false,          // Memastikan format teks kaya (Rich Text) tidak hilang
        paste_webkit_styles: "all",    // Memaksa browser mempertahankan style (seperti underline dari Google Docs)
        paste_merge_formats: true,     // Menggabungkan format teks yang serupa agar rapi
        // 4. Mencegah TinyMCE menghapus tag otomatis saat proses pembersihan teks
        extended_valid_elements: 'img[class|src|border|alt|title|hlspace|vspace|width|height|align|onmouseover|onmouseout|name]',
      });
    });
  }
}

function getNumber(element) {
  var val = element.val() || element.html()
  val = val.split(',').join('')
  return isNaN(val) || val.length < 1 ? 0 : parseInt(val)
}

function currency(number) {
  var reverse = number.toString().split('').reverse().join(''),
    currency = reverse.match(/\d{1,3}/g)
  return currency.join(',').split('').reverse().join('')
}
