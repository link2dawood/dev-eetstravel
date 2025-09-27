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
                                <th onclick="sortTable(0, 'current-bookings-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'current-bookings-table')"><?php echo trans('Tour'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'current-bookings-table')"><?php echo trans('Hotel Name'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'current-bookings-table')"><?php echo trans('City'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'current-bookings-table')"><?php echo trans('Status'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'current-bookings-table')"><?php echo trans('Date of Stay'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'current-bookings-table')">SIN <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(7, 'current-bookings-table')">DOU <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(8, 'current-bookings-table')">TRI <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(9, 'current-bookings-table')"><?php echo trans('Cancellation Policy'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(10, 'current-bookings-table')"><?php echo trans('Payments Made'); ?> <i class="fa fa-sort"></i></th>
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
                                <td>-</td> <!-- SIN -->
                                <td>-</td> <!-- DOU -->
                                <td>-</td> <!-- TRI -->
                                <td><?php echo e($booking->cancel_policy); ?></td>
                                <td><?php echo e($booking->payment_policy); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="11" class="text-center">No current bookings found</td>
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
    // Initialize Bootstrap table
    initializeBootstrapTable('current-bookings-table');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/offers/current_bookings.blade.php ENDPATH**/ ?>