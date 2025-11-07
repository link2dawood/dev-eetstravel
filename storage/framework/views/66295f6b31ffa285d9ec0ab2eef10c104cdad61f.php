
<?php $__env->startSection('title', 'Bus Companies'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xl">
    
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Service Management
                </div>
                <h2 class="page-title">
                    <i class="ti ti-bus me-2"></i>Bus Companies
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('transfer.create'), \App\Transfer::class, 'btn btn-primary'); ?>

                </div>
            </div>
        </div>
    </div>

    
    <?php if(Session::has('message')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-alert-circle me-2"></i>
                </div>
                <div class="flex-fill">
                    <?php echo e(Session::get('message')); ?>

                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('export_all')): ?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-info-circle me-2"></i>
                </div>
                <div class="flex-fill">
                    <?php echo e(session('export_all')); ?>

                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bus Company List</h3>
        </div>
        <div class="card-body">
            
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" 
                               id="transfer-search" 
                               class="form-control" 
                               placeholder="Search transfers by name, city, country..."
                               onkeyup="filterTable('transfer-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" 
                            onclick="exportTableToCSV('transfer-table', 'transfers_export.csv')">
                        <i class="ti ti-download me-1"></i>
                        <span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>

            
            <div class="table-responsive">
                <table id="transfer-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60px;" onclick="sortTable(0, 'transfer-table')" class="cursor-pointer">
                                ID <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th onclick="sortTable(1, 'transfer-table')" class="cursor-pointer">
                                <?php echo trans('main.Name'); ?> <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(2, 'transfer-table')" class="cursor-pointer">
                                <?php echo trans('main.Address'); ?> <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(3, 'transfer-table')" class="cursor-pointer">
                                <?php echo trans('main.Country'); ?> <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(4, 'transfer-table')" class="cursor-pointer">
                                <?php echo trans('main.City'); ?> <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-sm-table-cell" onclick="sortTable(5, 'transfer-table')" class="cursor-pointer">
                                <?php echo trans('main.Phone'); ?> <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-xl-table-cell" onclick="sortTable(6, 'transfer-table')" class="cursor-pointer">
                                <?php echo trans('main.Contact'); ?> <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="text-end"><?php echo trans('main.Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transfers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="text-muted">#<?php echo e($transfer->id); ?></span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold"><?php echo e($transfer->name ?? '—'); ?></span>
                                    <small class="text-muted d-md-none"><?php echo e($transfer->city_name ?? ''); ?></small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="text-muted"><?php echo e($transfer->address_first ?? '—'); ?></span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="text-muted"><?php echo e($transfer->country_name ?? '—'); ?></span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="text-muted"><?php echo e($transfer->city_name ?? '—'); ?></span>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="text-muted"><?php echo e($transfer->work_phone ?? '—'); ?></span>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <span class="text-muted"><?php echo e($transfer->contact_name ?? '—'); ?></span>
                            </td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    <?php echo $__env->make('component.action_buttons', ['item' => $transfer, 'routePrefix' => 'transfer'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-bus icon" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="empty-title">No bus companies found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first bus company</p>
                                    <div class="empty-action">
                                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('transfer.create'), \App\Transfer::class, 'btn btn-primary'); ?>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($transfers->hasPages()): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing <?php echo e($transfers->firstItem()); ?> to <?php echo e($transfers->lastItem()); ?> of <?php echo e($transfers->total()); ?> entries
                </div>
                <div>
                    <?php echo e($transfers->links()); ?>

                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<span id="service-name" hidden data-service-name='Transfer'></span>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('transfer-table');
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/transfer/index.blade.php ENDPATH**/ ?>