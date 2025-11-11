
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.title',
['title' => 'Supplier Invoices', 'sub_title' => 'Invoice List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div>
                <div id="tour_create">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class); ?>

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
                        <input type="text" id="invoices-search" class="form-control" placeholder="Search invoices..." onkeyup="filterTable('inovices-table', this.value)">
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success btn-sm" onclick="exportTableToCSV('inovices-table', 'supplier_invoices_export.csv')">
                            <i class="fa fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>

			<div class="table-responsive">
            	<table id="inovices-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
					<thead>
						<tr>
							<th onclick="sortTable(0, 'inovices-table')">id <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(1, 'inovices-table')">Invoice No <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(2, 'inovices-table')">Due Date <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(3, 'inovices-table')">Received Date <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(4, 'inovices-table')">Tour <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(5, 'inovices-table')">Service <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(6, 'inovices-table')">Office Name <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(7, 'inovices-table')">Total Price <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(8, 'inovices-table')">Status <i class="fa fa-sort"></i></th>
							<th class="actions-button">Actions</th>
						</tr>
					</thead>
					<tbody>
					<?php $__empty_1 = true; $__currentLoopData = $invoicesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
						<tr>
							<td><?php echo e($invoice->id); ?></td>
							<td><?php echo e($invoice->invoice_no); ?></td>
							<td><?php echo e($invoice->dueDate); ?></td>
							<td><?php echo e($invoice->receivedDate); ?></td>
							<td><?php echo e($invoice->tour); ?></td>
							<td><?php echo e($invoice->package); ?></td>
							<td><?php echo e($invoice->officeName); ?></td>
							<td><?php echo e($invoice->total_amount); ?></td>
							<td><?php echo e($invoice->status); ?></td>
							<td><?php echo $invoice->action_buttons; ?></td>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
						<tr>
							<td colspan="10" class="text-center">No invoices found</td>
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
        initializeBootstrapTable('inovices-table');
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/invoices/index.blade.php ENDPATH**/ ?>