<?php $__env->startSection('title', 'Edit'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title', [
        'title' => 'Supplier Invoice',
        'sub_title' => 'Edit Invoice',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <style>
        .cloned-container {
            margin-left: 210px;
            /* Adjust the margin as needed */
        }

        .button_div {
            margin-top: 0px;
            /* Adjust the margin as needed */
        }

        

        /* Style the button */
        .custom-button {
            background-color:darkslategray; /* Green background color */
            color: white; /* White text color */
            border: none; /* No border */
            padding: 10px 20px; /* Padding around the text */
            font-size: 18px; /* Font size */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Cursor style on hover */
        }

        /* Style the button on hover */
        .custom-button:hover {
            background-color: gray; /* Darker green on hover */
        }

        /* Style the icon inside the button */
        .custom-button i {
            margin-right: 5px; /* Spacing between icon and text */
        }
    </style>
    <section class="content">
        <form method='POST' action='<?php echo e(route('payment.store', ['id' => $invoices->id])); ?>' enctype="multipart/form-data">
            <?php echo e(csrf_field()); ?>

                    <?php if(count($errors) > 0): ?>
                        <br>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

            <div class="box box-secondry">
                <div class="box box-body ">
    
    
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <h1>Payment</h1>
                            </div>
                            <div>Payments that you were paid 
								to Supplier?</div>
                        </div>
                       
                        
                        <div class="margin_button">
                            <button class="custom-button" id="add_feild_button" type='button'>
                                <i class="fa fa-plus"></i>
                                <?php echo trans('Add Payment'); ?>

                            </button>
                        </div>
    
                        <div class="row">
                         
                                <div id="payment-inputs" class="row col-md-10">
    
                                </div>
                           
                        </div>
    
                    </div>
                    
    
    
    
    
    
    
                </div>
            </div>
            <a href="javascript:history.back()">
                <button type="button" class='btn btn-primary back_btn'><?php echo trans('main.Back'); ?></button>
            </a>
            <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
        </form>
    </section>
    <script type="text/javascript" src='<?php echo e(asset('js/rooms.js')); ?>'></script>
    <script type="text/javascript" src='<?php echo e(asset('js/hide_elements.js')); ?>'></script>

    <script type="text/javascript">
        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#pic').attr('src', e.target.result);
                    $('#file-caption-name').html(input.files[0].name);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function() {
            readURL(this);
        });

        APP_URL = '<?php echo e(url('/supplierdropdown')); ?>';
		function package_dropdown_ajax(selectedValue){
				$.ajax({
                            type: "GET",
                            url: APP_URL + '/' + selectedValue,
                            success: function(result) {

                                if (result[0] === "") {
                                    $("#service_div").show();
                                    $("#services").hide();
                                    $("#service_div").html(
                                        `<h3> Please Add Service in the tour </h3>`);
                                } else {
                                    $("#service_div").hide();
                                    $("#services").show();
                                    $("#services").html(result);
                                }
                            },
                            error: function(result) {
                                console.log(result);
                            }
                        });
			}
		var selectedValue = $(tour_id).val();
		package_dropdown_ajax(selectedValue);

        $(document).ready(function() {
			
            
            $("#tour_id").change(function() {
               			var selectedValue = $(this).val();
                		package_dropdown_ajax(selectedValue);
                        
                    });
              		

            function removeDropdown() {
                // Remove the dropdown element if no selection is made
                $('#services').next('select').remove();
                //  $("#service_div").hide();
            }
        });

        var array1 = [1, 2, 3, 4, 5];
        var array2 = [5, 7, 8, 9, 10];

        var allValuesNotInArray2 = true;

        array1.forEach(function(value) {
            if (array2.includes(value)) {
                allValuesNotInArray2 = false;
                return;
            }
        });

        if (allValuesNotInArray2) {
            console.log("All values of array1 are not in array2");
        } else {
            console.log("At least one value of array1 is present in array2");
        }
    </script>
    <script>
      let contactItemCount = 0;
      invoice_payments();
		function invoice_payments() {
			let invoice_id = $("#invoice_id").val();
            $.ajax({
                url: '/api/getInvoicePayments/2',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1,
					invoice_id:invoice_id,
                }
            }).done((res) => {
                contactItemCount++;
                $('#payment-inputs').append(res);
                $('input[name="_token"]').each(function() {
                    // Replace the 'value' attribute with your CSRF token value
                    $(this).val("<?php echo e(csrf_token()); ?>");
                });
            });
        }
      function payment_view_ajax(){
            $.ajax({
                url: '/api/getPaymentView',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1
                }
            }).done((res) => {
                contactItemCount++;
                $('#payment-inputs').append(res);
                $('input[name="_token"]').each(function() {
            // Replace the 'value' attribute with your CSRF token value
            $(this).val("<?php echo e(csrf_token()); ?>");
        });
            });
        }   
      $('#add_feild_button').on('click', function() {
        payment_view_ajax();
        });
        $(document).on('click', '#delete_contact_item', function() {
            $(this).closest('.item-contact').remove();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/invoices/payment_create.blade.php ENDPATH**/ ?>