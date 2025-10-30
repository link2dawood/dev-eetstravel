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
           ['title' => 'Buses', 'sub_title' => 'Buses List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Bus', 'icon' => 'bus', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('bus.create'), \App\Bus::class); ?>

                </div>
                <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                    <?php echo $__env->make('legend.buses_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </span>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="bus-search" class="form-control" placeholder="Search buses..." onkeyup="filterTable('bus-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('bus-table', 'buses_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="bus-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'bus-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'bus-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'bus-table')"><?php echo trans('main.Busnumber'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'bus-table')"><?php echo trans('main.BusCompany'); ?> <i class="fa fa-sort"></i></th>
                                <th><?php echo trans('main.Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($bus->id); ?></td>
                                <td><?php echo e($bus->name ?? ''); ?></td>
                                <td><?php echo e($bus->bus_number ?? ''); ?></td>
                                <td><?php echo e($bus->transfer_name ?? ''); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?php echo e(route('bus.show', ['bu' => $bus->id])); ?>" class="action-btn show" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        <a href="<?php echo e(route('bus.edit', ['bu' => $bus->id])); ?>" class="action-btn edit" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        <a href="#" onclick="confirmBusDelete(event, '/bus/<?php echo e($bus->id); ?>/deleteMsg')" class="action-btn delete" title="Delete">
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
                                <td colspan="5" class="text-center">No buses found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo e($buses->links()); ?>

                    </div>
                </div>
            </div>
        </div>

    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('bus-table');
});

function confirmBusDelete(event, deleteUrl) {
    event.preventDefault();
    event.stopPropagation();
    
    if (confirm("Are you sure you want to delete this bus?")) {
        // Navigate to the delete message URL
        window.location.href = deleteUrl;
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/bus/index.blade.php ENDPATH**/ ?>