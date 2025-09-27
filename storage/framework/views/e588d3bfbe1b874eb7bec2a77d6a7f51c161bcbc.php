<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.title',
       ['title' => 'Past Offers', 'sub_title' => 'Offer List',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Currencies', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <br><br>
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="past-offers-search" class="form-control" placeholder="Search past offers..." onkeyup="filterTable('past-offers-table', this.value)">
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success btn-sm" onclick="exportTableToCSV('past-offers-table', 'past_offers_export.csv')">
                            <i class="fa fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="past-offers-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%; table-layout: fixed'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'past-offers-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'past-offers-table')"><?php echo trans('Tour Name'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'past-offers-table')"><?php echo trans('City'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'past-offers-table')"><?php echo trans('Status'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'past-offers-table')"><?php echo trans('Departure Date'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'past-offers-table')"><?php echo trans('Return Date'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'past-offers-table')"><?php echo trans('PAX'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(7, 'past-offers-table')"><?php echo trans('Created At'); ?> <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($tour->id); ?></td>
                            <td><?php echo e($tour->name); ?></td>
                            <td><?php echo e($tour->city ? $tour->city->name : ''); ?></td>
                            <td>
                                <span class="badge badge-primary" style="background-color: <?php echo e($tour->getStatusColor()); ?>">
                                    <?php echo e($tour->getStatusName()); ?>

                                </span>
                            </td>
                            <td><?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : ''); ?></td>
                            <td><?php echo e($tour->retirement_date ? \Carbon\Carbon::parse($tour->retirement_date)->format('Y-m-d') : ''); ?></td>
                            <td><?php echo e($tour->pax); ?></td>
                            <td><?php echo e($tour->created_at ? $tour->created_at->format('Y-m-d H:i') : ''); ?></td>
                            <td>
                                <?php echo $__env->make('component.action_buttons', [
                                    'show_route' => route('tour.show', ['tour' => $tour->id]),
                                    'edit_route' => route('tour.edit', ['tour' => $tour->id]),
                                    'delete_route' => route('tour.destroy', $tour->id),
                                    'model' => $tour
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center">No past offers found</td>
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
        initializeBootstrapTable('past-offers-table');
    });
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/offers/past_offers.blade.php ENDPATH**/ ?>