<div class="item_selected_room_type">
    <div class="name_room_type">{{ $room_type->name }}</div>
    <div class="block-qty-room">
        <label>Qty: </label>
        <input type="number" min="0" data-info="{{ $room_type->id }}" name="room_types_qty[{{$room_type->id}}]" value="{{ isset($room_type->count_room) && $room_type->count_room !== null ? $room_type->count_room : ''}}" class="count_room_type form-control">
    </div>

    @if( Request::path() !== 'hotel_room_types_tours' && \Route::current()->getName() !== 'tour.edit'  )
    <div class="block-price-room">
        <label>{!!trans('main.Price')!!}: </label>
        <input type="" readonly data-info="{{ $room_type->id }}" name="room_types_price[{{$room_type->id}}]" value="{{ isset($room_type->price_room) ? $room_type->price_room : '' }}" class="count_room_type form-control ">
    </div>
    @endif
    <span class="icon_delete_room_type">
        <i class="fa fa-close"></i>
    </span>
</div>