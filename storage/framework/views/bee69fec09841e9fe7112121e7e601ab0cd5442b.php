
<?php $__env->startSection('title', 'Restaurants'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Service Management</div>
                <h2 class="page-title"><i class="ti ti-tools-kitchen-2 me-2"></i>Restaurants</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <?php echo \App\Helper\PermissionHelper::getCreateButton(route('restaurant.create'), \App\Restaurant::class, 'btn btn-primary'); ?>

            </div>
        </div>
    </div>

    <?php if(Session::has('message')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex"><div><i class="ti ti-alert-circle me-2"></i></div><div class="flex-fill"><?php echo e(Session::get('message')); ?></div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    <?php endif; ?>
    <?php if(session('export_all')): ?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex"><div><i class="ti ti-info-circle me-2"></i></div><div class="flex-fill"><?php echo e(session('export_all')); ?></div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Restaurants List</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="restaurants-search" class="form-control" placeholder="Search restaurants..." onkeyup="filterTable('restaurants-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('restaurants-table', 'restaurants_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="restaurants-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" onclick="sortTable(0, 'restaurants-table')" class="cursor-pointer">ID <i class="ti ti-arrows-sort"></i></th>
                            <th onclick="sortTable(1, 'restaurants-table')" class="cursor-pointer"><?php echo trans('main.Name'); ?> <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(2, 'restaurants-table')" class="cursor-pointer"><?php echo trans('main.Address'); ?> <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(3, 'restaurants-table')" class="cursor-pointer"><?php echo trans('main.Country'); ?> <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(4, 'restaurants-table')" class="cursor-pointer"><?php echo trans('main.City'); ?> <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell" onclick="sortTable(5, 'restaurants-table')" class="cursor-pointer"><?php echo trans('main.WorkPhone'); ?> <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-xl-table-cell" onclick="sortTable(6, 'restaurants-table')" class="cursor-pointer"><?php echo trans('main.ContactEmail'); ?> <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end"><?php echo trans('main.Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><span class="text-muted">#<?php echo e($restaurant->id); ?></span></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold"><?php echo e($restaurant->name); ?></span>
                                    <small class="text-muted d-lg-none"><?php echo e($restaurant->city_name ?? ''); ?></small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell"><span class="text-muted"><?php echo e($restaurant->address ?? '—'); ?></span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted"><?php echo e($restaurant->country_name ?? '—'); ?></span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted"><?php echo e($restaurant->city_name ?? '—'); ?></span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted"><?php echo e($restaurant->work_phone ?? '—'); ?></span></td>
                            <td class="d-none d-xl-table-cell"><span class="text-muted"><?php echo e($restaurant->contact_email ?? '—'); ?></span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    <?php echo $__env->make('component.action_buttons', ['item' => $restaurant, 'routePrefix' => 'restaurant'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon"><i class="ti ti-tools-kitchen-2 icon" style="font-size: 3rem;"></i></div>
                                    <p class="empty-title">No restaurants found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first restaurant</p>
                                    <div class="empty-action">
                                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('restaurant.create'), \App\Restaurant::class, 'btn btn-primary'); ?>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($restaurants->hasPages()): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing <?php echo e($restaurants->firstItem()); ?> to <?php echo e($restaurants->lastItem()); ?> of <?php echo e($restaurants->total()); ?> entries</div>
                <div><?php echo e($restaurants->links()); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<span id="service-name" hidden data-service-name='Restaurant'></span>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>$(document).ready(function() { initializeBootstrapTable('restaurants-table'); });</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/restaurant/index.blade.php ENDPATH**/ ?>