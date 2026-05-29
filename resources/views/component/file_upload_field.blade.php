@php
    $enableAjaxUploads = $enableAjaxUploads ?? true;
@endphp
<input id="attach" type="file" name="attach[]" multiple data-enable-ajax="{{ $enableAjaxUploads ? '1' : '0' }}">
<div id="err-container"></div>
<script type="text/javascript">
	$(document).on('ready', function(){
		let fileInput = $('#attach');
		let targetForm = fileInput.closest('form');
		let url = $(targetForm).attr('action');
		let showPreview = $('#showPreviewBlock').attr('data-info');
		showPreview = showPreview == true ? false : true;
		let enableAjaxUploads = fileInput.data('enable-ajax') !== 0 && fileInput.data('enable-ajax') !== '0';

		// Initialize fileinput
		fileInput.fileinput({
			uploadUrl: url,
			uploadExtraData: function() {
				let obj = {};
				let sr  = [];
				obj._token = "{{csrf_token()}}";
				
				// Collect all form inputs.
				// Use a negative selector instead of an allow-list so input
				// types like `tel`, `email`, `url`, `search`, `time`, etc.
				// aren't silently dropped from the upload extra data — the
				// old positive list missed `tel` and `email`, which broke
				// the dynamic contact-row sub-form on /clients/{id}/edit
				// whenever files were attached.
				// Checkboxes and radios are handled separately below.
				$(targetForm).find('input').not('[type=file], [type=submit], [type=reset], [type=button], [type=checkbox], [type=radio], [type=image]').each(function(){
					if ($(this).attr('name') && $(this).attr('id') !== 'attach') {
						obj[$(this).attr('name')] = $(this).val();
					}
				});
				
				$(targetForm).find('select').each(function(){
					if ($(this).attr('name')) {
						obj[$(this).attr('name')] = $(this).val();
					}
				});
				
				$(targetForm).find('textarea').each(function(){
					if ($(this).attr('name')) {
						obj[$(this).attr('name')] = $(this).val();
					}
				});
				
				$(targetForm).find('input:checkbox:checked').each(function(){
					if ($(this).attr('name')) {
						if (!obj[$(this).attr('name')]) {
							obj[$(this).attr('name')] = [];
						}
						obj[$(this).attr('name')].push($(this).val());
					}
				});
				
				$(targetForm).find('input:radio:checked').each(function(){
					if ($(this).attr('name')) {
						obj[$(this).attr('name')] = $(this).val();
					}
				});
				
				return obj;
			},
			showUpload: false,
			showPreview: showPreview,
			uploadAsync: false,
			initialCaption: false,
			required: false,
			minFileCount: 0,
			validateInitialCount: false,
			overwriteInitial: false,
			maxFileSize: 10240, // 10MB
			allowedFileExtensions: null, // Allow all file types
			elErrorContainer: '#err-container',
			msgPlaceholder: "Select files...",
			previewFileIcon: '<i class="ti ti-file"></i>',
			// Top-level button icons (the main action bar at the bottom of
			// the widget — Browse / Remove / Cancel). The plugin defaults to
			// glyphicon-* classes which aren't loaded; without these the
			// buttons render with empty icon slots.
			browseIcon: '<i class="ti ti-folder-open me-1"></i> ',
			browseLabel: ' Browse',
			browseClass: 'btn btn-sm btn-tms-primary',
			removeIcon: '<i class="ti ti-trash me-1"></i> ',
			removeLabel: ' Remove',
			removeClass: 'btn btn-sm btn-tms-secondary',
			cancelIcon: '<i class="ti ti-x me-1"></i> ',
			cancelLabel: ' Cancel',
			cancelClass: 'btn btn-sm btn-tms-secondary',
			uploadIcon: '<i class="ti ti-upload me-1"></i> ',
			uploadClass: 'btn btn-sm btn-tms-primary',
			fileActionSettings: {
				showUpload: false,
				showZoom: true,
				showDrag: true,
				showRemove: true,
				removeIcon: '<i class="ti ti-trash"></i>',
				removeClass: 'btn btn-xs btn-tms-action-danger',
				removeTitle: 'Remove file',
				zoomIcon: '<i class="ti ti-zoom-in"></i>',
				zoomClass: 'btn btn-xs btn-tms-action-primary',
				zoomTitle: 'Preview',
				dragIcon: '<i class="ti ti-grip-vertical"></i>',
				dragClass: 'text-slate-400',
				dragTitle: 'Reorder',
			}
		}).on('filebatchuploaderror', function(event, data, msg){
			console.error('Upload error:', data);
			let err = data.jqXHR.responseJSON;
			
			$('.validation-error').remove();
			$('.has-error').removeClass('has-error');
			
			if (err && typeof err === 'object') {
				for(let key in err){
					let targetInput = $("[name='" + key + "']");
					let par = $(targetInput).closest('.form-group');
					$(par).append("<p class='alert alert-danger validation-error'>" + err[key] + "</p>");
					$(par).addClass('has-error');
				}
			} else {
				$('#err-container').html('<div class="alert alert-danger">Upload failed. Please try again.</div>');
			}
		}).on('filebatchuploadcomplete', function(event, files, extra) {
			console.log('Upload complete');
		}).on('fileclear', function(event) {
			$('#err-container').html('');
		});
		
		if (!enableAjaxUploads) {
			return;
		}
		
		// Handle form submission
		$(targetForm).on('submit', function(event){ 
			event.preventDefault();
			
			// Clear previous errors
			$('.validation-error').remove();
			$('.has-error').removeClass('has-error');
			$('#err-container').html('');
			
			// Check if files are selected
			let filesCount = 0;
			try {
				filesCount = fileInput.fileinput('getFilesCount');
			} catch(e) {
				filesCount = fileInput[0].files.length;
			}
			
			if (filesCount === 0) {
				// No files - submit via normal AJAX
				console.log('Submitting without files');
				submitFormWithoutFiles();
			} else {
				// Has files - use fileinput upload
				console.log('Submitting with files');
				fileInput.fileinput('upload');
			}
		});
		
		// Function to submit form without files
		function submitFormWithoutFiles() {
			let formData = new FormData(targetForm[0]);
			
			// Remove file input from form data to avoid issues
			formData.delete('attach[]');
			
			$.ajax({
				url: url,
				method: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(res) {
					handleSuccessResponse(res);
				},
				error: function(xhr) {
					console.error('Submit error:', xhr);
					handleErrorResponse(xhr);
				}
			});
		}
		
		// Handle success response
		function handleSuccessResponse(res) {
			if(res.comments){
				$(document).find('#show_comments').html(res.content);
				$(document).find('#form_comment #content').val('');
				fileInput.fileinput('clear');
				$(document).find('#author_name').css({'display': 'none'});
				$(document).find('#name').html('');
				$(document).find('#parent_comment').val($('#id_comment').val());
			}
			else if (res.announcement) {
				$(document).find('#show_comments').html(res.content);
				$(document).find('#content').val('');
				fileInput.fileinput('clear');
				$(document).find('#author_name').css({'display': 'none'});
				$(document).find('#name').html('');
				$(document).find('#parent_comment').val($('#id_comment').val());
			}
			else if(res.range_data){
				$('.block-error-driver').text('').append('<span>'+res.error_message+'</span>').show();
			}
			else if(res.transfer_fail){
				$('.block-error-driver').text('').append('<span>'+res.error_message_transfer+'</span>').show();
			}
			else if(res.hotelContacts){
				$('.block-error').text('').append('<span>'+res.fullNameErrorValidate+'</span>').show();
			}
			else {
				if(res.error_buses === true){
					$('.block-error-driver').text('').append('<span>'+res.message_buses+'</span>').show();
				} else if(res.route){
					window.location.replace(res.route); 
				} else if(res.success){
					// Handle generic success
					if(res.redirect){
						window.location.href = res.redirect;
					} else {
						location.reload();
					}
				}
			}
		}
		
		// Handle error response
		function handleErrorResponse(xhr) {
			let err = xhr.responseJSON;
			if (err && typeof err === 'object') {
				for(let key in err){
					let errorMsg = Array.isArray(err[key]) ? err[key][0] : err[key];
					let targetInput = $("[name='" + key + "']");
					let par = $(targetInput).closest('.form-group');
					$(par).append("<p class='alert alert-danger validation-error'>" + errorMsg + "</p>");
					$(par).addClass('has-error');
				}
			} else {
				$('#err-container').html('<div class="alert alert-danger">An error occurred. Please check your input and try again.</div>');
			}
		}
		
		// Handle file upload success
		fileInput.on('filebatchuploadsuccess', function(event, data, previewId, index){
			let res = data.response;
			handleSuccessResponse(res);
		});
	});
</script>