jQuery(document).ready(function ($) {
  $('.uposts_table').DataTable({
    columnDefs: [{ type: 'date', targets: [5] }],
    order: [[5, 'desc']],
    language: {
      //customize pagination prev and next buttons: use arrows instead of words
      paginate: {
        previous: '<span class="fa fa-chevron-left"></span>',
        next: '<span class="fa fa-chevron-right"></span>',
      },
      //customize number of elements to be displayed
      lengthMenu:
        'Display <select class="form-control input-sm">' +
        '<option value="10">10</option>' +
        '<option value="20">20</option>' +
        '<option value="30">30</option>' +
        '<option value="40">40</option>' +
        '<option value="50">50</option>' +
        '<option value="-1">All</option>' +
        '</select> results',
    },
  });

  var numPager = 1;

  $('#post_type').on('change', function () {
    $('.upost_content').addClass('dnone');
    $('#upload_upost_images').val('');
    $('.upost_image_previews').html('');
    $('#upload_upost_pdf').val('');
    $('.upost_pdf_previews img').attr('src', '');
    $("#patient_name").val("");
    $("#id_number").val("");
    $("#patient_age").val("");
    $("#patient_email").val("");
    $("#patient_phone").val("");
    $("#start_date").val("");
    $("#end_date").val("");
    $("#description").val("");

    if ($('#upload_upost_pdf').val() === '') {
      $("label[for='upload_upost_pdf'] span").text('Upload PDF');
    } else {
      $("label[for='upload_upost_pdf'] span").text('Replace PDF');
    }

    tinymce.get('upost_article_content').setContent('');

    switch ($(this).val()) {
      case 'article':
        $('#article_content').removeClass('dnone');
        break;
      case 'images':
        $('#images_content').removeClass('dnone');
        break;
      case 'pdf':
        $('#pdf_content').removeClass('dnone');
        break;
      case 'certificate':
        $('#certificate_content').removeClass('dnone');
        break;
    }
  });

  // Images
  function readImageURL(input) {
    if (input.files) {
      $('.upost_image_previews').html('');
      for (let i = 0; i < input.files.length; i++) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('.upost_image_previews').append(
            `<div class="single_img"><img width="100px" align='middle'src="${e.target.result}"/></div>`
          );
        };
        reader.readAsDataURL(input.files[i]);
      }
    }
  }

  $('#upload_upost_images').change(function () {
    readImageURL(this);
  });

  function upost_pdf_view(pFile, numPage = 1) {
    let filename = '',
      canvas;
    filename = pFile.split('/').pop();

    let pdfjsLib = window['pdfjs-dist/build/pdf'];

    // The workerSrc property shall be specified.
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'pdf.warker.js';
    canvas = document.createElement('CANVAS');
    let pdfDoc = null,
      scale = 1,
      viewport,
      ctx = canvas.getContext('2d');

    function renderPage() {
      // Using promise to fetch the page
      pdfDoc.getPage(numPage).then(function (page) {
        viewport = page.getViewport({ scale: scale });
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        // Render PDF page into canvas context
        let renderContext = {
          canvasContext: ctx,
          viewport: viewport,
        };
        let renderTask = page.render(renderContext);
        // Wait for rendering to finish
        renderTask.promise.then(function () {
          $('.upost_pdf_previews img').attr(
            'src',
            canvas.toDataURL('image/jpeg')
          );
        });
      });
    }

    pdfjsLib.getDocument(pFile).promise.then(function (pdfDoc_) {
      pdfDoc = pdfDoc_;

      if (numPager > pdfDoc.numPages) {
        numPager = pdfDoc.numPages;
        return false;
      }

      if (numPager < 1) {
        numPager = 1;
        return false;
      }

      $('.leftPdfPage').css('display', 'inline-block');
      $('.rightPdfPage').css('display', 'inline-block');

      if (pdfDoc.numPages === 1) {
        $('.leftPdfPage').css('display', 'none');
        $('.rightPdfPage').css('display', 'none');
      }

      if (numPager == pdfDoc.numPages) {
        $('.rightPdfPage').css('display', 'none');
      }
      if (numPager == 1) {
        $('.leftPdfPage').css('display', 'none');
      }

      renderPage();
    });
  }

  function readPdfURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        upost_pdf_view(e.target.result);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  if ($(document).find('#pdf_loader').length > 0) {
    if ($('#pdf_loader').val() !== '') {
      upost_pdf_view($('#pdf_loader').val());
    }
  }

  $('#upload_upost_pdf').change(function () {
    if ($(this).val() === '') {
      $('#upload_upost_pdf').val('');
      $('.upost_pdf_previews img').attr('src', '');
      $("label[for='upload_upost_pdf'] span").text('Upload PDF');
    } else {
      $("label[for='upload_upost_pdf'] span").text('Replace PDF');
    }

    readPdfURL(this);
  });

  // Post image load
  function load_post_preview(img) {
    $('#upost_image_preview img').attr('src', img);
  }
  load_post_preview($('.upost_image.active').find('img').attr('src'));
  $('.upost_image').on('click', function () {
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    load_post_preview($(this).find('img').attr('src'));
  });
  // post pdf load
  if ($(document).find('#upost_pdf_preview').length > 0) {
    upost_pdf_view($('#upost_pdf_preview').attr('data-src'));
  }

  $('.leftPdfPage').on('click', function () {
    if (numPager > 1) {
      numPager--;
      upost_pdf_view($('#upost_pdf_preview').attr('data-src'), numPager);
    }
  });
  $('.rightPdfPage').on('click', function () {
    numPager++;
    upost_pdf_view($('#upost_pdf_preview').attr('data-src'), numPager);
  });

  $('a.download-file').on('click', function (e) {
    e.preventDefault();
    var link = document.createElement('a');
    link.href = $(this).attr('data-pdf');
    link.download = $(this).attr('data-name') + '.pdf';
    link.dispatchEvent(new MouseEvent('click'));
    $('.action_popup').addClass('dnone');
  });

  function generateZIP(links, zipFilename) {
    var zip = new JSZip();
    var count = 0;

    links.forEach(function (url, i) {
      var filename = links[i];
      filename = filename
        .replace(/[\/\*\|\:\<\>\?\"\\]/gi, '')
        .replace('easeare.com', '');
      // loading a file and add it in a zip file
      JSZipUtils.getBinaryContent(url, function (err, data) {
        if (err) {
          throw err; // or handle the error
        }
        zip.file(filename, data, { binary: true });
        count++;
        if (count == links.length) {
          zip.generateAsync({ type: 'blob' }).then(function (content) {
            saveAs(content, zipFilename);
          });
        }
      });
    });
  }

  $('a.download-images').on('click', function (e) {
    e.preventDefault();
    let links = [];
    $('.upost_image').each(function () {
      links.push($(this).find('img').attr('src'));
    });

    generateZIP(links, $(this).attr('data-name'));
  });

  $(document).on('click', '.action_btn', function () {
    $(this).parents('tr').siblings().find('.action_popup').addClass('dnone');
    $(this).parents('td').find('.action_popup').toggleClass('dnone');
  });
  $(document).on('click', '.account_btn', function (e) {
    e.preventDefault();
    $(this)
      .parents('.account_menu')
      .find('.account_popup')
      .toggleClass('dnone');
  });

  $(document).on('click', '.upost-del-btn', function (e) {
    if (!confirm('Are you sure to delete this post?')) {
      e.preventDefault();
    }
  });

  $(document).on('click', '.shareBTN', function (e) {
    e.preventDefault();
    $(this).next('.sharing_popup').removeClass('dnone');
    $('body').css('overflow', 'hidden');
  });

  $(document).on('click', '.closeUrlPopup', function () {
    $(this).parents('.sharing_popup').addClass('dnone');
  });

  $(document).on('click', '.copy_post_url', function () {
    let input = $(this).parents(".url_field").find(".share_post_url");

    input[0].select();
    input[0].setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.val());
    
    $(this).parents(".url_field").find(".tooltiptext").text("Copied");
  });

  $(document).on("mouseout", ".copy_post_url", function(){
    $(this).parents(".url_field").find(".tooltiptext").text("Copy to clipboard");
  });


  $('#certificate_download').on('click', function (e) {
    e.preventDefault();
    $(this).attr("disabled", "disabled");
    let element = document.getElementById("medical_certificate");
    let name = $(this).attr("data-name");

    var opt = {
	    margin: [0, -14, 0, 0],
      filename:     name+'.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2, scrollY: 0 },
      jsPDF:        { unit: 'px', format: [element.clientHeight - 5, element.clientWidth], orientation: 'portrait' }
    };
    
    // New Promise-based usage:
    html2pdf().set(opt).from(element).save();
    $(this).removeAttr("disabled");
  });
});