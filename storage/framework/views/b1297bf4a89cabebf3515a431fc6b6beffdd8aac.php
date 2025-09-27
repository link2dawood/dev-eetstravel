<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Buses', 'sub_title' => 'Buses List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Bus', 'icon' => 'bus', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('bus.create'), \App\Bus::class); ?>

                </div>
                <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                    <?php echo $__env->make('legend.buses_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </span>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="bus-search" class="form-control" placeholder="Search buses..." onkeyup="filterTable('bus-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('bus-table', 'buses_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="bus-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'bus-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'bus-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'bus-table')"><?php echo trans('main.Busnumber'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'bus-table')"><?php echo trans('main.BusCompany'); ?> <i class="fa fa-sort"></i></th>
                                <th><?php echo trans('main.Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($bus->id); ?></td>
                                <td><?php echo e($bus->name ?? ''); ?></td>
                                <td><?php echo e($bus->bus_number ?? ''); ?></td>
                                <td><?php echo e($bus->transfer_name ?? ''); ?></td>
                                <td>
                                    <?php echo \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('bus.show', ['bu' => $bus->id]),
                                        'edit' => route('bus.edit', ['bu' => $bus->id]),
                                        'delete_msg' => "/bus/{$bus->id}/deleteMsg"
                                    ], false, $bus); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">No buses found</td>
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
    initializeBootstrapTable('bus-table');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/bus/index.blade.php ENDPATH**/ ?>