

<?php $__env->startSection('title', 'Create Invoice'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xl">
    
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/home')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('accounting.index')); ?>">Invoices</a></li>
                            <li class="breadcrumb-item active">Create Invoice</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-file-invoice me-2"></i>Create Client Invoice
                </h2>
            </div>
            
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="javascript:history.back()" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-1"></i><?php echo trans('main.Back'); ?>

                    </a>
                    <button type="submit" form="invoice-form" class="btn btn-success">
                        <i class="ti ti-device-floppy me-1"></i><?php echo trans('main.Save'); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?php echo e(url('accounting')); ?>" enctype="multipart/form-data" id="invoice-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
        
        <?php if(empty($quotation)): ?>
            <input id="quotation_id" type="hidden" name="quotation_id" value="">
        <?php else: ?>
            <input id="quotation_id" type="hidden" name="quotation_id" value="<?php echo e($quotation->id); ?>">
        <?php endif; ?>

        <?php if(count($errors) > 0): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-alert-circle me-2"></i></div>
                    <div>
                        <h4 class="alert-title">Validation Errors</h4>
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-file-text me-2"></i>Invoice Detail
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-md-3">
                        <label class="form-label required">Currency</label>
                        <select name="currency" id="currency" class="form-select" required>
                            <option value="" disabled selected>Choose currency...</option>
                            <option value="EUR">Euro (EUR)</option>
                            <option value="USD">Dollar (USD)</option>
                            <option value="CHF">Swiss Franks (CHF)</option>
                        </select>
                    </div>

                    
                    <div class="col-md-3">
                        <label class="form-label required">Office</label>
                        <select name="office_id" id="office_id" class="form-select" required>
                            <option value="" disabled selected>Choose office...</option>
                            <?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($office->id); ?>"><?php echo e($office->office_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-3">
                        <label class="form-label required"><?php echo e(trans('main.Tour')); ?></label>
                        <select name="tour_id" id="tour_id" class="form-select" required>
                            <option value="" disabled selected>Choose tour...</option>
                            <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tour->id); ?>"><?php echo e($tour->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-3">
                        <label class="form-label required">Client</label>
                        <select name="client_id" id="client_id" class="form-select" required>
                            <option value="" disabled selected>Choose client...</option>
                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($client->id); ?>"><?php echo e($client->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-6">
                        <label class="form-label">Extra Cost</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ti ti-currency-dollar"></i>
                            </span>
                            <input type="number" name="extra_cost" class="form-control" placeholder="0.00" step="0.01">
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <label class="form-label">Note</label>
                        <textarea id="note" name="note" class="form-control" rows="3" placeholder="Add any additional notes..."></textarea>
                    </div>

                    
                    <div class="col-md-12" id="services" style="display:none">
                        <label class="form-label">Service</label>
                        <select id="service" name="service" class="form-select">
                            <option value="" disabled selected>Choose service...</option>
                        </select>
                    </div>
                    <div class="col-md-12" id="service_div"></div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="card-title">
                            <i class="ti ti-list me-2"></i>Extra Items
                        </h3>
                        <p class="text-muted mb-0">Add extra items to this invoice</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success" id="add_contact" type="button">
                            <i class="ti ti-plus me-1"></i>Add Item
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="items-contacts" class="row g-3">
                    <div class="item-contact col-12">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Item Name</label>
                                <input id="item_name" name="items[1][item_name]" type="text" class="form-control" placeholder="Enter item name" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <input id="item_desc" name="items[1][quantity]" type="number" class="form-control" onchange="calculateItemTotal(this)" placeholder="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Price (excl. VAT)</label>
                                <input id="amount" name="items[1][amount]" type="number" class="form-control" onchange="calculateItemTotal(this)" placeholder="0.00" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">VAT Rate</label>
                                <select name="items[<?php echo e($count); ?>][vat]" id="vat" class="form-select" onchange="calculateItemTotal(this)" required>
                                    <option value="" disabled selected>Choose VAT...</option>
                                    <?php $__currentLoopData = $taxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tax->value/100); ?>"><?php echo e($tax->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Total Amount</label>
                                <input id="total_amount" name="items[1][total_amount]" type="number" class="form-control item_total" placeholder="0.00" readonly>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-icon btn-ghost-danger" id="delete_contact_item" title="Delete item">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="card-title">
                            <i class="ti ti-credit-card me-2"></i>Payment
                        </h3>
                        <p class="text-muted mb-0">How is the client paying you?</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success" id="add_feild_button" type="button">
                            <i class="ti ti-plus me-1"></i>Add Payment
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="payment-inputs" class="row g-3">
                    
                </div>
            </div>
        </div>

        
        <div class="d-flex justify-content-end gap-2 mt-3 mb-3">
            <a href="javascript:history.back()" class="btn btn-ghost-secondary">
                <i class="ti ti-arrow-left me-1"></i><?php echo trans('main.Back'); ?>

            </a>
            <button class="btn btn-success" type="submit">
                <i class="ti ti-device-floppy me-1"></i><?php echo trans('main.Save'); ?>

            </button>
        </div>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
<script type="text/javascript" src="<?php echo e(asset('js/rooms.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/hide_elements.js')); ?>"></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        let contactItemCount = 1;

        // File upload preview
        function readURL(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#pic').attr('src', e.target.result);
                    $('#file-caption-name').html(input.files[0].name);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").on('change', function() {
            readURL(this);
        });

        // Add extra invoice item
        function item_invoice_ajax() {
            $.ajax({
                url: '/api/getItemInvoiceView',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1
                }
            }).done((res) => {
                contactItemCount++;
                $('#items-contacts').append(res);
                $('input[name="_token"]').each(function() {
                    $(this).val("<?php echo e(csrf_token()); ?>");
                });
            });
        }

        // Add payment field
        function payment_view_ajax() {
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
                    $(this).val("<?php echo e(csrf_token()); ?>");
                });
            });
        }

        // Add item button
        $('#add_contact').on('click', function() {
            item_invoice_ajax();
        });

        // Add payment button
        $('#add_feild_button').on('click', function() {
            payment_view_ajax();
        });

        // Delete item button
        $(document).on('click', '#delete_contact_item', function() {
            $(this).closest('.item-contact').remove();
        });

        // Tour change handler - load quotation
        $("#tour_id").on('change', function() {
            const tour_id = $(this).val();
            $.ajax({
                type: "GET",
                url: `api/getTourquotation/${tour_id}`,
                success: function(result) {
                    $("#quotation_id").val(result);
                    console.log('Quotation loaded:', result);
                },
                error: function(result) {
                    console.error('Error loading quotation:', result);
                }
            });
        });

        // Calculate item total with VAT
        window.calculateItemTotal = function(iteminput) {
            const itemContainer = iteminput.closest('.row');
            const quantity = parseFloat(itemContainer.querySelector("#item_desc")?.value) || 0;
            const price = parseFloat(itemContainer.querySelector("#amount")?.value) || 0;
            const vat = parseFloat(itemContainer.querySelector("#vat")?.value) || 0;

            const total_price = quantity * price;
            const total_tax = total_price * vat;
            const itemTotal = total_price + total_tax;

            // Update the item total displayed for this item
            const totalElement = itemContainer.querySelector("#total_amount");
            if (totalElement) {
                totalElement.value = itemTotal.toFixed(2);
            }

            // Recalculate the overall total
            calculateOverallTotal();
        }

        // Calculate overall total
        function calculateOverallTotal() {
            const itemTotals = document.querySelectorAll(".item_total");
            let overallTotal = 0;

            itemTotals.forEach(itemTotal => {
                const value = parseFloat(itemTotal.value) || 0;
                overallTotal += value;
            });

            console.log('Overall total:', overallTotal.toFixed(2));
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/accounting/create.blade.php ENDPATH**/ ?>