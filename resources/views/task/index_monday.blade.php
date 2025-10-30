@extends('scaffold-interface.layouts.tabler-app')
@section('title','Tasks')

@section('content')

<style>
/* Monday.com Style - Inline for immediate loading (CSS remains unchanged) */
:root {
    --monday-primary: #0073ea;
    --monday-primary-hover: #0060b9;
    --monday-primary-selected: #cce5ff;
    --monday-success: #00c875;
    --monday-warning: #fdab3d;
    --monday-danger: #e2445c;
    --monday-purple: #a25ddc;
    --monday-dark-purple: #401694;
    --monday-text-primary: #323338;
    --monday-text-secondary: #676879;
    --monday-border: #d0d4e4;
    --monday-background: #ffffff;
    --monday-background-hover: #f5f6f8;
    --monday-background-group: #f6f7fb;
    --monday-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    --monday-shadow-hover: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.monday-board {
    background: var(--monday-background);
    border-radius: 8px;
    box-shadow: var(--monday-shadow);
    overflow: hidden;
    margin: 20px 0;
}

.monday-toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--monday-background);
    border-bottom: 1px solid var(--monday-border);
    flex-wrap: wrap;
}

.monday-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid var(--monday-border);
    background: var(--monday-background);
    color: var(--monday-text-primary);
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.monday-btn:hover {
    background: var(--monday-background-hover);
    border-color: var(--monday-primary);
}

.monday-btn-primary {
    background: var(--monday-primary);
    color: white;
    border-color: var(--monday-primary);
}

.monday-btn-primary:hover {
    background: var(--monday-primary-hover);
    border-color: var(--monday-primary-hover);
}

.monday-btn svg {
    width: 18px;
    height: 18px;
}

.monday-search {
    position: relative;
    flex-grow: 1;
    max-width: 300px;
}

.monday-search-input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1px solid var(--monday-border);
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.monday-search-input:focus {
    outline: none;
    border-color: var(--monday-primary);
    box-shadow: 0 0 0 2px var(--monday-primary-selected);
}

.monday-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--monday-text-secondary);
    width: 18px;
    height: 18px;
}

.monday-group {
    margin-bottom: 1px;
}

.monday-group-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--monday-background-group);
    cursor: pointer;
    transition: background 0.2s ease;
    user-select: none;
}

.monday-group-header:hover {
    background: #ecedf5;
}

.monday-group-collapse-icon {
    width: 16px;
    height: 16px;
    color: var(--monday-text-secondary);
    transition: transform 0.2s ease;
}

.monday-group-collapsed .monday-group-collapse-icon {
    transform: rotate(-90deg);
}

.monday-group-color {
    width: 4px;
    height: 24px;
    border-radius: 2px;
}

.monday-group-title {
    font-size: 14px;
    font-weight: 500;
    color: var(--monday-text-primary);
    margin: 0;
    flex-grow: 1;
}

.monday-group-count {
    font-size: 13px;
    color: var(--monday-text-secondary);
    background: white;
    padding: 2px 8px;
    border-radius: 12px;
}

.monday-group-actions {
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.monday-group-header:hover .monday-group-actions {
    opacity: 1;
}

.monday-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.monday-table-header {
    background: var(--monday-background);
    position: sticky;
    top: 0;
    z-index: 10;
}

.monday-table-header th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--monday-text-secondary);
    border-bottom: 1px solid var(--monday-border);
    white-space: nowrap;
}

.monday-table-row {
    background: var(--monday-background);
    border-bottom: 1px solid var(--monday-border);
    transition: background 0.15s ease;
}

.monday-table-row:hover {
    background: var(--monday-background-hover);
}

.monday-table-row.completed {
    opacity: 0.6;
}

.monday-table-row.completed .monday-table-cell-task {
    text-decoration: line-through;
}

.monday-table-cell {
    padding: 8px 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--monday-border);
}

.monday-table-cell-task {
    min-width: 300px;
    max-width: 500px;
}

.monday-table-cell-checkbox {
    width: 40px;
    padding: 8px;
}

.monday-checkbox {
    width: 18px;
    height: 18px;
    border: 2px solid var(--monday-border);
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
    outline: none;
}

.monday-checkbox:hover {
    border-color: var(--monday-primary);
}

.monday-checkbox:checked {
    background-color: var(--monday-primary);
    border-color: var(--monday-primary);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
    background-size: 14px;
    background-position: center;
    background-repeat: no-repeat;
}

.monday-task-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.monday-task-priority-icon {
    color: var(--monday-warning);
    flex-shrink: 0;
}

.monday-task-text {
    color: var(--monday-text-primary);
    font-size: 14px;
}

.monday-editable {
    /* Removed from task and story points */
    padding: 6px 8px;
    border-radius: 4px;
    /* cursor: pointer !important; */
    transition: all 0.2s ease;
    min-height: 32px;
    display: flex;
    align-items: center;
}

.monday-editable:hover {
    /* Removed hover effect to stop inline edit feel */
    background: none;
    outline: none;
    box-shadow: none;
}

.monday-editable-input {
    width: 100%;
    padding: 6px 8px;
    border: 2px solid var(--monday-primary);
    border-radius: 4px;
    font-size: 14px;
    outline: none;
}

.monday-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 500;
    /* Removed cursor pointer */
    transition: all 0.2s ease;
    min-width: 100px;
}

.monday-status-clickable {
    /* Removed for non-clickable status */
    min-width: 120px;
    font-weight: 600;
    user-select: none;
}

.monday-status-clickable:hover {
    /* Removed hover effect for non-clickable status */
    transform: none;
    box-shadow: none;
    filter: none;
}

.monday-status-clickable svg {
    /* Hide the dropdown icon in non-clickable status */
    display: none;
}

.monday-status:hover {
    /* Removed hover effect */
    opacity: 1;
    transform: none;
}

.monday-person {
    display: flex;
    align-items: center;
    gap: 4px;
}

.monday-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    color: white;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.monday-avatar-add {
    background: var(--monday-background-hover);
    border: 2px dashed var(--monday-border);
    color: var(--monday-text-secondary);
    /* Removed cursor pointer */
    transition: all 0.2s ease;
}

.monday-avatar-add:hover {
    /* Removed hover effect */
    border-color: var(--monday-border);
    color: var(--monday-text-secondary);
}

.monday-date {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--monday-text-secondary);
    font-size: 13px;
    /* Removed cursor pointer */
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.monday-date:hover {
    /* Removed hover effect */
    background: none;
}

.monday-date.overdue {
    color: var(--monday-danger);
}

.monday-date-icon {
    width: 16px;
    height: 16px;
}

.monday-actions {
    display: flex;
    gap: 4px;
    justify-content: center;
    /* opacity: 0; */ /* Kept visible for easier action */
    transition: opacity 0.2s ease;
}

.monday-table-row:hover .monday-actions {
    opacity: 1;
}

.monday-action-btn {
    padding: 6px;
    border-radius: 4px;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.monday-action-btn:hover {
    background: var(--monday-background-hover);
}

.monday-action-btn svg {
    width: 18px;
    height: 18px;
}

.monday-action-btn.edit {
    color: var(--monday-primary);
}

.monday-action-btn.delete {
    color: var(--monday-danger);
}

.monday-add-task-row {
    padding: 8px 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid var(--monday-border);
    cursor: pointer;
    transition: background 0.2s ease;
}

.monday-add-task-row:hover {
    background: var(--monday-background-hover);
}

.monday-add-task-text {
    color: var(--monday-text-secondary);
    font-size: 14px;
}

.monday-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.monday-dropdown {
    position: relative;
}

.monday-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 200px;
    background: white;
    border: 1px solid var(--monday-border);
    border-radius: 8px;
    box-shadow: var(--monday-shadow-hover);
    padding: 8px 0;
    z-index: 1000;
    display: none;
}

.monday-dropdown-menu.show {
    display: block;
}

.monday-dropdown-item {
    padding: 10px 16px;
    cursor: pointer;
    transition: background 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.monday-dropdown-item:hover {
    background: var(--monday-background-hover);
}

.monday-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.monday-modal.show {
    opacity: 1;
    pointer-events: all;
}

.monday-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: var(--monday-shadow-hover);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.monday-modal.show .monday-modal-content {
    transform: scale(1);
}

.monday-modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--monday-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.monday-modal-title {
    font-size: 20px;
    font-weight: 500;
    margin: 0;
    color: var(--monday-text-primary);
}

.monday-modal-close {
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 4px;
    color: var(--monday-text-secondary);
}

.monday-modal-close:hover {
    color: var(--monday-text-primary);
}

.monday-modal-body {
    padding: 24px;
}

.monday-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--monday-border);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.monday-form-group {
    margin-bottom: 20px;
}

.monday-form-label {
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--monday-text-primary);
}

.monday-form-input,
.monday-form-textarea,
.monday-form-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--monday-border);
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.monday-form-input:focus,
.monday-form-textarea:focus,
.monday-form-select:focus {
    outline: none;
    border-color: var(--monday-primary);
    box-shadow: 0 0 0 2px var(--monday-primary-selected);
}

.monday-form-textarea {
    resize: vertical;
    min-height: 100px;
}

.monday-empty {
    text-align: center;
    padding: 60px 20px;
}

.monday-empty-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 16px;
    color: var(--monday-text-secondary);
    opacity: 0.5;
}

.monday-empty-title {
    font-size: 18px;
    font-weight: 500;
    color: var(--monday-text-primary);
    margin-bottom: 8px;
}

.monday-empty-text {
    font-size: 14px;
    color: var(--monday-text-secondary);
}

.monday-priority-star {
    color: var(--monday-warning);
    cursor: pointer;
    transition: all 0.2s ease;
}

.monday-priority-star:hover {
    transform: scale(1.2);
}

.monday-epic {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.monday-sp {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 8px;
    background: #e6f7ff;
    color: #0073ea;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    min-width: 40px;
}

.monday-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid var(--monday-border);
    border-top-color: var(--monday-primary);
    border-radius: 50%;
    animation: monday-spin 0.8s linear infinite;
}

.monday-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 16px 24px;
    border-top: 1px solid var(--monday-border);
}

.monday-pagination-btn {
    padding: 6px 12px;
    border: 1px solid var(--monday-border);
    border-radius: 4px;
    background: var(--monday-background);
    color: var(--monday-text-primary);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 13px;
}

.monday-pagination-btn:hover:not(:disabled) {
    background: var(--monday-background-hover);
    border-color: var(--monday-primary);
}

.monday-pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.monday-pagination-info {
    font-size: 13px;
    color: var(--monday-text-secondary);
    padding: 0 12px;
}

@keyframes monday-spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .monday-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .monday-search {
        max-width: 100%;
    }

    .monday-table-cell {
        padding: 8px;
    }

    .monday-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{ trans('main.Tasks') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="monday-board">
            <div class="monday-toolbar">
                <button class="monday-btn monday-btn-primary" id="newTaskBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    New Task
                </button>

                <div class="monday-search">
                    <svg class="monday-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="10" cy="10" r="7" />
                        <line x1="21" y1="21" x2="15" y2="15" />
                    </svg>
                    <input type="text" class="monday-search-input" placeholder="Search tasks..." id="taskSearch">
                </div>
            </div>

            <div class="monday-group" data-group="todo">
                <div class="monday-group-header" onclick="toggleGroup('todo')">
                    <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                    <div class="monday-group-color" style="background-color: #0073ea;"></div>
                    <h3 class="monday-group-title">To-Do</h3>
                    <span class="monday-group-count">{{ $todoTasks->total() }}</span>
                    <div class="monday-group-actions">
                        <button class="monday-action-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="1" />
                                <circle cx="12" cy="19" r="1" />
                                <circle cx="12" cy="5" r="1" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="monday-group-content" id="group-todo">
                    <table class="monday-table">
                        <thead class="monday-table-header">
                            <tr>
                                <th style="width: 40%">TASK</th>
                                <th style="width: 150px">PERSON</th>
                                <th style="width: 120px">STATUS</th>
                                <th style="width: 130px">DEADLINE</th>
                                <th style="width: 80px">PRIORITY</th>
                                <th style="width: 100px">EPIC</th>
                                <th style="width: 80px">SP</th>
                                <th style="width: 80px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todoTasks as $task)
                            <tr class="monday-table-row" data-task-id="{{ $task->id }}">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        @if($task->priority)
                                        <svg class="monday-task-priority-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" onclick="editTask({{ $task->id }})" title="High Priority: Click to edit task">
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        @else
                                        <svg class="monday-task-priority-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="opacity: 0.3;" onclick="editTask({{ $task->id }})" title="Normal Priority: Click to edit task">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        @endif
                                        <span class="monday-task-text" data-task-id="{{ $task->id }}" onclick="editTask({{ $task->id }})" style="cursor: pointer;">{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        @if($task->assigned_users && $task->assigned_users->count() > 0)
                                            @foreach($task->assigned_users->take(3) as $user)
                                                <div class="monday-avatar" style="background-color: {{ '#' . substr(md5($user->name), 0, 6) }}" title="{{ $user->name }}" onclick="editTask({{ $task->id }})" style="cursor: pointer;">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endforeach
                                            @if($task->assigned_users->count() > 3)
                                            <div class="monday-avatar" style="background-color: #757575" title="+{{ $task->assigned_users->count() - 3 }} more" onclick="editTask({{ $task->id }})" style="cursor: pointer;">
                                                +{{ $task->assigned_users->count() - 3 }}
                                            </div>
                                            @endif
                                        @endif
                                        <div class="monday-avatar monday-avatar-add" onclick="editTask({{ $task->id }})" title="Assign Person: Click to edit task">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                        </div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: {{ $task->getStatusColor() }}20; color: {{ $task->getStatusColor() }}; @if($task->getStatusName() === 'Pending') color: #000000 !important; @endif display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 6px 10px; cursor: pointer;" onclick="editTask({{ $task->id }})" title="Status: Click to edit task">
                                        <span>{{ $task->getStatusName() }}</span>
                                        </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-date {{ $task->isOverdue() ? 'overdue' : '' }}" onclick="editTask({{ $task->id }})" style="cursor: pointer;" title="Deadline: Click to edit task">
                                        <svg class="monday-date-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        <span>{{ $task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M d, H:i') : 'No date' }}</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell text-center">
                                    @if($task->priority)
                                    <span class="monday-badge" style="background-color: #ffebcc; color: #d97706;">High</span>
                                    @else
                                    <span class="monday-badge" style="background-color: #e5e7eb; color: #6b7280;">Normal</span>
                                    @endif
                                </td>
                                <td class="monday-table-cell">
                                    @if($task->epic)
                                    <span class="monday-epic" style="background-color: {{ $task->epic->color ?? '#6c757d' }}20; color: {{ $task->epic->color ?? '#6c757d' }}">
                                        {{ $task->epic->name }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="monday-table-cell text-center">
                                    @if($task->story_points || $task->estimated_sp)
                                    <span class="monday-sp" data-task-id="{{ $task->id }}" onclick="editTask({{ $task->id }})" style="cursor: pointer;" title="Story Points: Click to edit task">
                                        {{ $task->story_points ?? $task->estimated_sp }}
                                    </span>
                                    @else
                                    <span class="monday-sp" data-task-id="{{ $task->id }}" onclick="editTask({{ $task->id }})" style="opacity: 0.5; cursor: pointer;" title="Story Points: Click to edit task">0</span>
                                    @endif
                                </td>
                               <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; gap: 8px; justify-content: center; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask({{ $task->id }})" title="Edit" style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px; padding: 8px; color: #f59e0b;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button class="monday-action-btn delete" onclick="deleteTask({{ $task->id }})" title="Delete" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 8px; color: #ef4444;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">
                                    <div class="monday-empty">
                                        <svg class="monday-empty-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <rect x="9" y="5" width="6" height="6" rx="1" />
                                        </svg>
                                        <div class="monday-empty-title">No pending tasks</div>
                                        <div class="monday-empty-text">Click "Add task" to create your first task</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                   @if($todoTasks->hasPages())
                    <div class="monday-pagination">
                        <button class="monday-pagination-btn" onclick="navigatePage('todo', {{ $todoTasks->currentPage() - 1 }})" {{ $todoTasks->onFirstPage() ? 'disabled' : '' }}>
                            Previous
                        </button>
                        <span class="monday-pagination-info">
                            Page {{ $todoTasks->currentPage() }} of {{ $todoTasks->lastPage() }}
                        </span>
                        <button class="monday-pagination-btn" onclick="navigatePage('todo', {{ $todoTasks->currentPage() + 1 }})" {{ !$todoTasks->hasMorePages() ? 'disabled' : '' }}>
                            Next
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <div class="monday-group" data-group="completed">
                <div class="monday-group-header" onclick="toggleGroup('completed')">
                    <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                    <div class="monday-group-color" style="background-color: #00c875;"></div>
                    <h3 class="monday-group-title">Completed</h3>
                    <span class="monday-group-count">{{ $completedTasks->total() }}</span>
                </div>

                <div class="monday-group-content" id="group-completed" style="display: none;">
                    <table class="monday-table">
                        <thead class="monday-table-header">
                            <tr>
                                <th style="width: 40%">TASK</th>
                                <th style="width: 150px">PERSON</th>
                                <th style="width: 120px">STATUS</th>
                                <th style="width: 130px">DEADLINE</th>
                                <th style="width: 80px">PRIORITY</th>
                                <th style="width: 100px">EPIC</th>
                                <th style="width: 80px">SP</th>
                                <th style="width: 80px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedTasks as $task)
                            <tr class="monday-table-row completed" data-task-id="{{ $task->id }}">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text" onclick="editTask({{ $task->id }})" style="cursor: pointer;">{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        @if($task->assigned_users && $task->assigned_users->count() > 0)
                                            @foreach($task->assigned_users->take(3) as $user)
                                                <div class="monday-avatar" style="background-color: {{ '#' . substr(md5($user->name), 0, 6) }}" title="{{ $user->name }}" onclick="editTask({{ $task->id }})" style="cursor: pointer;">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: {{ $task->getStatusColor() }}20; color: {{ $task->getStatusColor() }}; display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 6px 10px; cursor: pointer;" onclick="editTask({{ $task->id }})" title="Status: Click to edit task">
                                        <span>{{ $task->getStatusName() }}</span>
                                        </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-date" onclick="editTask({{ $task->id }})" style="cursor: pointer;">
                                        <span class="text-muted">{{ $task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M d') : '-' }}</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-badge" style="background-color: #e5e7eb; color: #6b7280;">-</span>
                                </td>
                                <td class="monday-table-cell">
                                    @if($task->epic)
                                    <span class="monday-epic" style="background-color: {{ $task->epic->color ?? '#6c757d' }}20; color: {{ $task->epic->color ?? '#6c757d' }}">
                                        {{ $task->epic->name }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="monday-table-cell text-center">
                                    @if($task->story_points || $task->estimated_sp)
                                    <span class="monday-sp" onclick="editTask({{ $task->id }})" style="cursor: pointer;">{{ $task->story_points ?? $task->estimated_sp }}</span>
                                    @else
                                    <span class="text-muted" onclick="editTask({{ $task->id }})" style="cursor: pointer;">-</span>
                                    @endif
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; gap: 8px; justify-content: center; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask({{ $task->id }})" title="Edit" style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px; padding: 8px; color: #f59e0b;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">
                                    <div class="monday-empty">
                                        <div class="monday-empty-title">No completed tasks</div>
                                        <div class="monday-empty-text">Complete some tasks to see them here</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                   @if($completedTasks->hasPages())
                    <div class="monday-pagination">
                        <button class="monday-pagination-btn" onclick="navigatePage('completed', {{ $completedTasks->currentPage() - 1 }})" {{ $completedTasks->onFirstPage() ? 'disabled' : '' }}>
                            Previous
                        </button>
                        <span class="monday-pagination-info">
                            Page {{ $completedTasks->currentPage() }} of {{ $completedTasks->lastPage() }}
                        </span>
                        <button class="monday-pagination-btn" onclick="navigatePage('completed', {{ $completedTasks->currentPage() + 1 }})" {{ !$completedTasks->hasMorePages() ? 'disabled' : '' }}>
                            Next
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <div class="monday-group" data-group="aborted">
                <div class="monday-group-header" onclick="toggleGroup('aborted')">
                    <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                    <div class="monday-group-color" style="background-color: #e2445c;"></div>
                    <h3 class="monday-group-title">Aborted</h3>
                    <span class="monday-group-count">{{ $abortedTasks->total() }}</span>
                </div>

                <div class="monday-group-content" id="group-aborted" style="display: none;">
                    <table class="monday-table">
                        <thead class="monday-table-header">
                            <tr>
                                <th>TASK</th>
                                <th>PERSON</th>
                                <th>STATUS</th>
                                <th>DEADLINE</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($abortedTasks as $task)
                            <tr class="monday-table-row" data-task-id="{{ $task->id }}">
                                <td class="monday-table-cell-task">
                                    <span class="monday-task-text text-decoration-line-through text-muted" onclick="editTask({{ $task->id }})" style="cursor: pointer;">{{ $task->content }}</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        @if($task->assigned_users && $task->assigned_users->count() > 0)
                                            @foreach($task->assigned_users->take(3) as $user)
                                                <div class="monday-avatar" style="background-color: {{ '#' . substr(md5($user->name), 0, 6) }}" title="{{ $user->name }}" onclick="editTask({{ $task->id }})" style="cursor: pointer;">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: {{ $task->getStatusColor() }}20; color: {{ $task->getStatusColor() }}; display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 6px 10px; cursor: pointer;" onclick="editTask({{ $task->id }})" title="Status: Click to edit task">
                                        <span>{{ $task->getStatusName() }}</span>
                                        </div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted" onclick="editTask({{ $task->id }})" style="cursor: pointer;">{{ $task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M d') : '-' }}</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; gap: 8px; justify-content: center; opacity: 1;">
                                        <button class="monday-action-btn delete" onclick="deleteTask({{ $task->id }})" title="Delete" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 8px; color: #ef4444;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="monday-empty">
                                        <div class="monday-empty-title">No aborted tasks</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    
@if($abortedTasks->hasPages())
<div class="monday-pagination">
    <button class="monday-pagination-btn" onclick="navigatePage('aborted', {{ $abortedTasks->currentPage() - 1 }})" {{ $abortedTasks->onFirstPage() ? 'disabled' : '' }}>
        Previous
    </button>
    <span class="monday-pagination-info">
        Page {{ $abortedTasks->currentPage() }} of {{ $abortedTasks->lastPage() }}
    </span>
    <button class="monday-pagination-btn" onclick="navigatePage('aborted', {{ $abortedTasks->currentPage() + 1 }})" {{ !$abortedTasks->hasMorePages() ? 'disabled' : '' }}>
        Next
    </button>
</div>
@endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="monday-modal" id="quickAddModal">
    <div class="monday-modal-content">
        <div class="monday-modal-header">
            <h3 class="monday-modal-title">New Task</h3>
            <button class="monday-modal-close" onclick="closeModal('quickAddModal')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <div class="monday-modal-body">
            <form id="quickAddForm">
                <div class="monday-form-group">
                    <label class="monday-form-label">Task Name *</label>
                    <input type="text" class="monday-form-input" id="quick_task_name" name="content" required>
                </div>
                <div class="monday-form-group">
                    <label class="monday-form-label">Description</label>
                    <textarea class="monday-form-textarea" id="quick_description" name="description"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="monday-form-group">
                            <label class="monday-form-label">Status *</label>
                            <select class="monday-form-select" id="quick_status" name="status" required>
                                @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="monday-form-group">
                            <label class="monday-form-label">Priority</label>
                            <select class="monday-form-select" id="quick_priority" name="priority">
                                <option value="0">Normal</option>
                                <option value="1">High</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="monday-form-group">
                            <label class="monday-form-label">Due Date *</label>
                            <input type="date" class="monday-form-input" id="quick_due_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="monday-form-group">
                            <label class="monday-form-label">Due Time *</label>
                            <input type="time" class="monday-form-input" id="quick_due_time" name="end_time" value="18:00" required>
                        </div>
                    </div>
                </div>
                <div class="monday-form-group">
                    <label class="monday-form-label">Story Points</label>
                    <input type="number" class="monday-form-input" id="quick_story_points" name="story_points" min="0" value="0">
                </div>
                <input type="hidden" id="quick_task_type" name="task_type" value="2">
                <input type="hidden" id="quick_tour" name="tour" value="">
                <input type="hidden" id="quick_assigned_user" name="assigned_user" value="">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </div>
        <div class="monday-modal-footer">
            <button type="button" class="monday-btn" onclick="closeModal('quickAddModal')">Cancel</button>
            <button type="button" class="monday-btn monday-btn-primary" onclick="saveQuickTask()">Create Task</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Monday.com Style Task Management JavaScript - Inline version

function toggleGroup(groupName) {
    const group = document.querySelector(`[data-group="${groupName}"]`);
    const content = document.getElementById(`group-${groupName}`);

    if (content.style.display === 'none') {
        content.style.display = 'block';
        group.classList.remove('monday-group-collapsed');
    } else {
        content.style.display = 'none';
        group.classList.add('monday-group-collapsed');
    }
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('monday-modal')) {
        closeModal(event.target.id);
    }
});

document.getElementById('newTaskBtn')?.addEventListener('click', function() {
    openModal('quickAddModal');
});

function openQuickAdd(group) {
    openModal('quickAddModal');
}

function saveQuickTask() {
    const form = document.getElementById('quickAddForm');
    const formData = new FormData(form);

    if (!formData.get('content') || !formData.get('status') || !formData.get('end_date') || !formData.get('end_time')) {
        alert('Please fill in all required fields');
        return;
    }

    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<div class="monday-spinner" style="width: 16px; height: 16px; border-width: 2px;"></div>';
    saveBtn.disabled = true;

    fetch('/task', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': formData.get('_token'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.route) {
            window.location.href = data.route || window.location.href;
        } else {
            alert('Task created successfully!');
            closeModal('quickAddModal');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating task. Please try again.');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// *** REMOVED INLINE EDITING FUNCTIONS: editInline, saveInlineEdit, cancelInlineEdit, updateTaskField ***

// Re-defining core action functions to only redirect to the edit page
function togglePriority(taskId, event) {
    event.stopPropagation();
    // Redirect to edit page instead of updating inline
    editTask(taskId);
}

function openStatusPicker(taskId, element) {
    event.stopPropagation();
    // Redirect to edit page instead of opening a dropdown
    editTask(taskId);
}

function openPersonPicker(taskId) {
    // Redirect to edit page
    editTask(taskId);
}

function openDatePicker(taskId) {
    // Redirect to edit page
    editTask(taskId);
}

function editTask(taskId) {
    window.location.href = `/task/${taskId}/edit`;
}

function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) {
        return;
    }

    fetch(`/task/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        const row = document.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                location.reload();
            }, 300);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting task');
    });
}

function navigatePage(group, page) {
    const url = new URL(window.location.href);
    url.searchParams.set(group + '_page', page);
    window.location.href = url.toString();
}

document.getElementById('taskSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.monday-table-row');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('quick_due_date');
    if (dateInput && !dateInput.value) {
        dateInput.value = today;
    }

    document.querySelectorAll('.monday-table-row').forEach(row => {
        row.style.transition = 'all 0.3s ease';
    });
});
</script>
@endpush