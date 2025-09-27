<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('layouts.title',
   ['title' => 'Hotels', 'sub_title' => 'Hotels List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Hotels', 'icon' => 'hotel', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <br>
            <div class="col-lg-12">
                <div class="alert alert-danger" id="errors_message" style="text-align: center; display: none">
                </div>
            </div>
            <div class="box-body">
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('hotel.create'), \App\Hotel::class); ?>

                </div>
                <?php if(session('export_all')): ?>
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    <?php echo e(session('export_all')); ?>

                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="hotels-search" class="form-control" placeholder="Search hotels..." onkeyup="filterTable('hotels-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('hotels-table', 'hotels_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="hotels-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff;width: 98%;'>
                        <thead>
                          <tr>
                            <th onclick="sortTable(0, 'hotels-table')" style="width: 1%;">Id <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'hotels-table')" style="width: 10%;"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'hotels-table')" style="width: 10%;"><?php echo trans('main.Address'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'hotels-table')" style="width: 10%;"><?php echo trans('main.Country'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'hotels-table')" style="width: 10%;"><?php echo trans('main.City'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'hotels-table')" style="width: 10%;"><?php echo trans('main.WorkPhone'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'hotels-table')" style="width: 10%;"><?php echo trans('main.ContactEmail'); ?> <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width:150px; text-align: center;"><?php echo trans('main.Actions'); ?></th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($hotel->id); ?></td>
                                <td><?php echo e($hotel->name); ?></td>
                                <td><?php echo e($hotel->address); ?></td>
                                <td><?php echo e($hotel->country_name ?? ''); ?></td>
                                <td><?php echo e($hotel->city_name ?? ''); ?></td>
                                <td><?php echo e($hotel->work_phone); ?></td>
                                <td><?php echo e($hotel->contact_email); ?></td>
                                <td>
                                    <?php echo $__env->make('component.action_buttons', [
                                        'show_route' => route('hotel.show', $hotel->id),
                                        'edit_route' => route('hotel.edit', $hotel->id),
                                        'delete_route' => route('hotel.destroy', $hotel->id),
                                        'model' => $hotel
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hotels found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo e($hotels->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('hotels-table');
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/hotel/index.blade.php ENDPATH**/ ?>