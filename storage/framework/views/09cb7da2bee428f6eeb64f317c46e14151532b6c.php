<div class="item_selected_room_type">
    <div class="name_room_type"><?php echo e($room_type->name); ?></div>
    <div class="block-qty-room">
        <label>Qty: </label>
        <input type="number" min="0" data-info="<?php echo e($room_type->id); ?>" name="room_types_qty[<?php echo e($room_type->id); ?>]" value="<?php echo e(isset($room_type->count_room) && $room_type->count_room !== null ? $room_type->count_room : ''); ?>" class="count_room_type form-control">
    </div>

    <?php if( Request::path() !== 'hotel_room_types_tours' && \Route::current()->getName() !== 'tour.edit'  ): ?>
    <div class="block-price-room">
        <label><?php echo trans('main.Price'); ?>: </label>
        <input type="" readonly data-info="<?php echo e($room_type->id); ?>" name="room_types_price[<?php echo e($room_type->id); ?>]" value="<?php echo e(isset($room_type->price_room) ? $room_type->price_room : ''); ?>" class="count_room_type form-control ">
    </div>
    <?php endif; ?>
    <span class="icon_delete_room_type">
        <i class="fa fa-close"></i>
    </span>
</div><?php /**PATH /var/www/html/resources/views/component/item_hotel_room_type.blade.php ENDPATH**/ ?>