<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Bus Company', 'sub_title' => 'Bus Company List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Bus Company', 'icon' => 'exchange', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                    <div>
                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('transfer.create'), \App\Transfer::class); ?>

                    </div>
                <?php if(session('export_all')): ?>
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    <?php echo e(session('export_all')); ?>

                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="transfer-search" class="form-control" placeholder="Search transfers..." onkeyup="filterTable('transfer-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('transfer-table', 'transfers_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="transfer-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'transfer-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'transfer-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'transfer-table')"><?php echo trans('main.Address'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'transfer-table')"><?php echo trans('main.Country'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'transfer-table')"><?php echo trans('main.City'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'transfer-table')"><?php echo trans('main.Phone'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'transfer-table')"><?php echo trans('main.Contact'); ?> <i class="fa fa-sort"></i></th>
                                <th><?php echo trans('main.Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $transfers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($transfer->id); ?></td>
                                <td><?php echo e($transfer->name ?? ''); ?></td>
                                <td><?php echo e($transfer->address_first ?? ''); ?></td>
                                <td><?php echo e($transfer->country_name ?? ''); ?></td>
                                <td><?php echo e($transfer->city_name ?? ''); ?></td>
                                <td><?php echo e($transfer->work_phone ?? ''); ?></td>
                                <td><?php echo e($transfer->contact_name ?? ''); ?></td>
                                <td>
                                    <?php echo \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('transfer.show', ['transfer' => $transfer->id]),
                                        'edit' => route('transfer.edit', ['transfer' => $transfer->id]),
                                        'delete_msg' => "/transfer/{$transfer->id}/deleteMsg"
                                    ], false, $transfer); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">No transfers found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo e($transfers->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <span id="service-name" hidden data-service-name='Transfer'></span>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('transfer-table');
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/transfer/index.blade.php ENDPATH**/ ?>