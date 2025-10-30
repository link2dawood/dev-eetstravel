<div class="panel">
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo trans('main.Content'); ?></th>
                    <th><?php echo trans('main.Deadline'); ?></th>
                    <th><?php echo trans('main.Tour'); ?></th>
                    <th><?php echo trans('main.Status'); ?></th>
                    <th><?php echo trans('main.Priority'); ?></th>
                    <th style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="<?php echo e($task->priority ? 'background: #ffbbb2;' : ''); ?>">
                        <td><?php echo e($task->id); ?></td>
                        <td><?php echo e($task->content); ?></td>
                        <td><?php echo e($task->dead_line); ?></td>
                        <td class="click_tour_in_task"><?php echo $task->tour_link; ?></td>
                        <td class="status"><?php echo $task->status_display; ?></td>
                        <td class="priority"><?php echo e($task->priority_text); ?></td>
                        <td><?php echo $task->action_buttons; ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(empty($tasks)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No tasks found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php /**PATH /var/www/html/resources/views/component/list_tasks_for_profile.blade.php ENDPATH**/ ?>