$(function () {
    initRoomListArea();

    if(location.search.split('tab=')[1] == 'room_list') {
        $('#roomlist_tab').click();
    }

});

function initRoomListArea() {
    if($(document).find('#roomlist_textarea').length > 0) {
        CKEDITOR.replace('roomlist_textarea',{
            title : false,
            extraPlugins: 'imageuploader,uicolor',
            height: '400px'
        } );
        getRoomList();

        $('#roomlist_form').find('#roomlist_send').on('click',function () {
            $('#question_modal').modal();
        });

        $('#question_modal').find('#send_agree').on('click',function () {
            $.ajax({
                method: 'GET',
                url: $('#roomlist_form').find('#send_url').val(),
                data:{ 
                    hotelIds: $("#hotelselect").val(),
                    roomlist_textarea: CKEDITOR.instances['roomlist_textarea'].getData(),
                    name: $('#guest_list_name').val()
                },
                beforeSend: function(){
                    $('#question_modal').modal('hide');
                },
            }).done((res) => {
                $('#error_send').find('#title_modal_error').html('');

                if(res.error === 'error'){
                    $('#error_send').find('.error_send_message').html(res.message);
                    $('#error_send').find('#title_modal_error').html('Warning!');
                }else{
                    $('#error_send').find('.error_send_message').html(res.message);
                    if(res.broke){
                        $('#error_send').find('.error_send_message').append('<br><br>'+res.broke);
                    }
                    $('#error_send').find('#title_modal_error').html('Success!');
                }

                $('#error_send').modal();

                setTimeout(function() {
                    $('#error_send').modal('hide');
                    if(res.error != 'error'){
                        window.location.href = URLtoGuestList; 
                    }    
                }, 3000);
            });
        });
    }        
}

function getRoomList() {
    $.ajax({
        method: 'GET',
        url: $('#roomlist_form').find('#show_url').val(),
    }).done((res) => {
        if(res.content !== 'empty'){
            CKEDITOR.instances['roomlist_textarea'].setData(res.content);
            $('#roomlist_form').show();
            $('#roomlist_add').hide();
            $('#roomlist_form').find('.roomlist_submit').removeClass('hidden');
        }
    });
}

