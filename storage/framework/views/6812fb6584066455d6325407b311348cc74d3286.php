<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Clients', 'sub_title' => 'Clients List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Clients', 'icon' => 'handshake-o', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('clients.create'), \App\Client::class); ?>

                </div>
                <br>
                <br>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="clients-search" class="form-control" placeholder="Search clients..." onkeyup="filterTable('clients-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('clients-table', 'clients_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                <table id="clients-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'clients-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'clients-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'clients-table')"><?php echo trans('main.Country'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'clients-table')"><?php echo trans('main.City'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'clients-table')"><?php echo trans('main.Address'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'clients-table')"><?php echo trans('Account No'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'clients-table')"><?php echo trans('Company Address'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(7, 'clients-table')"><?php echo trans('Invoice Address'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(8, 'clients-table')"><?php echo trans('main.WorkPhone'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(9, 'clients-table')"><?php echo trans('main.WorkEmail'); ?> <i class="fa fa-sort"></i></th>
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

                <div class="row">
                    <div class="col-md-12">
                        <?php echo e($clients->links()); ?>

                    </div>
                </div>
				</div>
                <span id="service-name" hidden data-service-name='Event'></span>
            </div>
        </div>
    </section>
    
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('clients-table');
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/clients/index.blade.php ENDPATH**/ ?>