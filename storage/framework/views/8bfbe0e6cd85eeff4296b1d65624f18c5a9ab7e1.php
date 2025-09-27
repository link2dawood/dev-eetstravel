<?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="item-contact row "  style="margin-bottom: 0px">
    <div class="col-md-2">
        <input type="hidden" name="_token" value="FoLCzxpWMutk0cpjxl0Br5sZz2YQFviZIvm1FuJv">

        <div class="form-group">
            <label for="date"> Date *</label>
            <input class="form-control pull-right datepicker" name="paymentdate[]" id="date" type="date"
                value="<?php echo e($payment->date); ?>" required>
        </div>




    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label for="paid_amount">Paid *</label>
            <input class="form-control" name="paid_amount[]" id="paid_amount" type="number" value="<?php echo e($payment->amount); ?>" required>
        </div>
    </div>



    <div class="col-md-2">
        <div class="form-group">
    <label for="payment_method">Payment Method</label>
    <select name="payment_method[]" id="payment_method" class="form-control" required>
        <option value="" disabled="disabled" selected="selected">Choose option</option>
        <?php
            $paymentMethods = ["Transfer Out from Business Bank", "Business Credit Card", "Business Debit Card"];
        ?>
        <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($method); ?>" <?php echo e($method == $payment->payment_method ? 'selected' : ''); ?>>
                <?php echo e($method); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
    </div>
    <div class="col-md-2">
        <div class="form-group" style="margin-top: 20px"><button id="delete_contact_item" class="delete btn btn-danger btn-sm" type ="button"><i class="fa fa-trash-o" ></i></button></div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /var/www/html/resources/views/component/get_payment_form.blade.php ENDPATH**/ ?>