<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Statuses', 'sub_title' => 'Statuses List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Statuses', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">

            <br>
            <div class="col-lg-12">
                <div class="alert alert-danger" id="errors_message" style="text-align: center; display: none">
                </div>
            </div>


            <div class="box-body">
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('status.create'), \App\Status::class); ?>

                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="status-search" class="form-control" placeholder="Search statuses..." onkeyup="filterTable('status-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('status-table', 'statuses_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="status-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'status-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'status-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'status-table')"><?php echo trans('main.Type'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'status-table')"><?php echo trans('main.SortOrder'); ?> <i class="fa fa-sort"></i></th>
                                <th><?php echo trans('main.Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($statusItem->id); ?></td>
                                <td><?php echo e($statusItem->name ?? ''); ?></td>
                                <td><?php echo e($statusItem->status_type ?? ''); ?></td>
                                <td><?php echo e($statusItem->sort_order ?? ''); ?></td>
                                <td>
                                    <?php echo \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('status.show', ['status' => $statusItem->id]),
                                        'edit' => route('status.edit', ['status' => $statusItem->id]),
                                        'delete_msg' => "/status/{$statusItem->id}/deleteMsg"
                                    ], false, $statusItem); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">No statuses found</td>
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
    initializeBootstrapTable('status-table');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/status/index.blade.php ENDPATH**/ ?>