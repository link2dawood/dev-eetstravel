<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Rates', 'sub_title' => 'Rates List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Rates', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                    <div>
                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('rate.create'), \App\Rate::class); ?>

                    </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="rate-search" class="form-control" placeholder="Search rates..." onkeyup="filterTable('rate-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('rate-table', 'rates_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="rate-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'rate-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'rate-table')"><?php echo e(trans('main.Name')); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'rate-table')"><?php echo e(trans('main.Mark')); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'rate-table')"><?php echo e(trans('main.Ratetype')); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'rate-table')"><?php echo e(trans('main.Sortorder')); ?> <i class="fa fa-sort"></i></th>
                                <th><?php echo e(trans('main.Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($rate->id); ?></td>
                                <td><?php echo e($rate->name ?? ''); ?></td>
                                <td><?php echo e($rate->mark ?? ''); ?></td>
                                <td><?php echo e($rate->rate_type ?? ''); ?></td>
                                <td><?php echo e($rate->sort_order ?? ''); ?></td>
                                <td>
                                    <?php echo \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('rate.show', ['rate' => $rate->id]),
                                        'edit' => route('rate.edit', ['rate' => $rate->id]),
                                        'delete_msg' => "/rate/{$rate->id}/deleteMsg"
                                    ], false, $rate); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">No rates found</td>
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
$(document).ready(function() {
    initializeBootstrapTable('rate-table');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/rate/index.blade.php ENDPATH**/ ?>