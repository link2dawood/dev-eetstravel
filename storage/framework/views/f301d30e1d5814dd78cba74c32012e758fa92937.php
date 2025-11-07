
<?php $__env->startSection('title','Clients'); ?>
<?php $__env->startSection('content'); ?>
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(url('/home')); ?>"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Clients</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">Clients</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('clients.create'), \App\Client::class); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Clients List</h3>
                            <div class="col-auto ms-auto">
                                <div class="btn-list">
                                    <button class="btn btn-success btn-sm" onclick="exportTableToCSV('clients-table', 'clients_export.csv')">
                                        <i class="ti ti-download"></i> Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <input type="text" id="clients-search" class="form-control" placeholder="Search clients..." onkeyup="filterTable('clients-table', this.value)">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="clients-table" class="table card-table table-vcenter text-nowrap datatable" style='background:#fff; width: 100%;'>
                                <thead>
                                    <tr>
                                        <th onclick="sortTable(0, 'clients-table')">ID <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(1, 'clients-table')"><?php echo trans('main.Name'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(2, 'clients-table')"><?php echo trans('main.Country'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(3, 'clients-table')"><?php echo trans('main.City'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(4, 'clients-table')"><?php echo trans('main.Address'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(5, 'clients-table')"><?php echo trans('Account No'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(6, 'clients-table')"><?php echo trans('Company Address'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(7, 'clients-table')"><?php echo trans('Invoice Address'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(8, 'clients-table')"><?php echo trans('main.WorkPhone'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(9, 'clients-table')"><?php echo trans('main.WorkEmail'); ?> <i class="ti ti-arrows-sort"></i></th>
                                        <th class="actions-button" style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($client->id); ?></td>
                                        <td><?php echo e($client->name); ?></td>
                                        <td><?php echo e($client->country_name ?? ''); ?></td>
                                        <td><?php echo e($client->city_name ?? ''); ?></td>
                                        <td><?php echo e($client->address); ?></td>
                                        <td><?php echo e($client->account_no); ?></td>
                                        <td><?php echo e($client->company_address); ?></td>
                                        <td><?php echo e($client->invoice_address); ?></td>
                                        <td><?php echo e($client->work_phone); ?></td>
                                        <td><?php echo e($client->work_email); ?></td>
                                        <td>
                                            <?php echo $__env->make('component.action_buttons', [
                                                'show_route' => route('clients.show', $client->id),
                                                'edit_route' => route('clients.edit', $client->id),
                                                'delete_route' => route('clients.destroy', $client->id),
                                                'model' => $client
                                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="11" class="text-center">No clients found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            <?php echo e($clients->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="service-name" hidden data-service-name='Event'></span>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('clients-table');
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/clients/index.blade.php ENDPATH**/ ?>