
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.title',
['title' => 'Client Invoices', 'sub_title' => 'Invoices according to tours',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div>
                <div id="tour_create">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class); ?>

                </div>

            </div>
            <?php if(session('message_buses')): ?>
            <div class="alert alert-info col-md-12" style="text-align: center;">
                <?php echo e(session('message_buses')); ?>

            </div>
            <?php endif; ?>
         
            <br>
            <br>

            <div class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="accounting-search" class="form-control" placeholder="Search client invoices..." onkeyup="filterTable('transactions-table', this.value)">
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success btn-sm" onclick="exportTableToCSV('transactions-table', 'client_invoices_export.csv')">
                            <i class="fa fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="transactions-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'transactions-table')">id <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'transactions-table')">Date <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'transactions-table')">Invoice No <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'transactions-table')">Tour Name <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'transactions-table')">Client Name <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'transactions-table')">Amount Receivable <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'transactions-table')">Status <i class="fa fa-sort"></i></th>
                            <th class="actions-button">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $accountingData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($transaction->id); ?></td>
                            <td><?php echo e($transaction->date); ?></td>
                            <td><?php echo e($transaction->invoice_no); ?></td>
                            <td><?php echo e($transaction->tourName); ?></td>
                            <td><?php echo e($transaction->clientName); ?></td>
                            <td><?php echo e($transaction->amount_receiveable); ?></td>
                            <td><?php echo e($transaction->Status); ?></td>
                            <td><?php echo $transaction->action_buttons; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center">No client invoices found</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('transactions-table');
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/accounting/index.blade.php ENDPATH**/ ?>