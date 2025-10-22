
<div class="box box-primary">
    <?php if(Auth::user()->can('dashboard.tasks')): ?>
        <div class="box-header">
            <h4>Tasks</h4>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="tasks-table" class="table table-striped table-bordered table-hover clickable-rows"
                       style='background:#fff'>
                    <thead>
                    <th>ID</th>
                    <th style="width: 300px"><?php echo e(trans('main.Content')); ?></th>
                    <th><?php echo e(trans('main.Deadline')); ?></th>
                    <th><?php echo e(trans('main.Tour')); ?></th>
                    <th><?php echo e(trans('main.Assignto')); ?></th>
                    <th><?php echo e(trans('main.TaskType')); ?></th>
                    <th><?php echo e(trans('main.Status')); ?></th>
                    <th style="width: 140px"><?php echo e(trans('main.Actions')); ?></th>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="clickable-row <?php echo e($task->priority ? 'task-priority' : ''); ?>" onclick="window.location.href='<?php echo e(route('task.show', ['task' => $task->id])); ?>'">
                        <td><?php echo e($task->id); ?></td>
                        <td><?php echo e($task->content); ?></td>
                        <td><?php echo e($task->dead_line); ?></td>
                        <td class="click_tour_in_task">
                            <?php if($task->tour_link_show): ?>
                            <span data-tour-link="<?php echo e($task->tour_link_show); ?>" style="color: blue; text-decoration: underline; cursor: pointer"
                                  onclick="event.stopPropagation(); window.location.href='<?php echo e($task->tour_link_show); ?>'">
                                <?php echo e($task->tour_name); ?>

                            </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($task->show_assigned_users); ?></td>
                        <td><?php echo e($task->task_type); ?></td>
                        <td class="status" onclick="event.stopPropagation();">
                            <select name="status" class="task-status form-control"
                                    data-update-link="<?php echo e($task->data_update_link); ?>">
                                <?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->id); ?>" <?php echo e($task->status == $status->id ? 'selected' : ''); ?>>
                                    <?php echo e($status->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td onclick="event.stopPropagation();">
                            <div class="btn-list flex-nowrap">
                                <?php
                                    $actionHtml = $task->action_buttons;
                                    preg_match_all('/href=["\']([^"\']+)["\']/', $actionHtml, $links);
                                    $editLink = $links[1][0] ?? null;
                                    $deleteLink = $links[1][1] ?? null;
                                ?>
                                
                                <?php if($editLink): ?>
                                    <a href="<?php echo e($editLink); ?>" class="btn btn-icon btn-ghost-warning" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if($deleteLink): ?>
                                    <button type="button" class="btn btn-icon btn-ghost-danger delete-btn"
                                            data-url="<?php echo e($deleteLink); ?>"
                                            title="Delete"
                                            onclick="confirmDelete(event, this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php if(Auth::user()->can('task.create')): ?>
                    <a href="#" data-target="#modalCreate1" data-toggle="modal"
                       class="popupCreate btn btn-success">
                        <i class="fa fa-plus fa-md" aria-hidden="true"></i> <?php echo e(trans('main.NewTask')); ?>

                    </a>
                <?php endif; ?>
                <?php if(Auth::user()->can('task.index')): ?>
                    <a href="<?php echo e(route('task.index')); ?>" class="btn btn-outline-secondary float-end">
                        <?php echo e(trans('main.ViewAllTasks')); ?>

                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="box-header">
                <h4><?php echo e(trans('main.Tasks')); ?></h4>
            </div>
            <div class="box-body">
                <?php echo e(trans('main.Youdonthavepermissions')); ?>

            </div>
        <?php endif; ?>
        </div>
    </div>

<script>
function confirmDelete(event, button) {
    event.preventDefault();
    if (confirm('Are you sure you want to delete this item?')) {
        const url = button.getAttribute('data-url');

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting item');
        });
    }
}
</script>

<style>
.clickable-rows .clickable-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.clickable-rows .clickable-row:hover {
    background-color: #f8f9fa !important;
}

.clickable-rows .clickable-row td:last-child {
    cursor: default;
}
</style>
<?php /**PATH /var/www/html/resources/views/scaffold-interface/dashboard/components/tasks_list.blade.php ENDPATH**/ ?>