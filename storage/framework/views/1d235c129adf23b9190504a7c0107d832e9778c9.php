<div class="item_selected_room_type">
    <div class="name_room_type"><?php echo e($room->name); ?></div>
    <div class="block-qty-room">
        <label>Qty: </label>
        <input type="number" min="0" data-info="<?php echo e($room_type->room_type_id); ?>" name="room_types_qty[<?php echo e($room_type->room_type_id); ?>]" value="<?php echo e($room_type->count !== null ? $room_type->count :  null); ?>" class="count_room_type form-control">
    </div>
    
    <span class="icon_delete_room_type">
        <i class="fa fa-close"></i>
    </span>
</div><?php /**PATH /var/www/html/resources/views/component/item_agreement_hotel_room_type.blade.php ENDPATH**/ ?>