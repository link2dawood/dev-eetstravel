<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Room Types', 'sub_title' => 'Room Types List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Room Types', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('room_types.create'), \App\RoomTypes::class); ?>

                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="room-types-search" class="form-control" placeholder="Search room types..." onkeyup="filterTable('room-types-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('room-types-table', 'room_types_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="room-types-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'room-types-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'room-types-table')"><?php echo e(trans('main.Name')); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'room-types-table')"><?php echo e(trans('main.Code')); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'room-types-table')"><?php echo e(trans('main.Sortorder')); ?> <i class="fa fa-sort"></i></th>
                                <th><?php echo e(trans('main.Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($room_type->id); ?></td>
                                <td><?php echo e($room_type->name ?? ''); ?></td>
                                <td><?php echo e($room_type->code ?? ''); ?></td>
                                <td><?php echo e($room_type->sort_order ?? ''); ?></td>
                                <td>
                                    <?php echo \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('room_types.show', ['room_type' => $room_type->id]),
                                        'edit' => route('room_types.edit', ['room_type' => $room_type->id]),
                                        'delete_msg' => "/room_types/{$room_type->id}/deleteMsg"
                                    ], false, $room_type); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">No room types found</td>
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
    initializeBootstrapTable('room-types-table');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/room_types/index.blade.php ENDPATH**/ ?>