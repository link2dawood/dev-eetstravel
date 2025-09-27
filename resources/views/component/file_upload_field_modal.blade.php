<input id="attach_bus" type="file" name="attach_bus[]" multiple=true>
<div id="err-container"></div>
<script type="text/javascript">
	$(document).on('ready', function(){
	    let modal_id = '#modalUpdateTrip';
	    let field = '#attach_bus';
		let targetFormBus = $(modal_id).find('#form_comment');
		let url = $(targetFormBus).attr('action');

		showPreview = false;

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
			fileActionSettings: {
				showUpload: false
			}
		}).on('filebatchuploaderror', function(event, data, msg){
			let err = data.jqXHR.responseJSON;
			// console.log(err.name);
			for(let key in err){
				let targetInput = $("input[name=" + key + "]");
				let par = $(targetInput).closest('.form-group');
				$(par).append("<p class='alert alert-danger'>" + err[key] + "</p>");
				$(par).addClass('has-error');
			}
		});
		
		$(targetFormBus).on('submit', function(event){
			event.preventDefault();
			$(field).fileinput('upload');
		});
		
		$(field).on('filebatchuploadsuccess', function(event, data, previewId, index){
    	//	let res = JSON.parse(data.response);
            let res = data.response;

    		if(res.comments){
                $(modal_id).find('#show_comments').html(res.content);

                chart2.clear();
                chart2 = null;
                createGraph();
                // Clear form input, after added comment
                $(modal_id).find('#content').val('');
                $(modal_id).find(field).fileinput('clear');

                $(modal_id).find('#author_name').css({'display': 'none'});
                $(modal_id).find('#name').html('');
                $(modal_id).find('#parent_comment').attr('value', '');
                $(modal_id).find('#parent_comment').attr('value', $('#id_comment').val());
            } else if (res.announcement) {
            	$(modal_id).find('#show_comments').html(res.content);
            	$(modal_id).find('#content').val('');
                $(modal_id).find('#attach_bus').fileinput('clear');

                $(modal_id).find('#author_name').css({'display': 'none'});
                $(modal_id).find('#name').html('');
                $(modal_id).find('#parent_comment').attr('value', $('#id_comment').val());
            }else{
                window.location.replace(res.route);
            }
		})
	})
</script>