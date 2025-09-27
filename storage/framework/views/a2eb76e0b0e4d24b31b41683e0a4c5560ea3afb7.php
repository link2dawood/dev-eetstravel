<?php $__env->startSection('title', 'Create'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title', [
        'title' => 'Hotel Offers',
        'sub_title' => 'Create Offer',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">

                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>

                <form method="POST" id="hoteloffers_add_form" class="form-light"
                    action='<?php echo url('tour_package'); ?>/<?php echo $tour_package->id; ?>/offer_update' id="tour_package_add_form"
                    enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'><?php echo trans('main.Back'); ?></button>
                                </a>
                                <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                            <div class="input-wrapper col-md-4">
                                <input type="text" class="form-control" placeholder="Your Booking Refrence" name="reference">
                                <input type="hidden" value="<?php echo e($tour_package->pax); ?>" id="pax">
								<input type="hidden" value="<?php echo e($tour_package->id); ?>" name = "package_id">
                            </div>
                            <div class="input-wrapper col-md-4">
                                <label for="status"><?php echo trans('main.Status'); ?></label>
                                <select name="status" id="status" class="form-select form-control">
                                    <option value="Offered No rooms blocked" selected>Offered No rooms
                                        blocked
                                    </option>
                                    <option value="Offered with Option">Offered with Option</option>
                                    <option value="Waiting List">Waiting List</option>
                                    <option value="Unavailable">Unavailable</option>
    
                                </select>
                            </div>
                            <div class="input-wrapper col-md-4" id="option_with_date">
                                <label for="status"><?php echo trans('Option Date'); ?></label>
                                <input type="date" class="form-control" placeholder="" name="option_with_date">
                            </div>
                            <div class="">
								
                                <?php $__currentLoopData = $selected_room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selected_room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name ="room_type_id[]" value="<?php echo e($selected_room_type->id); ?>">
                                    <?php if($selected_room_type->code == 'SIN'): ?>
                                        <div class ="row">
                                            <label for="singleRate">Single Room Rate</label>
                                            <div class="input-wrapper" style="width: 113px;">
                                                <input class="form-control" type="text" id="singleRate"
                                                    name="room_rate_<?php echo e($selected_room_type->id); ?>" placeholder="Rate">
                                            </div>
                                            <div class="input-wrapper col-md-5 mt-2" style = "margin-left:5rem">
                                                <label for="singleBreakfastIncluded">Breakfast
                                                    included</label>
                                                <input type="checkbox" id="singleBreakfastIncluded"
                                                    name="is_breakfast_<?php echo e($selected_room_type->id); ?>" checked>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($selected_room_type->code == 'TWN'): ?>
                                        <div class ="row">
                                            <label for="twinRate">Twin Room Rate</label>
                                            <div class="input-wrapper col-md-2" style="width: 113px;">
                                                <input class="form-control" type="text" id="twinRate"
                                                    name="room_rate_<?php echo e($selected_room_type->id); ?>" placeholder="Rate">
                                            </div>
                                            <div class="input-wrapper col-md-5 mt-2" style="margin-left:5rem">
                                                <label for="twinBreakfastIncluded">Breakfast
                                                    included</label>
                                                <input type="checkbox" id="twinBreakfastIncluded"
                                                    name="is_breakfast_<?php echo e($selected_room_type->id); ?>" checked>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($selected_room_type->code == 'DOU'): ?>
                                        <div class ="row">
                                            <label for="doubleRate">Double Room Rate</label>
                                            <div class="input-wrapper" style="width: 113px;">
                                                <input class="form-control" type="text" id="doubleRate"
                                                    name="room_rate_<?php echo e($selected_room_type->id); ?>" placeholder="Rate">
                                            </div>
                                            <div class="input-wrapper col-md-5 mt-2" style="margin-left:5rem">
                                                <label for="doubleBreakfastIncluded">Breakfast
                                                    included</label>
                                                <input type="checkbox" id="doubleBreakfastIncluded"
                                                    name="is_breakfast_<?php echo e($selected_room_type->id); ?>" checked>
                                            </div>
    
                                        </div>
                                    <?php endif; ?>
                                    <?php if($selected_room_type->code == 'TRI'): ?>
                                        <div class ="row">
                                            <label for="tripleRate">Triple Room Rate</label>
                                            <div class="input-wrapper" style="width: 113px;">
                                                <input class="form-control" type="text" id="tripleRate"
                                                    name="room_rate_<?php echo e($selected_room_type->id); ?>"placeholder="Rate">
                                            </div>
                                            <div class="input-wrapper col-md-5 mt-2" style="margin-left:5rem">
                                                <label for="tripleBreakfastIncluded">Breakfast
                                                    included</label>
                                                <input type="checkbox" id="tripleBreakfastIncluded"
                                                    name="is_breakfast_<?php echo e($selected_room_type->id); ?>" checked>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($selected_room_type->code == 'SIE'): ?>
                                        <div class ="row">
                                            <label for="suiteRate">Suite Room Rate</label>
                                            <div class="input-wrapper" style="width: 113px;">
                                                <input class="form-control" type="text" id="suiteRate"
                                                    name="room_rate_<?php echo e($selected_room_type->id); ?>" placeholder="Rate">
                                            </div>
                                            <div class="input-wrapper col-md-5 mt-2" style="margin-left:5rem">
                                                <label for="suiteBreakfastIncluded">Breakfast
                                                    included</label>
                                                <input type="checkbox" id="suiteBreakfastIncluded"
                                                    name="is_breakfast_<?php echo e($selected_room_type->id); ?>" checked>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($selected_room_type->code == 'DFS'): ?>
                                        <div class ="row">
                                            <label for="dfsRate">Double for single Room Rate</label>
                                            <div class="input-wrapper" style="width: 113px;">
                                                <input class="form-control" type="text" id="dfsRate"
                                                    name="room_rate_<?php echo e($selected_room_type->id); ?>" placeholder="Rate">
                                            </div>
                                            <div class="input-wrapper col-md-5 mt-2" style="margin-left:5rem">
                                                <label for="dfsBreakfastIncluded">Breakfast
                                                    included</label>
                                                <input type="checkbox" id="dfsBreakfastIncluded"
                                                    name="is_breakfast_<?php echo e($selected_room_type->id); ?>" checked>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="row">
                                    <div class="input-wrapper" style="width: 113px;">
                                        <label for="halfboardMax">City Tax:</label>
                                        <input class="form-control" type="city_tax" id="city_tax" name="city_tax"
                                            min="0">
                                    </div>
    
                                    <div class="input-wrapper" style="width: 150px;">
                                        <label for="portrage_perperson">Porterage P.P:</label>
                                        <input class="form-control" type="portrage_perperson" id="portrage_perperson"
                                            name="portrage_perperson" min="0" style="width: 85px;">
                                    </div>
                                    <div class="input-wrapper" style="width: 200px;">
                                        <label for="halfboard">Halfboard Supp P.P:</label>
                                        <input class="form-control" type="number" id="halfboard" name="halfboard"
                                            min="0" max="999999" style="width: 85px;">
                                    </div>
                                </div>
                                <div class="row">
    									<div class="input-wrapper" style="width: 200px;">
                                                        <label for="foc_after_every_pax">Childeren Cost:</label>
                                                        <input class="form-control" type="number"
                                                            id="children_cost" name="children_cost"
                                                            min="0" style="width: 85px;">
                                                    </div>
													<div class="input-wrapper" style="width: 200px;">
                                                        <label for="foc_after_every_pax">F.O.C:</label>
                                                        <input class="form-control" type="number"
                                                            id="foc_after_every_pax" name="foc_after_every_pax"
                                                            min="0" style="width: 85px;">
                                                    </div>

                                                    
                                </div>
								<div class="input-wrapper" style="width: 200px;">
                                                        <label for="halfboardMax">Max allowed per group:</label>
                                                        <input class="form-control" type="number" id="halfboardMax"
                                                            name="halfboardMax" min="0" style="width: 85px;">
                                                    </div>
    
                                    <div class="input-wrapper col-md-4">
                                        <label for="currency">Currency</label>
                                        <select name="currency" id="currency" class="form-control form-select">
                                            <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($currency->id); ?>"
                                                    <?php echo e($currency->id == $tour_package->currency ? 'selected' : ''); ?>>
                                                    <?php echo e($currency->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
    
                            </div>
    
                            <div class="mb-3">
    
                                <input class="form-control" type="file" id="formFileDisabled" name="supplier_file">
                            </div>
                            <div class="input-wrapper">
                                <textarea name="hotel_note" id="" rows="4" class="form-control" placeholder="Note"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3>Other Conditions</h3>
                            <textarea class="form-control" id="otherConditions" name="otherConditions" rows="4" cols="50"></textarea>

                            <h2>Cancellation Policies</h2>
                            <div class="row">
                                <div class="input-wrapper" style="width: 113px;">
                                    <input type="number" class="form-control" id="cancellationDays" min="0">
                                </div>
                                <div class="input-wrapper col-md-2">
                                    <label for="cancellationDays">Days before Arrival:</label>
                                </div>

                                <div class="input-wrapper" style="width: 113px;">
                                    <input type="number" class=" form-control" id="cancellationPercentage" min="0">
                                </div>
                                <div class="input-wrapper col-md-4">
                                    <div class="input-group-append">
                                        <select class="form-control form-select" id="cancellationType">
                                            <option value="percentage">Percentage</option>
                                            <option value="amount">Amount</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <p>of rooms can be cancelled free of charge</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="addCancellation">+</button>
                            <div id="cancellationRequirements">
                                <!-- Cancellation requirements will be added here dynamically -->
                            </div>
                            <div class="input-wrapper mt-3">
                                <label for="cancellationNote">Additional Cancellation
                                    Policies:</label>
                                <textarea class="form-control" id="cancellationNote" name="cancellationNote" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>


    </section>
    <script>
    $("#option_with_date").hide();
    $("#status").change(function() {
        let val = $(this).val();
        if (val === "Offered with Option") {

            $("#option_with_date").show();
        } else {
            $("#option_with_date").hide();
        }

    });
    /*
	$('#price_person').on('input', function() {
    var value1 = parseFloat($('#price_person').val());
    var value2 = parseFloat($('#pax').val());
		
    
    // Perform the multiplication operation
    var result = value1 * value2;

    // Update the result in the target element
    $('#total_price').val(result);
  });
	*/
    $('#commentForm').on('submit', function(e) {

        e.preventDefault(); // Prevent default form submission

        // Get the form data
        var formData = $(this).serialize();

        // Make an AJAX POST request to the form's action URL
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {

                $("#comment_list").append(response);
            },
            error: function(xhr) {
                // Handle the error response
                // Display an error message or take appropriate action
            }
        });
    });

    document.getElementById('addCancellation').addEventListener('click', function() {
        const daysBeforeArrival = document.getElementById('cancellationDays').value;
        const percentageOrAmount = document.getElementById('cancellationPercentage').value;
        const cancellationType = document.getElementById('cancellationType').value;

        if (daysBeforeArrival && percentageOrAmount) {
            const daysInput = document.createElement('input');
            daysInput.type = 'hidden';
            daysInput.name = `cancellation_days[]`;
            daysInput.value = percentageOrAmount;
            const amountInput = document.createElement('input');
            amountInput.type = 'hidden';
            amountInput.name = `cancellation_percentage[]`;
            amountInput.value = daysBeforeArrival;
            const cancellationTypeInput = document.createElement('input');
            cancellationTypeInput.type = 'hidden';
            cancellationTypeInput.name = `cancellation_type[]`;
            cancellationTypeInput.value = cancellationType;


            const form = document.getElementById('hoteloffers_add_form');
            form.appendChild(daysInput);
            form.appendChild(amountInput);
            form.appendChild(cancellationTypeInput);
            const cancellationRequirements = document.getElementById('cancellationRequirements');
            const newRequirement = document.createElement('div');
            newRequirement.innerHTML =
                `<p>${daysBeforeArrival} days before arrival: ${percentageOrAmount} ${cancellationType} can be cancelled free of charge</p>`;
            cancellationRequirements.appendChild(newRequirement);
            daysBeforeArrival.value = '';
            percentageOrAmount.value = '';
        }
    });
/*
    document.getElementById('addDeposit').addEventListener('click', function() {
        const daysBeforeArrival = document.getElementById('deposit_days').value;
        const percentageOrAmount = document.getElementById('depositPercentage').value;
        const depositType = document.getElementById('depositType').value;

        if (daysBeforeArrival && percentageOrAmount) {
            const daysInput = document.createElement('input');
            daysInput.type = 'hidden';
            daysInput.name = `deposit_days[]`;
            daysInput.value = percentageOrAmount;
            const amountInput = document.createElement('input');
            amountInput.type = 'hidden';
            amountInput.name = `deposit_percentage[]`;
            amountInput.value = daysBeforeArrival;
            const depositTypeInput = document.createElement('input');
            depositTypeInput.type = 'hidden';
            depositTypeInput.name = `deposit_type[]`;
            depositTypeInput.value = depositType;


            const form = document.getElementById('hoteloffers_add_form');
            form.appendChild(daysInput);
            form.appendChild(amountInput);
            form.appendChild(depositTypeInput);

            const depositRequirements = document.getElementById('depositRequirements');
            const newRequirement = document.createElement('div');
            newRequirement.innerHTML =
                `<p> ${percentageOrAmount} ${depositType} can be paid before ${daysBeforeArrival}days before arrival </p>`;
            depositRequirements.appendChild(newRequirement);
        }
    });*/
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.modernapp', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/tour_package/offers/create.blade.php ENDPATH**/ ?>