
<style>
:root {
    --monday-primary: #0073ea;
    --monday-primary-hover: #0060b9;
    --monday-bg: #f6f7fb;
    --monday-border: #e6e9ef;
    --monday-text: #323338;
}

.monday-wrapper {
    font-family: 'Inter', sans-serif;
    background-color: var(--monday-bg);
    padding: 1.5rem;
}
.monday-group {
    background: #fff;
    border: 1px solid var(--monday-border);
    border-radius: 10px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.monday-group-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 1rem;
    cursor: pointer;
    border-bottom: 1px solid var(--monday-border);
}
.monday-group-header:hover {
    background-color: #f9fafb;
}
.monday-group-collapse-icon {
    width: 18px;
    height: 18px;
    transition: transform 0.2s;
}
.monday-group[data-collapsed="true"] .monday-group-collapse-icon {
    transform: rotate(-90deg);
}
.monday-group-color {
    width: 10px;
    height: 40px;
    border-radius: 3px;
}
.monday-group-title {
    flex-grow: 1;
    font-size: 1rem;
    font-weight: 600;
}
.monday-group-count {
    background-color: #f3f4f6;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
}
.monday-table {
    width: 100%;
    border-collapse: collapse;
}
.monday-table-header {
    background-color: #f9fafb;
}
.monday-table th, .monday-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--monday-border);
    vertical-align: middle;
}
.monday-task-content {
    display: flex;
    align-items: center;
    gap: 8px;
}
.monday-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}
.monday-status {
    padding: 0.4rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    display: inline-block;
}
.monday-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}
.monday-epic {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}
.monday-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    align-items: center;
}
.monday-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}
.monday-action-btn svg {
    width: 20px;
    height: 20px;
}
.monday-action-btn.edit svg {
    stroke: #f59e0b;
}
.monday-action-btn.delete svg {
    stroke: #ef4444;
}
.monday-action-btn:hover {
    transform: scale(1.15);
}
.monday-empty {
    text-align: center;
    padding: 2rem 1rem;
    color: #6b7280;
}
</style>

<div class="monday-wrapper">
    
    
    
    <div class="monday-group" data-group="todo">
        <div class="monday-group-header" onclick="toggleDashboardGroup('todo')">
            <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0" fill="none"/>
                <polyline points="6 9 12 15 18 9" />
            </svg>
            <div class="monday-group-color" style="background-color: #0073ea;"></div>
            <h3 class="monday-group-title">To-Do</h3>
            <span class="monday-group-count"><?php echo e($todoTasks->count()); ?></span>
        </div>
        <div class="monday-group-content" id="dashboard-group-todo">
            <table class="monday-table">
                <thead class="monday-table-header">
                    <tr>
                        <th>TASK</th>
                        <th>PERSON</th>
                        <th>STATUS</th>
                        <th>DEADLINE</th>
                        <th>PRIORITY</th>
                        <th>EPIC</th>
                        <th>SP</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $todoTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="monday-table-row" data-task-id="<?php echo e($task->id); ?>">
                        <td class="monday-table-cell-task">
                            <div class="monday-task-content">
                                <span class="monday-task-text"><?php echo e($task->content); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="monday-person">
                                <?php if($task->show_assigned_users): ?>
                                    <?php
                                        $users = explode(' ', trim($task->show_assigned_users));
                                        $displayUsers = array_slice($users, 0, 3);
                                    ?>
                                    <?php $__currentLoopData = $displayUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="monday-avatar" style="background-color: <?php echo e('#' . substr(md5($userName), 0, 6)); ?>"><?php echo e(strtoupper(substr($userName, 0, 2))); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="monday-status" style="background-color:#fff3e0;color:#e65100;">Pending</span>
                        </td>
                        <td><?php echo e($task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M d') : '-'); ?></td>
                        <td class="text-center">
                            <?php if($task->priority): ?>
                            <span class="monday-badge" style="background-color:#ffebcc;color:#d97706;">High</span>
                            <?php else: ?>
                            <span class="monday-badge" style="background-color:#e5e7eb;color:#6b7280;">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($task->tour_link_show): ?>
                            <span class="monday-epic" style="background-color:#0ea5e920;color:#0ea5e9;"><?php echo e($task->tour_name); ?></span>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?php echo e($task->story_points ?? '-'); ?></td>
                        <td onclick="event.stopPropagation();">
                            <div class="monday-actions">
                                <?php
                                    $actionHtml = $task->action_buttons;
                                    preg_match_all('/href=["\']([^"\']+)["\']/', $actionHtml, $links);
                                    $editLink = $links[1][0] ?? null;
                                    $deleteLink = $links[1][1] ?? null;
                                ?>
                                <?php if($editLink): ?>
                                <a href="<?php echo e($editLink); ?>" class="monday-action-btn edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                                <?php if($deleteLink): ?>
                                <a href="#" onclick="confirmDashboardDelete(event, '<?php echo e($deleteLink); ?>')" class="monday-action-btn delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8"><div class="monday-empty">No to-do tasks</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    
    
    <div class="monday-group" data-group="completed">
        <div class="monday-group-header" onclick="toggleDashboardGroup('completed')">
            <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0" fill="none"/>
                <polyline points="6 9 12 15 18 9" />
            </svg>
            <div class="monday-group-color" style="background-color: #00c875;"></div>
            <h3 class="monday-group-title">Completed</h3>
            <span class="monday-group-count"><?php echo e($completedTasks->count()); ?></span>
        </div>
        <div class="monday-group-content" id="dashboard-group-completed" style="display:none;">
            <table class="monday-table">
                <thead class="monday-table-header">
                    <tr>
                        <th>TASK</th>
                        <th>PERSON</th>
                        <th>STATUS</th>
                        <th>DEADLINE</th>
                        <th>PRIORITY</th>
                        <th>EPIC</th>
                        <th>SP</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $completedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr data-task-id="<?php echo e($task->id); ?>">
                        <td class="monday-table-cell-task">
                            <div class="monday-task-content">
                                <span class="monday-task-text" style="text-decoration:line-through;"><?php echo e($task->content); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="monday-person">
                                <?php if($task->show_assigned_users): ?>
                                    <?php
                                        $users = explode(' ', trim($task->show_assigned_users));
                                        $displayUsers = array_slice($users, 0, 3);
                                    ?>
                                    <?php $__currentLoopData = $displayUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="monday-avatar" style="background-color: <?php echo e('#' . substr(md5($userName), 0, 6)); ?>"><?php echo e(strtoupper(substr($userName, 0, 2))); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="monday-status" style="background-color:#e8f5e9;color:#2e7d32;">Completed</span>
                        </td>
                        <td><?php echo e($task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M d') : '-'); ?></td>
                        <td class="text-center">
                            <?php if($task->priority): ?>
                            <span class="monday-badge" style="background-color:#ffebcc;color:#d97706;">High</span>
                            <?php else: ?>
                            <span class="monday-badge" style="background-color:#e5e7eb;color:#6b7280;">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($task->tour_link_show): ?>
                            <span class="monday-epic" style="background-color:#0ea5e920;color:#0ea5e9;"><?php echo e($task->tour_name); ?></span>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?php echo e($task->story_points ?? '-'); ?></td>
                        <td onclick="event.stopPropagation();">
                            <div class="monday-actions">
                                <?php
                                    $actionHtml = $task->action_buttons;
                                    preg_match_all('/href=["\']([^"\']+)["\']/', $actionHtml, $links);
                                    $editLink = $links[1][0] ?? null;
                                    $deleteLink = $links[1][1] ?? null;
                                ?>
                                <?php if($editLink): ?>
                                <a href="<?php echo e($editLink); ?>" class="monday-action-btn edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                                <?php if($deleteLink): ?>
                                <a href="#" onclick="confirmDashboardDelete(event, '<?php echo e($deleteLink); ?>')" class="monday-action-btn delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8"><div class="monday-empty">No completed tasks</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    
    
    <div class="monday-group" data-group="aborted">
        <div class="monday-group-header" onclick="toggleDashboardGroup('aborted')">
            <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0" fill="none"/>
                <polyline points="6 9 12 15 18 9" />
            </svg>
            <div class="monday-group-color" style="background-color: #e2445c;"></div>
            <h3 class="monday-group-title">Aborted</h3>
            <span class="monday-group-count"><?php echo e($abortedTasks->count()); ?></span>
        </div>

        <div class="monday-group-content" id="dashboard-group-aborted" style="display: none;">
            <table class="monday-table">
                <thead class="monday-table-header">
                    <tr>
                        <th>TASK</th>
                        <th>PERSON</th>
                        <th>STATUS</th>
                        <th>DEADLINE</th>
                        <th>PRIORITY</th>
                        <th>EPIC</th>
                        <th>SP</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $abortedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="monday-table-row" data-task-id="<?php echo e($task->id); ?>">
                        <td class="monday-table-cell-task">
                            <div class="monday-task-content">
                                <span class="monday-task-text" style="text-decoration: line-through;"><?php echo e($task->content); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="monday-person">
                                <?php if($task->show_assigned_users): ?>
                                    <?php
                                        $users = explode(' ', trim($task->show_assigned_users));
                                        $displayUsers = array_slice($users, 0, 3);
                                    ?>
                                    <?php $__currentLoopData = $displayUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="monday-avatar" style="background-color: <?php echo e('#' . substr(md5($userName), 0, 6)); ?>"><?php echo e(strtoupper(substr($userName, 0, 2))); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="monday-status" style="background-color:#ffebee;color:#c62828;">Aborted</span>
                        </td>
                        <td><?php echo e($task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M d') : '-'); ?></td>
                        <td class="text-center">
                            <?php if($task->priority): ?>
                            <span class="monday-badge" style="background-color:#ffebcc;color:#d97706;">High</span>
                            <?php else: ?>
                            <span class="monday-badge" style="background-color:#e5e7eb;color:#6b7280;">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($task->tour_link_show): ?>
                            <span class="monday-epic" style="background-color:#0ea5e920;color:#0ea5e9;"><?php echo e($task->tour_name); ?></span>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?php echo e($task->story_points ?? '-'); ?></td>
                        <td onclick="event.stopPropagation();">
                            <div class="monday-actions">
                                <?php
                                    $actionHtml = $task->action_buttons;
                                    preg_match_all('/href=["\']([^"\']+)["\']/', $actionHtml, $links);
                                    $editLink = $links[1][0] ?? null;
                                    $deleteLink = $links[1][1] ?? null;
                                ?>
                                <?php if($editLink): ?>
                                <a href="<?php echo e($editLink); ?>" class="monday-action-btn edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                                <?php if($deleteLink): ?>
                                <a href="#" onclick="confirmDashboardDelete(event, '<?php echo e($deleteLink); ?>')" class="monday-action-btn delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8"><div class="monday-empty">No aborted tasks</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleDashboardGroup(group) {
    const el = document.querySelector(`[data-group="${group}"]`);
    const content = document.getElementById(`dashboard-group-${group}`);
    const collapsed = el.getAttribute('data-collapsed') === 'true';
    el.setAttribute('data-collapsed', !collapsed);
    content.style.display = collapsed ? '' : 'none';
}

function confirmDashboardDelete(event, deleteUrl) {
    event.preventDefault();
    event.stopPropagation();
    
    if (confirm("Are you sure you want to delete this task?")) {
        const form = document.createElement('form');
        form.action = deleteUrl;
        form.method = 'POST';
        form.style.display = 'none';

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script><?php /**PATH /var/www/html/resources/views/scaffold-interface/dashboard/components/tasks_list.blade.php ENDPATH**/ ?>