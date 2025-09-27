<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Events', 'sub_title' => 'Events List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Events', 'icon' => 'map-signs', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                    <div>
                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('event.create'), \App\Event::class); ?>

                    </div>
                <?php if(session('export_all')): ?>
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    <?php echo e(session('export_all')); ?>

                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="events-search" class="form-control" placeholder="Search events..." onkeyup="filterTable('events-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('events-table', 'events_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="events-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff'>
                        <thead>
                        <tr>
                            <th onclick="sortTable(0, 'events-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'events-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'events-table')"><?php echo trans('main.Address'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'events-table')"><?php echo trans('main.Country'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'events-table')"><?php echo trans('main.City'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'events-table')"><?php echo trans('main.WorkPhone'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'events-table')"><?php echo trans('main.ContactEmail'); ?> <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width: 140px"><?php echo trans('main.actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($event->id); ?></td>
                                <td><?php echo e($event->name); ?></td>
                                <td><?php echo e($event->address); ?></td>
                                <td><?php echo e($event->country_name ?? ''); ?></td>
                                <td><?php echo e($event->city_name ?? ''); ?></td>
                                <td><?php echo e($event->work_phone); ?></td>
                                <td><?php echo e($event->contact_email); ?></td>
                                <td>
                                    <?php echo $__env->make('component.action_buttons', [
                                        'show_route' => route('event.show', $event->id),
                                        'edit_route' => route('event.edit', $event->id),
                                        'delete_route' => route('event.destroy', $event->id),
                                        'model' => $event
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">No events found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo e($events->links()); ?>

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
        initializeBootstrapTable('events-table');
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/event/index.blade.php ENDPATH**/ ?>