
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
                    <table id="tasks-table" class="table table-striped table-bordered table-hover"
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
                        <tr class="<?php echo e($task->priority ? 'task-priority' : ''); ?>">
                            <td><?php echo e($task->id); ?></td>
                            <td><?php echo e($task->content); ?></td>
                            <td><?php echo e($task->dead_line); ?></td>
                            <td class="click_tour_in_task">
                                <?php if($task->tour_link_show): ?>
                                <span data-tour-link="<?php echo e($task->tour_link_show); ?>" style="color: blue; text-decoration: underline; cursor: pointer">
                                    <?php echo e($task->tour_name); ?>

                                </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($task->show_assigned_users); ?></td>
                            <td><?php echo e($task->task_type); ?></td>
                            <td class="status">
                                <select name="status" class="task-status form-control"
                                        data-update-link="<?php echo e($task->data_update_link); ?>">
                                    <?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status->id); ?>" <?php echo e($task->status == $status->id ? 'selected' : ''); ?>>
                                        <?php echo e($status->name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td><?php echo $task->action_buttons; ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <div class="box-footer clearfix">
                        <?php if(Auth::user()->can('task.create')): ?>
                            <a href="#" data-target="#modalCreate1" data-toggle="modal"
                               class="popupCreate btn btn-success">
                                <i class="fa fa-plus fa-md" aria-hidden="true"></i> <?php echo e(trans('main.NewTask')); ?>

                            </a>
                        <?php endif; ?>
                        <?php if(Auth::user()->can('task.index')): ?>
                            <a href="<?php echo e(route('task.index')); ?>">
                                <button href="javascript:void(0)"
                                        class="btn btn-default btn-flat pull-right"><?php echo e(trans('main.ViewAllTasks')); ?>

                                </button>
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
</div>
<?php /**PATH /var/www/html/resources/views/scaffold-interface/dashboard/components/tasks_list.blade.php ENDPATH**/ ?>