$(document).on('keydown', '.count_room_type', function(e){
    if (e.keyCode === 13) {
        e.preventDefault();
        $('.count_room_type').blur();
    }
});

$('.select_room_type').click(function(){
    var data = $(this).find('input');
    var list_selected_rooms = $('.count_room_type');
    var agreement = (data.attr('data-agreement')) ? data.attr('data-agreement') : $('#agreement_id').val();

    console.log(agreement);

    for(var i = 0; i < list_selected_rooms.length; i++){
        var item = list_selected_rooms[i];

        if($(item).attr('data-info') === data.attr('data-info')){
            return false;
        }
    }

    $.ajax({
        method: 'POST',
        url: '/season_hotel_room_types',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            room_type: data.val(),
            agreement: agreement,
            hotel_id: $('#hotel_id').val()
        }
    }).done((res) => {
        $('#list_selected_room_types').append(res);

        console.log(res);
       /// if(res.created_id) {
          //  data.attr('data-agreement', res.created_id);
            //$('#agreement_id').val(res.created_id);
        //}else{
          //  $('#agreement_id').val('');
        //}

        $('.list_room_types').slideUp(200);
    })
});

$('.btn_for_select_room_type').click(function(){
    if($('.list_room_types').css('display') === 'none'){
        $('.list_room_types').slideDown(200);
    }else{
        $('.list_room_types').slideUp(200);
    }
});

$(document).on('click', '.icon_delete_room_type', function(){
    $(this).closest('.item_selected_room_type').remove();
});