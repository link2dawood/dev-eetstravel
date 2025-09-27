<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Guide', 'sub_title' => 'Guide Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Guides', 'icon' => 'street-view', 'route' => route('guide.index')],
   ['title' => 'Show', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'><?php echo trans('main.Back'); ?></button>
                            </a>
                            <a href="<?php echo route('guide.edit', $guide->id); ?>">
                                <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                            </a>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'><?php echo trans('main.Info'); ?></a></li>
                    <li role='presentation'><a href="#history-tab" aria-controls='history-tab' role='tab' data-toggle='tab'><?php echo trans('main.History'); ?></a></li>
					<li role='presentation' class="tab" data-tab="invoices-tab"><a href="#invoices-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="invoices_tab" ><?php echo trans('Invoices'); ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
                        <table class='table_show col-lg-6 table table-bordered'>
                            <tbody>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.Name'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->name; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.Company'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->company; ?></td>
                            </tr>

                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.AddressFirst'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->address_first; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.AddressSecond'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->address_second; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.Country'); ?> : </i></b>
                                </td>
								<?php if(!empty($guide->country)): ?>
								<td class="info_td_show"><?php echo \App\Helper\CitiesHelper::getCountryById($guide->country)['name']; ?></td>
								<?php else: ?>
								<td class="info_td_show"></td>
								<?php endif; ?>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.City'); ?> : </i></b>
                                </td>
								<?php if(!empty($guide->city)): ?>
								<td class="info_td_show"><?php echo \App\Helper\CitiesHelper::getCityById($guide->city)['name']; ?></td>
								<?php else: ?>
								<td class="info_td_show"></td>
								<?php endif; ?>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.Code'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->code; ?></td>
                            </tr>

                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.WorkPhone'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->work_phone; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.WorkFax'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->work_fax; ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <table class='table_show col-lg-6 table table-bordered'>
                            <tbody>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.WorkEmail'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->work_email; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.IntComments'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->int_comments; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.Comments'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->comments; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.ContactName'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->contact_name; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.ContactPhone'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->contact_phone; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.ContactEmail'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->contact_email; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.Criterias'); ?> : </i></b>
                                </td>
                                <?php $__empty_1 = true; $__currentLoopData = $criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $__empty_2 = true; $__currentLoopData = $guide->criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <?php if($criteria->id == $item->criteria_id): ?>
                                            <td class="info_td_show criteria_block" style="display: block"><?php echo $criteria->name; ?></td>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                        <td class="info_td_show"></td>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <td class="info_td_show"></td>
                                <?php endif; ?>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.Rate'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->rate_name; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i><?php echo trans('main.Website'); ?> : </i></b>
                                </td>
                                <td class="info_td_show"><?php echo $guide->website; ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>
        <span id="showPreviewBlock" data-info="<?php echo e(true); ?>"></span>
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
                <i class="fa fa-comments-o"></i>

                <h3 class="box-title"><?php echo trans('main.Comments'); ?></h3>
            </div>
            <div class="box-body">
                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                        <div id="show_comments"></div>
                    </div>
                    <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                </div>
            </div>
            <!-- /.chat -->
            <div class="box-footer">
                <form method='POST' action='<?php echo e(route('comment.store')); ?>' enctype="multipart/form-data" id="form_comment">
                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo trans('main.Files'); ?></label>
                        <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                    </div>
                    <input type="text" id="parent_comment" hidden name="parent" value="<?php echo e(null); ?>">
                    <input type="text" id="default_reference_id" hidden name="reference_id" value="<?php echo e($guide->id); ?>">
                    <input type="text" id="default_reference_type" hidden name="reference_type" value="<?php echo e(\App\Comment::$services['guide']); ?>">

                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;"><?php echo trans('main.Send'); ?></button>
                </form>
            </div>
        </div>
                    </div>
                    <div class="tab-pane fade" role='tabpanel' id='history-tab'>
                        <div id='history-container'></div>
                    </div>
					<div role="tabpanel" class="tab-pane fade in" id="invoices-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class); ?>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" id="guideInvoiceSearchInput" class="form-control" placeholder="Search invoices..." onkeyup="filterGuideInvoiceTable()">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="exportGuideInvoicesToCSV()">Export CSV</button>
                            <button type="button" class="btn btn-success" onclick="exportGuideInvoicesToExcel()">Export Excel</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="inovices-table" class="table table-striped table-bordered table-hover">
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($invoices) && $invoices->count() > 0): ?>
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
                                    <td colspan="10" class="text-center">No invoices found for this guide</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(isset($invoices) && method_exists($invoices, 'links')): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo e($invoices->links()); ?>

                        </div>
                    </div>
                <?php endif; ?>



                </div>
                </div>
                
            </div>
        </div>
    </section>
<span id="services_name" data-service-name='Guide' data-history-route="<?php echo e(route('services_history', ['id' => $guide->id])); ?>"></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap table functionality
            initializeBootstrapTable('inovices-table');
        });

        // Guide invoice table search functionality
        function filterGuideInvoiceTable() {
            const input = document.getElementById('guideInvoiceSearchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('inovices-table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let display = false;
                const td = tr[i].getElementsByTagName('td');

                for (let j = 0; j < td.length - 1; j++) { // Exclude action column
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

        // Export functions for guide invoices
        function exportGuideInvoicesToCSV() {
            exportTableToCSV('inovices-table', 'guide-invoices.csv');
        }

        function exportGuideInvoicesToExcel() {
            exportTableToExcel('inovices-table', 'guide-invoices');
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/guide/show.blade.php ENDPATH**/ ?>