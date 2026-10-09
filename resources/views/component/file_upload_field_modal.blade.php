<input id="attach_bus" type="file" name="attach_bus[]" multiple>
<div id="err-container"></div>
<script type="text/javascript">
	$(document).on('ready', function(){
	    let modal_id = '#modalUpdateTrip';
	    let field = '#attach_bus';
		let targetFormBus = $(modal_id).find('#form_comment');
		let url = $(targetFormBus).attr('action');
		let showPreview = false;

        $(field).fileinput({
			uploadUrl: url,
			uploadExtraData: () => {
				let obj = {};
                let sr  = [];
				obj._token = "{{csrf_token()}}";
				
				$(targetFormBus).find('input:text').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
                $(targetFormBus).find('input[type=number]').each(function(){
                    if (!$(this).attr('name')) return;
                    obj[$(this).attr('name')] = $(this).val();
                });
				$(targetFormBus).find('input:hidden').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
				$(targetFormBus).find('select').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
				$(targetFormBus).find('textarea').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
                $(targetFormBus).find('input:checkbox').each(function(){
                	if (!$(this).attr('name')) return;
                    if($(this).is(":checked")) {
                        sr.push($(this).val());
                        obj[$(this).attr('name')] = sr;
                    }
                });
                return obj;
			},
			showUpload: false,
            showPreview: showPreview,
			uploadAsync: false,
			elErrorContainer: false,
			// CRITICAL: Prevent validation errors
			required: false,
			minFileCount: 0,
			maxFileCount: 50,
			validateInitialCount: false,
			overwriteInitial: false,
			initialCaption: false,
			fileActionSettings: {
				showUpload: false,
				showZoom: true,
				showRemove: true,
				showDrag: false
			}
		}).on('filebatchuploaderror', function(event, data, msg){
			let err = data.jqXHR.responseJSON;
			$('.validation-error').remove();
			$('.has-error').removeClass('has-error');
			for(let key in err){
				let targetInput = $(modal_id).find("input[name=" + key + "]");
				let par = $(targetInput).closest('.form-group');
				$(par).append("<p class='alert alert-danger validation-error'>" + err[key] + "</p>");
				$(par).addClass('has-error');
			}
		});
		
		$(targetFormBus).on('submit', function(event){
			event.preventDefault();
			
			// Check if files are selected
			let files = $(field).fileinput('getFilesCount');
			
			if (files === 0) {
				// Submit without files
				$.ajax({
					url: url,
					method: 'POST',
					data: $(targetFormBus).serialize(),
					success: function(res) {
						handleResponse(res);
					},
					error: function(xhr) {
						console.log('Error:', xhr);
						alert('An error occurred. Please try again.');
					}
				});
			} else {
				// Upload with files
				$(field).fileinput('upload');
			}
		});
		
		function handleResponse(res) {
			if(res.comments){
                $(modal_id).find('#show_comments').html(res.content);
                
                // Redraw chart if exists
                if (typeof chart2 !== 'undefined' && chart2 !== null) {
                    chart2.clear();
                    chart2 = null;
                    if (typeof createGraph === 'function') {
                        createGraph();
                    }
                }
                
                // Clear form
                $(modal_id).find('#content').val('');
                $(modal_id).find(field).fileinput('clear');
                $(modal_id).find('#author_name').css({'display': 'none'});
                $(modal_id).find('#name').html('');
                $(modal_id).find('#parent_comment').attr('value', '');
                $(modal_id).find('#parent_comment').attr('value', $('#id_comment').val());
            } 
            else if (res.announcement) {
            	$(modal_id).find('#show_comments').html(res.content);
            	$(modal_id).find('#content').val('');
                $(modal_id).find(field).fileinput('clear');
                $(modal_id).find('#author_name').css({'display': 'none'});
                $(modal_id).find('#name').html('');
                $(modal_id).find('#parent_comment').attr('value', $('#id_comment').val());
            }
            else if(res.route){
                window.location.replace(res.route);
            }
		}
		
		$(field).on('filebatchuploadsuccess', function(event, data, previewId, index){
            let res = data.response;
            handleResponse(res);
		});
	});
</script>