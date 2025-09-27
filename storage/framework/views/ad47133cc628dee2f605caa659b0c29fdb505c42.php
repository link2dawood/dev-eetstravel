
<?php $__currentLoopData = $invoice_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="item-contact row " style="margin-bottom: 0px">
    <div class="col-md-2">
        <div class="form-group">
            <label for="item_name"><?php echo trans('Item Name'); ?></label>
            <input id="item_name" name="items[<?php echo e($count); ?>][item_name]" type="text" class="form-control" value="<?php echo e($invoice_item->item_name); ?>" required>
        </div>
    </div>
    <div class="col-md-2">
		<div class="form-group" style="padding-left: 0">
			<label for="item_desc"><?php echo trans('Quantity'); ?></label>
			<input id="item_desc" name="items[<?php echo e($count); ?>][quantity]" type="number" class="form-control" onchange = "calculateItemTotal(this)" value="<?php echo e($invoice_item->quantity); ?>" required>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group " style="padding-right: 0">
			<label for="amount"><?php echo trans('Price(excl. VAT)'); ?></label>
			<input id="amount" name="items[<?php echo e($count); ?>][amount]" type="number" class="form-control" onchange = "calculateItemTotal(this)" value="<?php echo e($invoice_item->amount); ?>" required>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<label for="vat">VAT Rate</label>
			<select name="items[<?php echo e($count); ?>][vat]"  id="vat" class="form-control" onchange = "calculateItemTotal(this)" required>
				<option value="" disabled="disabled" selected="selected"  >Choose option</option>
				<?php $__currentLoopData = $taxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php $tax_value = $tax->value/100; ?>
				<?php if($tax_value == $invoice_item->vat): ?>
				<option value="<?php echo e($tax_value); ?>" selected="selected"><?php echo e($tax->name); ?></option>
				<?php else: ?>
				<option value="<?php echo e($tax_value); ?>"><?php echo e($tax->name); ?></option>
				<?php endif; ?>

				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group " style="padding-right: 0">
			<label for="total_amount"><?php echo trans('Total  Amount'); ?></label>
			<input id="total_amount" name="items[<?php echo e($count); ?>][total_amount]" type="number" class="form-control item_total" value="<?php echo e($invoice_item->total_amount); ?>" readonly>
		</div>
	</div>								
	<div class="col-md-1">
		<div class="form-group" style="margin-top: 20px"><button id="delete_contact_item" class="delete btn btn-danger btn-sm" type ="button"><i class="fa fa-trash-o" ></i></button></div>
	</div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php /**PATH /var/www/html/resources/views/component/invoice_items.blade.php ENDPATH**/ ?>