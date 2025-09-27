<div class="item-contact row " style="margin-bottom: 0px">
    <div class="col-md-2">
        <input type="hidden" name="_token" value="FoLCzxpWMutk0cpjxl0Br5sZz2YQFviZIvm1FuJv">

        <div class="form-group">
            <label for="date"> Date *</label>
            <input class="form-control pull-right datepicker"  name="paymentdate[]" id="date"
                type="date" value="" required>
        </div>




    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label for="paid_amount">Paid *</label>
            <input class="form-control" name="paid_amount[]" id="paid_amount" type="number" value="" required>
        </div>
    </div>



    <div class="col-md-2">
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method[]" id="payment_method" class="form-control" required>
                <option value="" disabled="disabled" selected="selected">Choose option</option>
                <option value="Transfer Out from Business Bank">Transfer Out from Business Bank Account</option>
                <option value="Business Credit Card">Business Credit Card</option>
                <option value="Business Debit Card">Business Debit Card</option>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group" style="margin-top: 20px"><button id="delete_contact_item"
                class="delete btn btn-danger btn-sm" type="button"><i class="fa fa-trash-o"></i></button></div>
    </div>
<?php /**PATH /var/www/html/resources/views/component/payment_form.blade.php ENDPATH**/ ?>