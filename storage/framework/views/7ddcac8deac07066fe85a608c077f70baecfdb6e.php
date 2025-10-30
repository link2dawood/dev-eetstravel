<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
        ['title' => 'Current Bookings', 'sub_title' => 'Offer List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Currencies', 'icon' => null, 'route' => null]
        ]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="current-bookings-search" class="form-control" placeholder="Search current bookings..." onkeyup="filterTable('current-bookings-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('current-bookings-table', 'current_bookings_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="current-bookings-table" class="table table-striped table-bordered table-hover bootstrap-table" style="background:#fff; width: 100%;">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'current-bookings-table')" style="width: 60px;">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'current-bookings-table')"><?php echo trans('Tour'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'current-bookings-table')"><?php echo trans('Hotel Name'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'current-bookings-table')"><?php echo trans('City'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'current-bookings-table')"><?php echo trans('Status'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'current-bookings-table')"><?php echo trans('Date of Stay'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'current-bookings-table')" style="width: 60px;">SIN <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(7, 'current-bookings-table')" style="width: 60px;">DOU <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(8, 'current-bookings-table')" style="width: 60px;">TRI <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(9, 'current-bookings-table')" style="width: 150px;"><?php echo trans('Cancellation Policy'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(10, 'current-bookings-table')" style="width: 200px;"><?php echo trans('Payments Made'); ?> <i class="fa fa-sort"></i></th>
                                <th class="actions-button" style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $processedBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($booking->id); ?></td>
                                <td><?php echo e($booking->tour_name); ?></td>
                                <td><?php echo e($booking->hotel_name); ?></td>
                                <td><?php echo e($booking->city_name); ?></td>
                                <td><?php echo e($booking->status_name); ?></td>
                                <td><?php echo e($booking->stay_date); ?></td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td>
                                    <div style="max-width: 150px; white-space: normal; word-wrap: break-word;">
                                        <?php echo e($booking->cancel_policy); ?>

                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 200px; white-space: normal; word-wrap: break-word;">
                                        <?php echo e($booking->payment_policy); ?>

                                    </div>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <?php echo $__env->make('component.action_buttons', [
                                        'item' => $booking,
                                        'routePrefix' => 'tour'
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="12" class="text-center">No current bookings found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Action Buttons Container */
.action-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

/* Individual Action Button */
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    padding: 0;
    border: 2px solid;
    border-radius: 8px;
    background-color: transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0;
    text-decoration: none;
}

.btn-action:hover {
    transform: translateY(-2px);
}

.btn-action svg {
    width: 20px;
    height: 20px;
}

/* Edit Button */
.edit-action {
    color: #f9a825;
    border-color: #f9a825;
}

.edit-action:hover {
    background-color: #f9a825;
    color: white;
}

/* Delete Button */
.delete-action {
    color: #ef5350;
    border-color: #ef5350;
}

.delete-action:hover {
    background-color: #ef5350;
    color: white;
}

/* Table Cell Text Wrapping */
.table td {
    vertical-align: middle;
}

/* Improved column width control */
#current-bookings-table th:nth-child(11),
#current-bookings-table td:nth-child(11) {
    min-width: 200px;
    max-width: 250px;
}

#current-bookings-table th:nth-child(10),
#current-bookings-table td:nth-child(10) {
    min-width: 150px;
    max-width: 180px;
}

/* Better text display in long columns */
.table td > div {
    line-height: 1.4;
}

/* Actions column */
#current-bookings-table th:nth-child(12),
#current-bookings-table td:nth-child(12) {
    text-align: center;
}

.actions-cell {
    padding: 12px 8px !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
$(document).ready(function() {
    // Initialize Bootstrap table
    initializeBootstrapTable('current-bookings-table');
    
    // Delete button click handler
    $(document).on('click', '.delete-action', function(e) {
        e.preventDefault();
        var link = $(this).data('link');
        $(this).attr('data-link', link);
        $(this).trigger('click');
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/offers/current_bookings.blade.php ENDPATH**/ ?>