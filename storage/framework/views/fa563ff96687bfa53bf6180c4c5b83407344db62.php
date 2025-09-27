<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Drivers', 'sub_title' => 'Drivers List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Drivers', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                    <div>
                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('driver.create'), \App\Driver::class); ?>

                    </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="driver-search" class="form-control" placeholder="Search drivers..." onkeyup="filterTable('driver-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('driver-table', 'drivers_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="driver-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'driver-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'driver-table')">Name <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'driver-table')">Phone <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'driver-table')">Email <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'driver-table')">Bus Company <i class="fa fa-sort"></i></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($driver->id); ?></td>
                                <td><?php echo e($driver->name ?? ''); ?></td>
                                <td><?php echo e($driver->phone ?? ''); ?></td>
                                <td><?php echo e($driver->email ?? ''); ?></td>
                                <td><?php echo e($driver->transfer_name ?? ''); ?></td>
                                <td>
                                    <?php echo \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('driver.show', ['driver' => $driver->id]),
                                        'edit' => route('driver.edit', ['driver' => $driver->id]),
                                        'delete_msg' => "/driver/{$driver->id}/delete_msg"
                                    ], false, $driver); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">No drivers found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <span id="service-name" hidden data-service-name='Driver'></span>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('driver-table');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/driver/index.blade.php ENDPATH**/ ?>