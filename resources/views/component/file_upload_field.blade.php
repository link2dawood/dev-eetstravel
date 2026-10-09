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
				
				// Collect all form inputs
				$(targetForm).find('input:text, input[type=number], input:password, input:hidden, input[type=date]').each(function(){
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
			previewFileIcon: '<i class="glyphicon glyphicon-file"></i>',
			fileActionSettings: {
				showUpload: false,
				showZoom: true,
				showDrag: true,
				showRemove: true,
				removeIcon: '<i class="glyphicon glyphicon-trash"></i>',
				removeClass: 'btn btn-sm btn-danger',
				zoomIcon: '<i class="glyphicon glyphicon-zoom-in"></i>',
				zoomClass: 'btn btn-sm btn-primary',
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