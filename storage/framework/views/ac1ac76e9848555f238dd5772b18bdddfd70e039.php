<div class="panel panel-info">
    <div class="panel-heading"><?php echo trans('main.Tasks'); ?></div>
    <a data-info="<?php echo e($listIdTasks); ?>" id="getListIdTasks"></a>
    <a data-info="<?php echo e($tour->name); ?>" id="getNameTour"></a>
    <div class="panel-body">
        <div class="row">
            <?php if(Auth::user()->can('create.task')): ?>
            <div class="col-md-12">
                <a href="<?php echo url("task"); ?>/create?tour=<?php echo $tour->id; ?>" class='btn btn-success'><?php echo trans('main.AddTask'); ?></a>
            </div>
            <?php endif; ?>
        </div>
        <br>
        <table class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
            <thead>
            <tr>
                <th>ID</th>
                <th><?php echo trans('main.Content'); ?></th>
                <th><?php echo trans('main.Deadline'); ?></th>
                <th><?php echo trans('main.Assignto'); ?></th>
                <th><?php echo trans('main.Status'); ?></th>
                <th><?php echo trans('main.Priority'); ?></th>
                <th class="actions-button" style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
            </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $tasksData ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e($task['priority_value'] ? 'priority-high' : ''); ?>" style="<?php echo e($task['priority_value'] ? 'background: #ffbbb2;' : ''); ?>">
                    <td><?php echo e($task['id']); ?></td>
                    <td><?php echo e($task['content'] ?? ''); ?></td>
                    <td><?php echo e($task['dead_line'] ?? ''); ?></td>
                    <td><?php echo $task['assign'] ?? ''; ?></td>
                    <td>
                        <select name="status" class="task-status form-control" data-update-link="<?php echo e(route('task.update', ['task' => $task['id']])); ?>" style="border:none; outline:0px; background-color:inherit; -moz-appearance: none; -webkit-appearance: none; width: 100%;">
                            <?php $__currentLoopData = $task['task_statuses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status->id); ?>" <?php echo e($status->id == $task['status_id'] ? 'selected="selected"' : ''); ?>><?php echo e($status->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td class="priority" style="display: none;"><?php echo e($task['priority']); ?></td>
                    <td><?php echo $task['action_buttons'] ?? ''; ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center">No tasks found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Handle task status updates via AJAX (keeping existing functionality)
    $('body').on('change', 'select.task-status', function() {
        var select = $(this);
        var updateUrl = select.data('update-link');
        var newStatus = select.val();

        $.ajax({
            url: updateUrl,
            method: 'PUT',
            data: {
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Optional: Show success message
                console.log('Task status updated successfully');
            },
            error: function(xhr) {
                // Optional: Show error message and revert selection
                console.error('Error updating task status');
                // Revert to previous selection
                select.prop('selectedIndex', 0);
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?><?php /**PATH /var/www/html/resources/views/component/list_tasks_for_tour.blade.php ENDPATH**/ ?>