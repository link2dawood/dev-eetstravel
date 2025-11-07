
<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
<style>
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 20px;
    }

    .nav-tabs > li > a {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li > a:hover {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        background-color: transparent;
    }

    .tab-content {
        padding: 20px 0;
    }

    .tab-pane {
        animation: fadeIn 0.3s ease;
    }

    @keyframes  fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .info-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-item {
        background: white;
        padding: 15px;
        border-radius: 6px;
        border-left: 4px solid #3b82f6;
    }

    .info-item label {
        font-weight: 600;
        color: #495057;
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-item .value {
        color: #212529;
        font-size: 15px;
        word-break: break-word;
    }

    .criteria-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .criteria-badge {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .margin_button {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
    }

    .margin_button button {
        padding: 10px 20px;
        font-weight: 500;
    }

    .box-header {
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 15px;
        padding-bottom: 15px;
    }

    .box-header i {
        margin-right: 10px;
        color: #3b82f6;
    }

    .box-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .menu-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .menu-table table {
        margin-bottom: 0;
    }

    .menu-table thead {
        background: #f8f9fa;
    }

    .menu-table th {
        border-bottom: 2px solid #e9ecef;
        padding: 15px;
        font-weight: 600;
        color: #495057;
    }

    .menu-table td {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .invoice-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .invoice-table table {
        margin-bottom: 0;
    }

    .invoice-table thead {
        background: #f8f9fa;
    }

    .invoice-table th {
        border-bottom: 2px solid #e9ecef;
        padding: 15px;
        font-weight: 600;
        color: #495057;
    }

    .invoice-table td {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .comments-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e9ecef;
    }
</style>

<?php echo $__env->make('layouts.title',
   ['title' => 'Restaurant', 'sub_title' => 'Show Restaurant',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Restaurants', 'icon' => 'coffee', 'route' => route('restaurant.index')],
   ['title' => 'Show', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <!-- Action Buttons -->
            <div class="margin_button">
                <a href="javascript:history.back()">
                    <button class='btn btn-primary'>
                        <i class="fa fa-arrow-left"></i> <?php echo e(trans('main.Back')); ?>

                    </button>
                </a>
                <a href="<?php echo route('restaurant.edit', $restaurant->id); ?>">
                    <button class='btn btn-warning'>
                        <i class="fa fa-pencil"></i> <?php echo e(trans('main.Edit')); ?>

                    </button>
                </a>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" role='tablist'>
                <li role='presentation' class="active">
                    <a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>
                        <i class="fa fa-info-circle"></i> <?php echo e(trans('main.Info')); ?>

                    </a>
                </li>
                <li role='presentation'>
                    <a href="#history-tab" aria-controls='history-tab' role='tab' data-toggle='tab'>
                        <i class="fa fa-history"></i> <?php echo e(trans('main.History')); ?>

                    </a>
                </li>
                <li role='presentation'>
                    <a href="#menu-tab" aria-controls='menu-tab' role='tab' data-toggle='tab'>
                        <i class="fa fa-cutlery"></i> <?php echo e(trans('main.Menu')); ?>

                    </a>
                </li>
                <li role='presentation'>
                    <a href="#invoices-tab" aria-controls='invoices-tab' role='tab' data-toggle='tab'>
                        <i class="fa fa-file-invoice"></i> <?php echo e(trans('Invoices')); ?>

                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                
                <!-- Info Tab -->
                <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
                    <!-- Basic Information Section -->
                    <div class="info-section">
                        <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 600;"><?php echo e(trans('main.BasicInformation')); ?></h4>
                        <div class="info-row">
                            <div class="info-item">
                                <label><?php echo e(trans('main.Name')); ?></label>
                                <div class="value"><?php echo $restaurant->name; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.Code')); ?></label>
                                <div class="value"><?php echo $restaurant->code; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.Country')); ?></label>
                                <div class="value">
                                    <?php if(!empty($restaurant->country)): ?>
                                        <?php echo \App\Helper\CitiesHelper::getCountryById($restaurant->country)['name']; ?>

                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.City')); ?></label>
                                <div class="value">
                                    <?php if(!empty($restaurant->city)): ?>
                                        <?php if(is_numeric($restaurant->city)): ?>
                                            <?php echo \App\Helper\CitiesHelper::getCityById($restaurant->city)['name']; ?>

                                        <?php else: ?>
                                            <?php echo $restaurant->city; ?>

                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.Rate')); ?></label>
                                <div class="value"><?php echo $restaurant->rate_name; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.Website')); ?></label>
                                <div class="value">
                                    <?php if($restaurant->website): ?>
                                        <a href="<?php echo $restaurant->website; ?>" target="_blank"><?php echo $restaurant->website; ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information Section -->
                    <div class="info-section">
                        <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 600;"><?php echo e(trans('main.Address')); ?></h4>
                        <div class="info-row">
                            <div class="info-item">
                                <label><?php echo e(trans('main.AddressFirst')); ?></label>
                                <div class="value"><?php echo $restaurant->address_first; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.AddressSecond')); ?></label>
                                <div class="value"><?php echo $restaurant->address_second; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="info-section">
                        <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 600;"><?php echo e(trans('main.ContactInformation')); ?></h4>
                        <div class="info-row">
                            <div class="info-item">
                                <label><?php echo e(trans('main.WorkPhone')); ?></label>
                                <div class="value"><?php echo $restaurant->work_phone; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.WorkFax')); ?></label>
                                <div class="value"><?php echo $restaurant->work_fax; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.WorkEmail')); ?></label>
                                <div class="value"><?php echo $restaurant->work_email; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.ContactName')); ?></label>
                                <div class="value"><?php echo $restaurant->contact_name; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.ContactPhone')); ?></label>
                                <div class="value"><?php echo $restaurant->contact_phone; ?></div>
                            </div>
                            <div class="info-item">
                                <label><?php echo e(trans('main.ContactEmail')); ?></label>
                                <div class="value"><?php echo $restaurant->contact_email; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="info-section">
                        <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 600;"><?php echo e(trans('main.AdditionalInformation')); ?></h4>
                        <div class="info-item" style="margin-bottom: 20px;">
                            <label><?php echo e(trans('main.Criterias')); ?></label>
                            <div class="criteria-badges">
                                <?php $__empty_1 = true; $__currentLoopData = $restaurant->criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $__currentLoopData = $criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($criteria->id == $item->criteria_id): ?>
                                            <span class="criteria-badge"><?php echo $criteria->name; ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="info-item" style="margin-bottom: 20px;">
                            <label><?php echo e(trans('main.Comments')); ?></label>
                            <div class="value"><?php echo $restaurant->comments; ?></div>
                        </div>
                        <div class="info-item">
                            <label><?php echo e(trans('main.IntComments')); ?></label>
                            <div class="value"><?php echo $restaurant->int_comments; ?></div>
                        </div>
                    </div>

                    <!-- Files Section -->
                    <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>

                    <!-- Comments Section -->
                    <div class="comments-section" style="margin-top: 20px;">
                        <div class="box-header">
                            <i class="fa fa-comments-o"></i>
                            <h3 class="box-title"><?php echo e(trans('main.Comments')); ?></h3>
                        </div>
                        <div id="show_comments" style="margin-bottom: 20px;"></div>
                        <form method='POST' action='<?php echo e(route('comment.store')); ?>' enctype="multipart/form-data" id="form_comment">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <textarea class="form-control" id="content" name="content" placeholder="<?php echo e(trans('main.AddComment')); ?>" rows="4"></textarea>
                            </div>
                            <div class="form-group">
                                <label><?php echo e(trans('main.Files')); ?></label>
                                <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                            </div>
                            <input type="hidden" id="parent_comment" name="parent" value="<?php echo e(null); ?>">
                            <input type="hidden" id="default_reference_id" name="reference_id" value="<?php echo e($restaurant->id); ?>">
                            <input type="hidden" id="default_reference_type" name="reference_type" value="<?php echo e(\App\Comment::$services['restaurant']); ?>">
                            <button type="submit" class="btn btn-success" id="btn_send_comment">
                                <i class="fa fa-paper-plane"></i> <?php echo e(trans('main.Send')); ?>

                            </button>
                        </form>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" role='tabpanel' id='history-tab'>
                    <div id='history-container'></div>
                </div>

                <!-- Menu Tab -->
                <div class="tab-pane fade" role='tabpanel' id='menu-tab'>
                    <div style="margin-bottom: 20px;">
                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('menu.create'), \App\Menu::class); ?>

                    </div>

                    <div class="menu-table table-responsive">
                        <table id="menu-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo e(trans('main.Name')); ?></th>
                                    <th><?php echo e(trans('main.Description')); ?></th>
                                    <th style="width: 140px;"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($menu->id); ?></td>
                                        <td><?php echo e($menu->name); ?></td>
                                        <td><?php echo e($menu->description); ?></td>
                                        <td class="text-center">
                                            <?php echo $__env->make('component.action_buttons', [
                                                'routePrefix' => 'menu',
                                                'item' => $menu,
                                                'showEdit' => true,
                                                'showDelete' => true,
                                                'showView' => true
                                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center"><?php echo e(trans('main.NoMenusFound')); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Invoices Tab -->
                <div class="tab-pane fade" role='tabpanel' id='invoices-tab'>
                    <div style="margin-bottom: 20px;">
                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class); ?>

                    </div>

                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-6">
                            <input type="text" id="restaurantInvoiceSearchInput" class="form-control" placeholder="Search invoices..." onkeyup="filterRestaurantInvoiceTable()">
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-primary" onclick="exportRestaurantInvoicesToCSV()">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportRestaurantInvoicesToExcel()">
                                <i class="fa fa-download"></i> Export Excel
                            </button>
                        </div>
                    </div>

                    <div class="invoice-table table-responsive">
                        <table id="inovices-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice No</th>
                                    <th>Due Date</th>
                                    <th>Received Date</th>
                                    <th>Tour</th>
                                    <th>Service</th>
                                    <th>Office Name</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($invoices) && count($invoices) > 0): ?>
                                    <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($invoice->id); ?></td>
                                            <td><?php echo e($invoice->invoice_no ?? 'N/A'); ?></td>
                                            <td><?php echo e($invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : 'N/A'); ?></td>
                                            <td><?php echo e($invoice->received_date ? \Carbon\Carbon::parse($invoice->received_date)->format('Y-m-d') : 'N/A'); ?></td>
                                            <td><?php echo e($invoice->tour_name ?? 'N/A'); ?></td>
                                            <td><?php echo e($invoice->service_name ?? 'N/A'); ?></td>
                                            <td><?php echo e($invoice->office_name ?? 'N/A'); ?></td>
                                            <td><?php echo e(number_format($invoice->total_amount ?? 0, 2)); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo e($invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger')); ?>">
                                                    <?php echo e(ucfirst($invoice->status ?? 'pending')); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $__env->make('component.action_buttons', [
                                                    'routePrefix' => 'invoices',
                                                    'item' => $invoice,
                                                    'showEdit' => true,
                                                    'showDelete' => true,
                                                    'showView' => true
                                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No invoices found for this restaurant</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(isset($invoices) && method_exists($invoices, 'links')): ?>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <?php echo e($invoices->links()); ?>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <span id="services_name" data-service-name='Restaurant' data-history-route="<?php echo e(route('services_history', ['id' => $restaurant->id])); ?>"></span>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            initializeBootstrapTable('inovices-table');
        });

        function filterRestaurantInvoiceTable() {
            const input = document.getElementById('restaurantInvoiceSearchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('inovices-table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let display = false;
                const td = tr[i].getElementsByTagName('td');

                for (let j = 0; j < td.length - 1; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }

                tr[i].style.display = display ? '' : 'none';
            }
        }

        function exportRestaurantInvoicesToCSV() {
            exportTableToCSV('inovices-table', 'restaurant-invoices.csv');
        }

        function exportRestaurantInvoicesToExcel() {
            exportTableToExcel('inovices-table', 'restaurant-invoices');
        }

        function confirmMenuDelete(event, deleteUrl) {
            event.preventDefault();
            event.stopPropagation();
            
            if (confirm("Are you sure you want to delete this menu?")) {
                const form = document.createElement('form');
                form.action = deleteUrl;
                form.method = 'POST';
                form.style.display = 'none';

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.getAttribute('content');
                    form.appendChild(csrfInput);
                }

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/restaurant/show.blade.php ENDPATH**/ ?>