<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
<style>
.action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px;
}

.action-btn svg {
    width: 20px;
    height: 20px;
}

.action-btn.show svg {
    stroke: #3b82f6;
}

.action-btn.edit svg {
    stroke: #f59e0b;
}

.action-btn.delete svg {
    stroke: #ef4444;
}

.action-btn:hover {
    transform: scale(1.15);
}

.action-btn.show:hover {
    background-color: rgba(59, 130, 246, 0.1);
}

.action-btn.edit:hover {
    background-color: rgba(245, 158, 11, 0.1);
}

.action-btn.delete:hover {
    background-color: rgba(239, 68, 68, 0.1);
}
</style>

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
                            <div class="action-buttons">
                                <a href="<?php echo e(route('restaurant.show', $restaurant->id)); ?>" class="action-btn show" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="12" r="2" />
                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                    </svg>
                                </a>
                                <a href="<?php echo e(route('restaurant.edit', $restaurant->id)); ?>" class="action-btn edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </a>
                                <a href="#" onclick="confirmRestaurantDelete(event, '<?php echo e(route('restaurant.destroy', $restaurant->id)); ?>')" class="action-btn delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                    </svg>
                                </a>
                            </div>
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

    function confirmRestaurantDelete(event, deleteUrl) {
        event.preventDefault();
        event.stopPropagation();
        
        if (confirm("Are you sure you want to delete this restaurant?")) {
            const form = document.createElement('form');
            form.action = deleteUrl;
            form.method = 'POST';
            form.style.display = 'none';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/restaurant/index.blade.php ENDPATH**/ ?>