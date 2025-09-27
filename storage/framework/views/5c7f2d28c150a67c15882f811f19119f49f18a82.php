<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('layouts.title',
   ['title' => 'Restaurants', 'sub_title' => 'Restaurants List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Restaurants', 'icon' => 'coffee', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
            <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
				<div>
					<?php echo \App\Helper\PermissionHelper::getCreateButton(route('restaurant.create'), \App\Restaurant::class); ?>

				</div>
        <?php if(session('export_all')): ?>
          	<div class="alert alert-info col-md-12" style="text-align: center;">
         		<?php echo e(session('export_all')); ?>

            </div>
        <?php endif; ?>
        <div class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="restaurants-search" class="form-control" placeholder="Search restaurants..." onkeyup="filterTable('restaurants-table', this.value)">
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-success btn-sm" onclick="exportTableToCSV('restaurants-table', 'restaurants_export.csv')">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="restaurants-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%; table-layout: fixed;word-break: break-all;'>
                <thead>
                <tr>
                    <th onclick="sortTable(0, 'restaurants-table')">ID <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(1, 'restaurants-table')"><?php echo e(trans('main.Name')); ?> <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(2, 'restaurants-table')"><?php echo e(trans('main.Address')); ?> <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(3, 'restaurants-table')"><?php echo e(trans('main.Country')); ?> <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(4, 'restaurants-table')"><?php echo e(trans('main.City')); ?> <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(5, 'restaurants-table')"><?php echo e(trans('main.Phone')); ?> <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(6, 'restaurants-table')"><?php echo e(trans('main.Email')); ?> <i class="fa fa-sort"></i></th>
                    <th class="actions-button" style="width: 140px"><?php echo e(trans('main.Actions')); ?></th>
                </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($restaurant->id); ?></td>
                        <td><?php echo e($restaurant->name); ?></td>
                        <td><?php echo e($restaurant->address_first); ?></td>
                        <td><?php echo e($restaurant->country_name ?? ''); ?></td>
                        <td><?php echo e($restaurant->city_name ?? ''); ?></td>
                        <td><?php echo e($restaurant->work_phone); ?></td>
                        <td><?php echo e($restaurant->contact_email); ?></td>
                        <td>
                            <?php echo $__env->make('component.action_buttons', [
                                'show_route' => route('restaurant.show', $restaurant->id),
                                'edit_route' => route('restaurant.edit', $restaurant->id),
                                'delete_route' => route('restaurant.destroy', $restaurant->id),
                                'model' => $restaurant
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center">No restaurants found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?php echo e($restaurants->links()); ?>

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
        initializeBootstrapTable('restaurants-table');
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/restaurant/index.blade.php ENDPATH**/ ?>