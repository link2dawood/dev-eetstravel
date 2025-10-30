<div class="panel">
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo trans('main.Content'); ?></th>
                    <th style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($notification->id); ?></td>
                        <td><?php echo e($notification->content); ?></td>
                        <td><?php echo $notification->action_buttons; ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($notifications->isEmpty()): ?>
                    <tr>
                        <td colspan="3" class="text-center">No notifications found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php /**PATH /var/www/html/resources/views/component/list_notifications_profile.blade.php ENDPATH**/ ?>