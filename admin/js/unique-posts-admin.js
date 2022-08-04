jQuery(function( $ ) {
	'use strict';

	function uploadMedia() {
		var imgfile, selectedFiles;
		// If the frame already exists, re-open it.
		if (imgfile) {
			imgfile.open();
			return;
		}
		//Extend the wp.media object
		imgfile = wp.media.frames.file_frame = wp.media({
			title: 'Choose Images',
			button: {
				text: 'Select'
			},
			multiple: true
		});

		//When a file is selected, grab the URL and set it as the text field's value
		imgfile.on('select', function () {
			selectedFiles = imgfile.state().get('selection');

			if(selectedFiles){
				selectedFiles.forEach(image => {
					let imgObj = image.toJSON();
					$("#upost_media_images").append(`<div class="mimage"><span class="remove_upost_img">+</span><img src="${imgObj.url}"><input type="hidden" name="upost_media_images[]" value="${imgObj.url}"></div>`);
				});
			}
			
		});

		//Open the uploader dialog
		imgfile.open();
	}

	$("#add-upost-images").on("click", function(e){
		e.preventDefault();
		uploadMedia();
	});

	$(document).on("click", ".mimage", function(){
		$(this).siblings().removeClass("selected");
		$(this).toggleClass("selected");
		let src = $(".mimage.selected").find("img").attr("src");
		
		$(".preview_upost_image").html(`<img src="${src}">`)
	});
	
	$(document).on("click", ".remove_upost_img", function(){
		$(this).parents(".mimage").remove();
	});


	function upost_pdf_view(pFile) {
		let filename = '', canvas;
		filename = pFile.split('/').pop()

		let pdfjsLib = window['pdfjs-dist/build/pdf'];

		// The workerSrc property shall be specified.
		pdfjsLib.GlobalWorkerOptions.workerSrc = 'pdf.warker.js';
		canvas = document.createElement("CANVAS");
		let pdfDoc = null,
		scale = 1, viewport,
		ctx = canvas.getContext('2d');
		
		function renderPage() {
			// Using promise to fetch the page
			pdfDoc.getPage(1).then(function (page) {
				viewport = page.getViewport({ scale: scale });
				canvas.height = viewport.height;
				canvas.width = viewport.width;
		
				// Render PDF page into canvas context
				let renderContext = {
					canvasContext: ctx,
					viewport: viewport
				};
				let renderTask = page.render(renderContext);
				// Wait for rendering to finish
				renderTask.promise.then(function () {
					$('#upost_pdf_view').attr("src", canvas.toDataURL('image/jpeg'));
				});
			});
		}

		pdfjsLib.getDocument(pFile).promise.then(function (pdfDoc_) {
			pdfDoc = pdfDoc_;
			renderPage();
		});
	}

	function loadPdfFile() {
		let pdfFile, selectedFile;
		// If the frame already exists, re-open it.
		if ( pdfFile ) {
			pdfFile.open();
			return;
		}
		//Extend the wp.media object
		pdfFile = wp.media.frames.file_frame = wp.media({
			title: 'Choose PDF',
			button: {
				text: 'Choose PDF'
			},
			library: {
				type: ['application/pdf']
			},
			multiple: false
		});

		//When a file is selected, grab the URL and set it as the text field's value
		pdfFile.on('select', function() {
			selectedFile = pdfFile.state().get('selection').first().toJSON();
			upost_pdf_view(selectedFile.url);
			$('#upost_document_file').val(selectedFile.url);
		});

		//Open the uploader dialog
		pdfFile.open();
	}

	$('.upost_document_btn').on("click", (e)=>{
		e.preventDefault();
		loadPdfFile();
	});

	if ($(document).find('#upost_document_file').length > 0) {
		if ($('#upost_document_file').val() !== "") {
			upost_pdf_view($('#upost_document_file').val());
		}
	}

	$("#type_of_upost_option").on("change", function(){
		$('.upost_content_box').addClass("dnone");
		$("#upost_media_images").html("");
		$('#upost_document_file').val("");
		$('#upost_pdf_view').attr("src", "");
		tinymce.get("upost_article_content").setContent("")
		
		switch ($(this).val()) {
			case 'article':
				$(".upost_article").removeClass("dnone")
				break;
				case 'images':
				$(".image_content").removeClass("dnone");
				break;
				case 'pdf':
				$(".pdf_content").removeClass("dnone");
				break;
		}
	});
});
