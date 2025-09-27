<input type="text" hidden id="target_form">
<div class="alert alert-danger" id="error_block" style="display: none; margin-top: 10px;">
    <ul>

    </ul>
</div>
<script>
    $(document).on('ready', function(){
        let form = $('#target_form').closest('form');
        let url = $(form).attr('action');
        let _token = $('meta[name="csrf-token"]').attr('content');


        $(form).on('submit', function (e) {
            e.preventDefault();
            let block_message = $('#error_block');
            let menu_list_errors = $(block_message).find('ul');

            $(block_message).css({'display':'none'});
            $(menu_list_errors).html('');

            let data = generateData();

            $.ajax({
                method: "POST",
                url: url,
                data: data,
                processData: false,
                contentType: false,
                success: function (response) {                 
                    window.location.replace(response.route);
                },
                error: function (error) {
                    let errors = error.responseJSON;
                    let list_message = [];

                    jQuery.each(errors, function(i, val) {
                        for (let i = 0; i < val.length; i++){
                            list_message.push(`<li>${val[i]}</li>`)
                        }
                    });

                    $(block_message).css({'display':'block'});
                    $(menu_list_errors).append(list_message);
                    $('#overlay_delete').remove();
                }
            });
        });

               
        function generateData() {
            let fd = new FormData;
            let sr = [];

            fd.append('_token', _token);

            // Text Field
            $(form).find('input:text').each(function(){
                if (!$(this).attr('name')) return;
                fd.append($(this).attr('name'), $(this).val());
            });

            // File Fields
            $(form).find('input[type=file]').each(function () {
                if (!$(this).attr('name')) return;

                let files = $(this).prop('files');
                let name = $(this).attr('name');
  
                if(files){
                    for (let j = 0; j < files.length; j++){
                        fd.append(name, files[j]);
                    }
                }
            });
            
            // Number Field
            $(form).find('input[type=number]').each(function(){
                if (!$(this).attr('name')) return;
                fd.append($(this).attr('name'), $(this).val());
            });

            // Hidden Field
            $(form).find('input:hidden').each(function(){
                if (!$(this).attr('name')) return;
                fd.append($(this).attr('name'), $(this).val());
            });

            // Select Field
            $(form).find('select:not(.select2)').each(function(){
                if (!$(this).attr('name')) return;
                fd.append($(this).attr('name'), $(this).val());
            });

            // Select 2 Field
            $(form).find('select.select2').each(function(){
                if (!$(this).attr('name')) return;
                let name = $(this).attr('name');
                let values =  $(this).val();

                if(values){
                    for (let i = 0; i < values.length; i++){
                        fd.append(name, values[i]);
                    }
                }
            });

            // Textare Field
            $(form).find('textarea').each(function(){
                if (!$(this).attr('name')) return;
                fd.append($(this).attr('name'), $(this).val());
            });

            // Textarea CKEditor Field
            $(form).find('textarea.textarea_editor').each(function(){
                if (!$(this).attr('name')) return;
                let id_area = $(this).attr('id') + '';
                let value = CKEDITOR.instances[id_area].getData();
                fd.append($(this).attr('name'), value);
            });

            // Checkbox field
            $(form).find('input:checkbox').each(function(){
                if (!$(this).attr('name')) return;
                if($(this).is(":checked")) {
                    sr.push($(this).val());
                    fd.append($(this).attr('name'), sr);
                }
            });

            return fd;
        }
    });
</script>