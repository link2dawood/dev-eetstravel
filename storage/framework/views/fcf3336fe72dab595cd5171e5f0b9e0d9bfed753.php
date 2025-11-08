
<?php $__env->startSection('title', 'Buses'); ?>

<?php $__env->startSection('content'); ?>
<!-- Delete Confirmation Modal -->
<div class="modal modal-blur fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle icon mb-2 text-danger icon-lg"></i>
                <h3>Are you sure?</h3>
                <div class="text-muted" id="delete-message">Do you really want to delete this record?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                        <div class="col">
                            <form id="deleteForm" method="GET" style="display: inline;">
                                <button type="submit" class="btn btn-danger w-100">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Fleet Management</div>
                <h2 class="page-title"><i class="ti ti-bus me-2"></i>Buses</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <?php echo \App\Helper\PermissionHelper::getCreateButton(route('bus.create'), \App\Bus::class, 'btn btn-primary'); ?>

            </div>
        </div>
    </div>

    <?php if(Session::has('message')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex"><div><i class="ti ti-alert-circle me-2"></i></div><div class="flex-fill"><?php echo e(Session::get('message')); ?></div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Buses List</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="bus-search" class="form-control" placeholder="Search buses..." onkeyup="filterTable('bus-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('bus-table', 'buses_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="bus-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" onclick="sortTable(0, 'bus-table')" class="cursor-pointer">ID <i class="ti ti-arrows-sort"></i></th>
                            <th onclick="sortTable(1, 'bus-table')" class="cursor-pointer">License Plate <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(2, 'bus-table')" class="cursor-pointer">Bus Company <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(3, 'bus-table')" class="cursor-pointer">Seats <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell" onclick="sortTable(4, 'bus-table')" class="cursor-pointer">Type <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><span class="text-muted">#<?php echo e($bus->id); ?></span></td>
                            <td><span class="fw-bold"><?php echo e($bus->license_plate ?? '—'); ?></span></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted"><?php echo e($bus->transfer_name ?? '—'); ?></span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted"><?php echo e($bus->seats ?? '—'); ?></span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted"><?php echo e($bus->type ?? '—'); ?></span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    <?php echo $__env->make('component.action_buttons', ['item' => $bus, 'routePrefix' => 'bus'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon"><i class="ti ti-bus icon" style="font-size: 3rem;"></i></div>
                                    <p class="empty-title">No buses found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first bus</p>
                                    <div class="empty-action">
                                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('bus.create'), \App\Bus::class, 'btn btn-primary'); ?>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($buses->hasPages()): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing <?php echo e($buses->firstItem()); ?> to <?php echo e($buses->lastItem()); ?> of <?php echo e($buses->total()); ?> entries</div>
                <div><?php echo e($buses->links()); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('bus-table');

    // Handle delete button click
    $('.delete').on('click', function(e) {
        e.preventDefault();
        var link = $(this).data('link') || '';

        if (link.indexOf('deleteMsg') !== -1) {
            // Fetch confirmation message HTML (if controller returns it)
            $.get(link, function(response) {
                $('#delete-message').html(response);
                var deleteUrl = link.replace('deleteMsg', 'delete');
                $('#deleteForm').attr('action', deleteUrl);
            });
        } else {
            $('#deleteForm').attr('action', link);
        }

        var myModal = new bootstrap.Modal(document.getElementById('myModal'));
        myModal.show();
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/bus/index.blade.php ENDPATH**/ ?>