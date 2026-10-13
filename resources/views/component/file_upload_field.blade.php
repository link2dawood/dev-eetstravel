@php
    $enableAjaxUploads = $enableAjaxUploads ?? true;
@endphp
<style>
    .file-input .file-caption-main { flex-wrap: wrap; }
    .file-input .file-caption-main .file-caption { flex: 1 1 160px; }
    .file-input .file-caption-main .input-group-btn {
        flex: 1 1 auto;
        flex-wrap: wrap;
        max-width: 100%;
    }
    .file-input .file-caption-main .btn { min-width: 0; }
</style>
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
			handleErrorResponse(data.jqXHR);
		}).on('filebatchuploadcomplete', function(event, files, extra) {
			console.log('Upload complete');
		}).on('fileclear', function(event) {
			$('#err-container').html('');
		});
		
		if (!enableAjaxUploads) {
			return;
		}

		// One save at a time: a second click on Save used to create the record twice
		function setSaving(on) {
			targetForm.data('saving', on);
			targetForm.find('button[type=submit], input[type=submit]').prop('disabled', on);
		}
		
		// Handle form submission
		$(targetForm).on('submit', function(event){ 
			event.preventDefault();
			if (targetForm.data('saving')) {
				return;
			}
			setSaving(true);
			
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
			let leavesPage = !res.comments && !res.announcement && !res.range_data && !res.transfer_fail
				&& !res.hotelContacts && res.error_buses !== true && (res.route || res.success);
			if (!leavesPage) {
				setSaving(false);
			}
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
			setSaving(false);
			let err = xhr.responseJSON;
			// Laravel validation replies { message, errors: { field: [..] } }
			let fieldErrors = err && typeof err === 'object' ? (err.errors || err) : null;
			$('.validation-error').remove();
			$('.has-error').removeClass('has-error');
			let shown = 0;
			if (fieldErrors && typeof fieldErrors === 'object') {
				for (let key in fieldErrors) {
					if (key === 'message' || key === 'success' || key === 'status_code' || key === 'debug') continue;
					let errorMsg = Array.isArray(fieldErrors[key]) ? fieldErrors[key][0] : fieldErrors[key];
					let targetInput = $("[name='" + key + "'], [name='" + key + "[]']").first();
					let par = targetInput.closest('.form-group, .mb-3, [class*="col-"]');
					if (!par.length) continue;
					$('<p class="alert alert-danger validation-error mt-1 mb-0"></p>').text(errorMsg).appendTo(par);
					par.addClass('has-error');
					shown++;
				}
			}
			let message = shown ? 'Please correct the highlighted fields.' : ((err && err.message) || 'An error occurred. Please check your input and try again.');
			if (typeof window.appToast === 'function') {
				window.appToast(message, 'error');
			} else {
				$('#err-container').html($('<div class="alert alert-danger"></div>').text(message));
			}
		}
		
		// Handle file upload success
		fileInput.on('filebatchuploadsuccess', function(event, data, previewId, index){
			let res = data.response;
			handleSuccessResponse(res);
		});
	});
</script>
