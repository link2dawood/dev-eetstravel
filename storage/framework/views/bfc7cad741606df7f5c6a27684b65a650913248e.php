<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Currencies', 'sub_title' => 'Currencies List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Currencies', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('currencies.create'), \App\Currencies::class); ?>

                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="currencies-search" class="form-control" placeholder="Search currencies..." onkeyup="filterTable('currencies-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('currencies-table', 'currencies_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="currencies-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'currencies-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'currencies-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'currencies-table')"><?php echo trans('main.Code'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'currencies-table')"><?php echo trans('main.Symbol'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'currencies-table')"><?php echo trans('main.Cent'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'currencies-table')"><?php echo trans('main.SymbolCent'); ?> <i class="fa fa-sort"></i></th>
                                <th><?php echo trans('main.Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($currency->id); ?></td>
                                <td><?php echo e($currency->name ?? ''); ?></td>
                                <td><?php echo e($currency->code ?? ''); ?></td>
                                <td><?php echo e($currency->symbol ?? ''); ?></td>
                                <td><?php echo e($currency->cent ?? ''); ?></td>
                                <td><?php echo e($currency->symbol_cent ?? ''); ?></td>
                                <td>
                                    <?php echo \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('currencies.show', ['currency' => $currency->id]),
                                        'edit' => route('currencies.edit', ['currency' => $currency->id]),
                                        'delete_msg' => "/currencies/{$currency->id}/deleteMsg"
                                    ], false, $currency); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">No currencies found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo e($currencies->links()); ?>

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
    initializeBootstrapTable('currencies-table');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/currencies/index.blade.php ENDPATH**/ ?>