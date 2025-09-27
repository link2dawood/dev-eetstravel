<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Client', 'sub_title' => 'Client Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Clients', 'icon' => 'handshake-o', 'route' => route('clients.index')],
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
                        <a href="<?php echo route('clients.edit', $client->id); ?>">
                            <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                        </a>
                    </div>
                </div>
            </div>
            <div id="fixed-scroll" class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'><?php echo trans('main.Info'); ?></a></li>
                    <li role='presentation'><a href="#contacts-tab" aria-controls='contacts-tab' role='tab' data-toggle='tab'><?php echo trans('main.Contacts'); ?></a></li>
					<li role='presentation' class="tab" data-tab="billing-tab"><a href="#billing-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="billing_tab"><?php echo trans('Billing'); ?></a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
					<div class="row">
						<div class="col-lg-6">
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Name'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $client->name; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Country'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo \App\Helper\CitiesHelper::getCountryById($client->country)['name']; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.City'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo \App\Helper\CitiesHelper::getCityById($client->city)['name']; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Address'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $client->address; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.WorkPhone'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $client->work_phone; ?></td>
                        </tr>
                        </tbody>
                    </table>
						</div>
							<div class="col-lg-6">
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.ContactPhone'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $client->contact_phone; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.WorkEmail'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $client->work_email; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.ContactEmail'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $client->contact_email; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.WorkFax'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $client->work_fax; ?></td>
                        </tr>
                        </tbody>
                    </table>
						</div>
						</div>
                    <div style="clear: both"></div>
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
                                <input type="text" id="default_reference_id" hidden name="reference_id" value="<?php echo e($client->id); ?>">
                                <input type="text" id="default_reference_type" hidden name="reference_type" value="<?php echo e(\App\Comment::$services['client']); ?>">

                                <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;"><?php echo trans('main.Send'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" role='tabpanel' id='contacts-tab'>
                    <div>
                            <?php if($contacts->count()): ?>
                            <table class='table table-bordered' style="width:50%; float:left">
                                <tbody>
                                    <tr>
                                        <td><b><?php echo trans('main.FullName'); ?></b></td>
                                        <td><b><?php echo trans('main.MobilePhone'); ?></b></td>
                                        <td><b><?php echo trans('main.WorkPhone'); ?></b></td>
                                        <td><b><?php echo trans('main.Email'); ?></b></td>
                                    </tr>
                                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($contact->full_name); ?></td>
                                            <td><?php echo $contact->mobile_phone; ?></td>
                                            <td><?php echo $contact->work_phone; ?></td>
                                            <td><?php echo $contact->email; ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                                <h2><?php echo trans('Client dont have contacts'); ?>!</h2>
                            <?php endif; ?>
                        </div>
                </div>
				<div role="tabpanel" class="tab-pane fade" id="billing-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class); ?>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search transactions..." onkeyup="filterTable()">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="exportToCSV()">Export CSV</button>
                            <button type="button" class="btn btn-success" onclick="exportToExcel()">Export Excel</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="transactions-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Tour Name</th>
                                <th>Client Name</th>
                                <th>Amount Receivable</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($transactions) && $transactions->count() > 0): ?>
                                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($transaction->id); ?></td>
                                        <td><?php echo e($transaction->date); ?></td>
                                        <td><?php echo e($transaction->tour_name ?? 'N/A'); ?></td>
                                        <td><?php echo e($transaction->client_name ?? 'N/A'); ?></td>
                                        <td><?php echo e(number_format($transaction->amount_receivable ?? 0, 2)); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($transaction->status == 'paid' ? 'success' : 'warning'); ?>">
                                                <?php echo e(ucfirst($transaction->status ?? 'pending')); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $__env->make('component.action_buttons', [
                                                'routePrefix' => 'accounting',
                                                'item' => $transaction,
                                                'showEdit' => true,
                                                'showDelete' => true,
                                                'showView' => true
                                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No transactions found for this client</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(isset($transactions) && method_exists($transactions, 'links')): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo e($transactions->links()); ?>

                        </div>
                    </div>
                <?php endif; ?>



                </div>
            </div>
</section>
    <span id="services_name" data-service-name='Client' data-history-route="<?php echo e(route('services_history', ['id' => $client->id])); ?>"></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap table functionality
            initializeBootstrapTable('transactions-table');
        });

        // Table search functionality
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('transactions-table');
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

        // Export functions
        function exportToCSV() {
            exportTableToCSV('transactions-table', 'client-transactions.csv');
        }

        function exportToExcel() {
            exportTableToExcel('transactions-table', 'client-transactions');
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/clients/show.blade.php ENDPATH**/ ?>