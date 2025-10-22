<?php $__env->startSection('title','Tasks'); ?>
<?php $__env->startSection('content'); ?>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col">
                    <div class="page-pretitle">Project Management</div>
                    <div class="page-title d-flex align-items-center">
                        <h2 class="me-3">Tasks</h2>
                        <div class="dropdown">
                            <button class="btn btn-ghost-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <svg class="icon icon-tabler icon-tabler-chevron-down" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#">All Tasks</a>
                                <a class="dropdown-item" href="#">My Tasks</a>
                                <a class="dropdown-item" href="#">Completed Tasks</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                        <li class="nav-item">
                            <a href="#main-table" class="nav-link active" data-bs-toggle="tab">Main table</a>
                        </li>
                        <li class="nav-item">
                            <a href="#kanban" class="nav-link" data-bs-toggle="tab">Kanban</a>
                        </li>
                        <li class="nav-item ms-auto">
                            <a href="#" class="btn btn-primary">
                                <svg class="icon icon-tabler icon-tabler-plus" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                New task
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="btn-list">
                            <div class="input-icon">
                                <input type="text" class="form-control" placeholder="Search tasks...">
                                <span class="input-icon-addon">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                </span>
                            </div>
                            <a href="#" class="btn btn-outline-primary">
                                <svg class="icon icon-tabler icon-tabler-user" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                Person
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <svg class="icon icon-tabler icon-tabler-filter" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.414 -4.414a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                                Filter
                            </a>
                        </div>
                        <div class="btn-list">
                            <a href="#" class="btn btn-outline-primary">
                                <svg class="icon icon-tabler icon-tabler-sort-ascending" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l7 0" /><path d="M4 12l7 0" /><path d="M4 18l9 0" /><path d="M15 9l3 -3l3 3" /><path d="M18 6l0 12" /></svg>
                                Sort
                            </a>
                            <a href="#" class="btn">
                                <svg class="icon icon-tabler icon-tabler-eye-off" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" /><path d="M3 3l18 18" /></svg>
                                Hide
                            </a>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Group by
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">None</a>
                                    <a class="dropdown-item" href="#">Status</a>
                                    <a class="dropdown-item" href="#">Type</a>
                                    <a class="dropdown-item" href="#">Owner</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane active show" id="main-table">
                            <div class="accordion" id="taskAccordion">
                                <!-- To-Do Section -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#todoSection">
                                            <svg class="icon me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M7 7h10v2H7z"/><path d="M7 11h10v2H7z"/><path d="M7 15h10v2H7z"/></svg>
                                            To-Do
                                        </button>
                                    </h2>
                                    <div id="todoSection" class="accordion-collapse collapse show">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-vcenter table-nowrap clickable-rows">
                                                    <thead>
                                                        <tr>
                                                            <th class="w-1"><input type="checkbox" class="form-check-input m-0 align-middle" aria-label="Select all tasks"></th>
                                                            <th>Task</th>
                                                            <th class="text-center" style="width:40px">Star</th>
                                                            <th class="text-center" style="width:40px">Due</th>
                                                            <th>Owner</th>
                                                            <th>Status</th>
                                                            <th>Type</th>
                                                            <th>Task ID</th>
                                                            <th>Estimated SP</th>
                                                            <th>Epic</th>
                                                            <th class="text-center" style="width: 100px">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__empty_1 = true; $__currentLoopData = $tasks->where('status.name', '!=', 'Completed'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr class="clickable-row" onclick="window.location='<?php echo e(route('task.show', ['task' => $task->id])); ?>'">
                                                            <td onclick="event.stopPropagation();"><input type="checkbox" class="form-check-input m-0 align-middle" aria-label="Select task"></td>
                                                            <td><?php echo e($task->content); ?></td>
                                                            <td class="text-center" onclick="event.stopPropagation();">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary" data-bs-toggle="tooltip" title="Star">
                                                                    <svg class="icon icon-tabler icon-tabler-star" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                                                                </a>
                                                            </td>
                                                            <td class="text-center" onclick="event.stopPropagation();">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary" data-bs-toggle="tooltip" title="Due date">
                                                                    <svg class="icon icon-tabler icon-tabler-clock" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><polyline points="12 7 12 12 15 15" /></svg>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <?php if($task->assignedTo): ?>
                                                                    <div class="avatar avatar-sm"><?php echo e(substr($task->assignedTo->name, 0, 2)); ?></div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge" style="background-color: <?php echo e($task->getStatusColor()); ?>">
                                                                    <?php echo e($task->getStatusName()); ?>

                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?php echo e($task->task_type == '1' ? 'green' : ($task->task_type == '2' ? 'red' : 'blue')); ?>">
                                                                    <?php echo e(\App\Task::$taskTypes[$task->task_type] ?? ''); ?>

                                                                </span>
                                                            </td>
                                                            <td>TREP-<?php echo e(str_pad($task->id, 3, '0', STR_PAD_LEFT)); ?></td>
                                                            <td><?php echo e($task->story_points ?? 0); ?> SP</td>
                                                            <td>
                                                                <?php if($task->epic): ?>
                                                                    <span class="badge bg-azure-lt"><?php echo e($task->epic); ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td onclick="event.stopPropagation();">
                                                                <div class="d-flex justify-content-center gap-2">
                                                                    <a href="<?php echo e(route('task.edit', ['task' => $task->id])); ?>" class="text-decoration-none" title="Edit">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFA500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                                        </svg>
                                                                    </a>
                                                                    <a href="#" class="text-decoration-none" title="Delete" onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('delete-task-<?php echo e($task->id); ?>').submit();">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                            <polyline points="3 6 5 6 21 6"/>
                                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                                                        </svg>
                                                                    </a>
                                                                    <form id="delete-task-<?php echo e($task->id); ?>" action="<?php echo e(route('task.destroy', $task->id)); ?>" method="POST" style="display: none;">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr>
                                                            <td colspan="11" class="text-center py-4">
                                                                <div class="empty">
                                                                    <div class="empty-icon">
                                                                        <svg class="icon" width="24" height="24" viewBox="0 0 24 24"><path d="M20 6H4V4h16v2zm0 2H4v2h16V8zm0 4H4v2h16v-2zm0 4H4v2h16v-2z"/></svg>
                                                                    </div>
                                                                    <p class="empty-title">No tasks found</p>
                                                                    <p class="empty-subtitle text-secondary">
                                                                        Try adjusting your search or filter to find what you're looking for.
                                                                    </p>
                                                                    <div class="empty-action">
                                                                        <a href="<?php echo e(route('task.create')); ?>" class="btn btn-primary">
                                                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                                                            Add your first task
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                        <?php if($tasks->where('status.name', '!=', 'Completed')->count() > 0): ?>
                                                        <tr>
                                                            <td colspan="11">
                                                                <a href="#" class="btn btn-link">+ Add task</a>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Completed Section -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#completedSection">
                                            <svg class="icon me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                            Completed
                                        </button>
                                    </h2>
                                    <div id="completedSection" class="accordion-collapse collapse">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-vcenter table-nowrap clickable-rows">
                                                    <thead>
                                                        <tr>
                                                            <th class="w-1"><input type="checkbox" class="form-check-input m-0 align-middle" aria-label="Select all completed tasks"></th>
                                                            <th>Task</th>
                                                            <th>Owner</th>
                                                            <th>Status</th>
                                                            <th>Type</th>
                                                            <th>Task ID</th>
                                                            <th>Estimated SP</th>
                                                            <th>Epic</th>
                                                            <th class="text-center" style="width: 100px">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__empty_1 = true; $__currentLoopData = $tasks->where('status.name', 'Completed'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr class="clickable-row" onclick="window.location='<?php echo e(route('task.show', ['task' => $task->id])); ?>'">
                                                            <td onclick="event.stopPropagation();"><input type="checkbox" class="form-check-input m-0 align-middle" aria-label="Select completed task"></td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2"><?php echo e($task->content); ?></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <?php if($task->assignedTo): ?>
                                                                    <div class="avatar avatar-sm"><?php echo e(substr($task->assignedTo->name, 0, 2)); ?></div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-success">Completed</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?php echo e($task->task_type == '1' ? 'green' : ($task->task_type == '2' ? 'red' : 'blue')); ?>">
                                                                    <?php echo e(\App\Task::$taskTypes[$task->task_type] ?? ''); ?>

                                                                </span>
                                                            </td>
                                                            <td>TREP-<?php echo e(str_pad($task->id, 3, '0', STR_PAD_LEFT)); ?></td>
                                                            <td><?php echo e($task->story_points ?? 0); ?> SP</td>
                                                            <td>
                                                                <?php if($task->epic): ?>
                                                                    <span class="badge bg-azure-lt"><?php echo e($task->epic); ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td onclick="event.stopPropagation();">
                                                                <div class="d-flex justify-content-center gap-2">
                                                                    <a href="<?php echo e(route('task.edit', ['task' => $task->id])); ?>" class="text-decoration-none" title="Edit">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFA500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                                        </svg>
                                                                    </a>
                                                                    <a href="#" class="text-decoration-none" title="Delete" onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('delete-task-<?php echo e($task->id); ?>').submit();">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                            <polyline points="3 6 5 6 21 6"/>
                                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                                                        </svg>
                                                                    </a>
                                                                    <form id="delete-task-<?php echo e($task->id); ?>" action="<?php echo e(route('task.destroy', $task->id)); ?>" method="POST" style="display: none;">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr>
                                                            <td colspan="9" class="text-center py-4">
                                                                <div class="empty">
                                                                    <p class="empty-title h3">No completed tasks</p>
                                                                    <p class="empty-subtitle text-secondary">
                                                                        Completed tasks will appear here
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(method_exists($tasks, 'links')): ?>
                    <div class="card-footer">
                        <?php echo e($tasks->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <span id="task_types" data-info="<?php echo e($task_types); ?>"></span>
    <span id="task_statuses" data-info="<?php echo e(json_encode($statuses)); ?>"></span>
    <span id="status_permission" data-info="<?php echo e(\App\Helper\PermissionHelper::checkPermission('task.edit') ? 'status' : ''); ?>"></span>

    <!-- Task Legend Modal -->
    <div class="modal modal-blur fade" id="taskLegendModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task Legend</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $__env->make('legend.task_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
<?php $__env->stopPush(); ?>

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

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Action icons hover effect */
    .clickable-rows td svg {
        transition: transform 0.2s ease;
    }

    .clickable-rows td a:hover svg {
        transform: scale(1.1);
    }
</style>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/task/index.blade.php ENDPATH**/ ?>