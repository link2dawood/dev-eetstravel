<div class="box box-primary">
    <?php if(Auth::user()->can('dashboard.announcements')): ?>
    <div class="box-header">
        <h4>Announcements</h4>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <br>
        <table class="table table-striped table-hover" style='background:#fff'>
            <thead>
            <th>ID</th>
            <th><?php echo e(trans('main.Title')); ?></th>
            <th><?php echo e(trans('main.Content')); ?></th>
            <th><?php echo e(trans('main.Date')); ?></th>
            <th><?php echo e(trans('main.Sender')); ?></th>
            <th style="width: 140px"><?php echo e(trans('main.Actions')); ?></th>
            </thead>
            <tbody>
            <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($announcement->id); ?></td>
                    <td><span style="font-size: 14px;font-weight: bold;"><?php echo e($announcement->title); ?></span></td>
                    <td><?php echo e($announcement->content); ?></td>
                    <td><?php echo e($announcement->created_at); ?></td>
                    <td><?php echo e($announcement->sender); ?></td>
                    <td><?php echo $announcement->action_buttons; ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="box-footer clearfix">
            <?php if(Auth::user()->can('announcements.create')): ?>
            <a href="<?php echo e(route('announcements.create')); ?>">
                <button class="btn btn-primary pull-left" type="submit"><i class="fa fa-plus fa-md" aria-hidden="true"></i> New Announcement
                </button>
            </a>
            <?php endif; ?>
            <?php if(Auth::user()->can('announcements.index')): ?>
            <a href="<?php echo e(route('announcements.index')); ?>">
                <button href="javascript:void(0)" class="btn btn-default pull-right">View All Announcements
                </button>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
        <div class="box-header">
            <h4><?php echo e(trans('main.Announcements')); ?></h4>
        </div>
        <div class="box-body">
            <?php echo e(trans('main.Youdonthavepermissions')); ?>

        </div>
    <?php endif; ?>
</div>

<!--  END Commentaries  -->

<?php /**PATH /var/www/html/resources/views/scaffold-interface/dashboard/components/announcements_list.blade.php ENDPATH**/ ?>