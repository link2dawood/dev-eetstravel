<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Guides', 'sub_title' => 'Guides List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Guides', 'icon' => 'street-view', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                    <div>
                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('guide.create'), \App\Guide::class); ?>

                    </div>
                <?php if(session('export_all')): ?>
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    <?php echo e(session('export_all')); ?>

                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="guides-search" class="form-control" placeholder="Search guides..." onkeyup="filterTable('guides-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('guides-table', 'guides_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="guides-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff'>
                        <thead>
                          <tr>
                            <th onclick="sortTable(0, 'guides-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'guides-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'guides-table')"><?php echo trans('main.Address'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'guides-table')"><?php echo trans('main.Country'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'guides-table')"><?php echo trans('main.City'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'guides-table')"><?php echo trans('main.WorkPhone'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'guides-table')"><?php echo trans('main.WorkContact'); ?> <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width: 140px"><?php echo trans('main.Actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $guides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($guide->id); ?></td>
                                <td><?php echo e($guide->name); ?></td>
                                <td><?php echo e($guide->address); ?></td>
                                <td><?php echo e($guide->country_name ?? ''); ?></td>
                                <td><?php echo e($guide->city_name ?? ''); ?></td>
                                <td><?php echo e($guide->work_phone); ?></td>
                                <td><?php echo e($guide->work_contact); ?></td>
                                <td>
                                    <?php echo $__env->make('component.action_buttons', [
                                        'show_route' => route('guide.show', $guide->id),
                                        'edit_route' => route('guide.edit', $guide->id),
                                        'delete_route' => route('guide.destroy', $guide->id),
                                        'model' => $guide
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">No guides found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo e($guides->links()); ?>

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
        initializeBootstrapTable('guides-table');
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/guide/index.blade.php ENDPATH**/ ?>