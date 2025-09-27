<input id="attach" type="file" name="attach[]" multiple=true>
<div id="err-container"></div>
<script type="text/javascript">
	$(document).on('ready', function(){
		let targetForm = $('#attach').closest('form');
		let url = $(targetForm).attr('action');
		let showPreview = $('#showPreviewBlock').attr('data-info');
		showPreview = showPreview == true ? false : true;

		$("#attach").fileinput({
			uploadUrl: url,
			uploadExtraData: () => {
				let obj = {};
                let sr  = [];
				obj._token = "{{csrf_token()}}";
				$(targetForm).find('input:text').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
                $(targetForm).find('input[type=number]').each(function(){
                    if (!$(this).attr('name')) return;
                    obj[$(this).attr('name')] = $(this).val();
                });
				$(targetForm).find('input:password').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
				$(targetForm).find('input:hidden').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
				$(targetForm).find('select').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
				$(targetForm).find('textarea').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
				$(targetForm).find('input[type=date]').each(function(){
					if (!$(this).attr('name')) return;
					obj[$(this).attr('name')] = $(this).val();
				});
                $(targetForm).find('input:checkbox').each(function(){
                	if (!$(this).attr('name')) return;
                    if($(this).is(":checked")) {
                        sr.push($(this).val());
                        obj[$(this).attr('name')] = sr;
                    }
                });
                
                $(targetForm).find('#imgInp').each(function () {
                    if (!$(this).attr('name')) return;

                    let files = $(this).prop('files');
                    let name = $(this).attr('name');

                    if(files){
                        for (let j = 0; j < files.length; j++){
//                            fd.append(name, files[j]);
                            obj[$(this).attr('name')] = files[j];
                        }
                    }
                });                
                
                // for ( instance in CKEDITOR.instances )  CKEDITOR.instances.content.setData('');

//                console.log(obj);
                return obj;
			},
			showUpload: false,
            showPreview: showPreview,
			uploadAsync: false,
            initialCaption : false,
            elErrorContainer: false,
			fileActionSettings: {
				showUpload: false,
                showZoom: true,
                showDrag: true,
                removeTitle: '',
                uploadTitle: '',
                zoomTitle: '',
                dragTitle: '',
                dragSettings: {},
                indicatorNewTitle: '',
                indicatorSuccessTitle: '',
                indicatorErrorTitle: '',
                indicatorLoadingTitle: ''
			}
		}).on('filebatchuploaderror', function(event, data, msg){
			let err = data.jqXHR.responseJSON;
            $('.validation-error').remove();
            $('.has-error').removeClass('has-error');
            for(let key in err){
                let targetInput = $("[name=" + key + "]");
                let par = $(targetInput).closest('.form-group');
                $(par).append("<p class='alert alert-danger validation-error'>" + err[key] + "</p>");
                $(par).addClass('has-error');
            }
		});
		
		$(targetForm).on('submit', function(event){ 
			event.preventDefault();
			$('#attach').fileinput('upload');
            
		});
		
		$('#attach').on('filebatchuploadsuccess', function(event, data, previewId, index){
//    		let res = JSON.parse(data.response);
    		let res = data.response;
            if(res.comments){
                $(document).find('#show_comments').html(res.content);
                // Clear form input, after added comment
                $(document).find('#form_comment #content').val('');
                $(document).find('#attach').fileinput('clear');

                $(document).find('#author_name').css({'display': 'none'});
                $(document).find('#name').html('');
                $(document).find('#parent_comment').attr('value', '');
                $(document).find('#parent_comment').attr('value', $('#id_comment').val());
            }
            else if (res.announcement) {
            	$(document).find('#show_comments').html(res.content);
            	$(document).find('#content').val('');
                $(document).find('#attach').fileinput('clear');

                $(document).find('#author_name').css({'display': 'none'});
                $(document).find('#name').html('');
                $(document).find('#parent_comment').attr('value', $('#id_comment').val());
            }
            else if(res.range_data){
                $('.block-error-driver').text('');
                $('.block-error-driver').append('<span>'+res.error_message+'</span>');
                $('.block-error-driver').css({'display': 'block'});
            }
            else if(res.transfer_fail){
                $('.block-error-driver').text('');
                $('.block-error-driver').append('<span>'+res.error_message_transfer+'</span>');
                $('.block-error-driver').css({'display': 'block'});
            }
            else if(res.hotelContacts){
                $('.block-error').text('');
                $('.block-error').append('<span>'+res.fullNameErrorValidate+'</span>');
                $('.block-error').css({'display': 'block'});
            }
            else{
    		    if(res.error_buses === true){
                    $('.block-error-driver').text('');
                    $('.block-error-driver').append('<span>'+res.message_buses+'</span>');
                    $('.block-error-driver').css({'display': 'block'});
                }else{
                    window.location.replace(res.route); 
//                    window.location.replace(res);
                }
            }
		})
	})
</script>