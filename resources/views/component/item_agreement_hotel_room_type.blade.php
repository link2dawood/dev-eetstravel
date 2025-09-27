<div class="item_selected_room_type">
    <div class="name_room_type">{{$room->name}}</div>
    <div class="block-qty-room">
        <label>Qty: </label>
        <input type="number" min="0" data-info="{{ $room_type->room_type_id }}" name="room_types_qty[{{$room_type->room_type_id}}]" value="{{ $room_type->count !== null ? $room_type->count :  null}}" class="count_room_type form-control">
    </div>
    {{--
    @if( Request::path() !== 'hotel_room_types_tours' && \Route::current()->getName() !== 'tour.edit'  )
    <div class="block-price-room">
        <label>{!!trans('main.Price')!!}: </label>
        <input type="number" min="0" data-info="{{ $room_type->room_type_id }}" name="room_types_price[{{$room_type->room_type_id}}]" value="{{ $room_type->price_room !== null ? $room_type->price_room :  null}}" class="count_room_type form-control">
    </div>
    @endif
    --}}
    <span class="icon_delete_room_type">
        <i class="fa fa-close"></i>
    </span>
</div>