@extends('scaffold-interface.layouts.tabler-app')
@section('title','Tasks')
@section('content')
    <style>
        .task-table-wrapper {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .task-table {
            margin-bottom: 0;
        }
        
        .task-table thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        
        .task-table thead th {
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            color: #6c757d;
            padding: 12px 16px;
            border: none;
        }
        
        .task-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.15s ease;
        }
        
        .task-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .task-table tbody td {
            padding: 16px;
            vertical-align: middle;
            border: none;
        }
        
        .task-id-badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #e3f2fd;
            color: #1976d2;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .task-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .task-content .priority-star {
            color: #ffa726;
            flex-shrink: 0;
        }
        
        .deadline-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6c757d;
            font-size: 13px;
        }
        
        .deadline-wrapper.overdue {
            color: #dc3545;
        }
        
        .deadline-icon {
            width: 16px;
            height: 16px;
        }
        
        .tour-badge {
            display: inline-block;
            padding: 4px 10px;
            background-color: #e0f2f1;
            color: #00695c;
            border-radius: 4px;
            font-size: 12px;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }
        
        .tour-badge:hover {
            background-color: #b2dfdb;
        }
        
        .user-avatars {
            display: flex;
            gap: 4px;
            align-items: center;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: white;
            border: 2px solid white;
        }
        
        .type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .type-badge.personal {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        
        .type-badge.general {
            background-color: #e1f5fe;
            color: #0277bd;
        }
        
        .sp-badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #e3f2fd;
            color: #1976d2;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-badge.pending {
            background-color: #fff3e0;
            color: #e65100;
        }
        
        .status-badge.completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-badge.aborted {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .action-buttons {
            display: flex;
            gap: 4px;
            justify-content: center;
        }
        
        .action-icon {
            width: 20px;
            height: 20px;
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        
        .action-icon:hover {
            transform: scale(1.1);
        }
        
        .action-icon.edit {
            color: #f57c00;
        }
        
        .action-icon.delete {
            color: #d32f2f;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background-color: #fafafa;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            user-select: none;
        }
        
        .section-header:hover {
            background-color: #f5f5f5;
        }
        
        .section-icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }
        
        .section-title {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            color: #333;
        }
        
        .section-count {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            margin-left: auto;
        }
        
        .section-count.todo {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .section-count.completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .section-count.aborted {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .completed-row td {
            opacity: 0.7;
        }
        
        .completed-row .task-content span {
            text-decoration: line-through;
        }
        
        /* Header Controls */
        .task-header-controls {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 16px 20px;
            background: white;
            border-bottom: 1px solid #e9ecef;
        }
        
        .new-task-dropdown {
            position: relative;
        }
        
        .search-input {
            padding: 8px 12px 8px 36px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            width: 250px;
            font-size: 14px;
        }
        
        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .header-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.15s ease;
        }
        
        .header-btn:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
        }
        
        .header-btn svg {
            width: 18px;
            height: 18px;
        }
        
        /* Kanban View */
        .kanban-view {
            display: none;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        
        .kanban-view.active {
            display: grid;
        }
        
        .kanban-column {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
        }
        
        .kanban-column-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .kanban-column-title {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
        }
        
        .kanban-card {
            background: white;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .kanban-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .kanban-card-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #212529;
        }
        
        .kanban-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #6c757d;
        }
        
        .view-toggle {
            display: flex;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .view-toggle-btn {
            padding: 8px 16px;
            border: none;
            background: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .view-toggle-btn:not(:last-child) {
            border-right: 1px solid #dee2e6;
        }
        
        .view-toggle-btn.active {
            background: #0d6efd;
            color: white;
        }
        
        .view-toggle-btn:hover:not(.active) {
            background: #f8f9fa;
        }
    </style>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col">
                    <div class="page-pretitle">Project Management</div>
                    <h2 class="page-title">Tasks</h2>
                </div>
                <div class="col-auto">
                    <div class="view-toggle">
                        <button class="view-toggle-btn active" id="mainTableBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="4" y="4" width="16" height="4" rx="1" />
                                <rect x="4" y="12" width="16" height="4" rx="1" />
                                <rect x="4" y="20" width="16" height="4" rx="1" />
                            </svg>
                            Main table
                        </button>
                        <button class="view-toggle-btn" id="kanbanBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="4" y="4" width="6" height="16" rx="1" />
                                <rect x="14" y="4" width="6" height="10" rx="1" />
                            </svg>
                            Kanban
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Header Controls -->
            <div class="task-header-controls">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        New task
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('task.create') }}">Create Task</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#quickAddModal">Quick Add</a></li>
                    </ul>
                </div>

                <div class="position-relative">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="10" cy="10" r="7" />
                        <line x1="21" y1="21" x2="15" y2="15" />
                    </svg>
                    <input type="text" class="search-input" placeholder="Search" id="taskSearch">
                </div>

                <button class="header-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="9" />
                        <line x1="12" y1="8" x2="12.01" y2="8" />
                        <polyline points="11 12 12 12 12 16 13 16" />
                    </svg>
                    Person
                </button>

                <button class="header-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5.5 5h13a1 1 0 0 1 .5 1.5l-5 5.5l0 7l-4 -3l0 -4l-5 -5.5a1 1 0 0 1 .5 -1.5" />
                    </svg>
                    Filter
                </button>

                <button class="header-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 9l4 -4l4 4m-4 -4v14" />
                        <path d="M21 15l-4 4l-4 -4m4 4v-14" />
                    </svg>
                    Sort
                </button>

                <button class="header-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    Hide
                </button>

                <button class="header-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="4" y="4" width="6" height="6" rx="1" />
                        <rect x="14" y="4" width="6" height="6" rx="1" />
                        <rect x="4" y="14" width="6" height="6" rx="1" />
                        <rect x="14" y="14" width="6" height="6" rx="1" />
                    </svg>
                    Group by
                </button>

                <button class="header-btn ms-auto" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="1" />
                        <circle cx="12" cy="19" r="1" />
                        <circle cx="12" cy="5" r="1" />
                    </svg>
                </button>
            </div>

            <!-- Main Table View -->
            <div id="mainTableView" class="task-table-wrapper">
                <!-- To-Do Section -->
                <div class="section-header" data-bs-toggle="collapse" data-bs-target="#todoSection">
                    <svg class="section-icon text-primary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 8v4" />
                        <path d="M12 16h.01" />
                    </svg>
                    <h3 class="section-title">To-Do</h3>
                    <span class="section-count todo">{{ $tasks->where('status.is_completed', '!=', true)->where('status.is_aborted', '!=', true)->count() }}</span>
                </div>
                
                <div id="todoSection" class="collapse show">
                    <table class="task-table table">
                        <thead>
                            <tr>
                                <th style="width: 40px"><input type="checkbox" class="form-check-input"></th>
                                <th style="width: 35%">TASK</th>
                                <th style="width: 40px"></th>
                                <th style="width: 40px"></th>
                                <th style="width: 140px">ASSIGN TO</th>
                                <th style="width: 120px">STATUS</th>
                                <th style="width: 120px">TYPE</th>
                                <th style="width: 100px">TASK ID</th>
                                <th style="width: 100px">ESTIMATED SP</th>
                                <th style="width: 100px">EPIC</th>
                                <th style="width: 80px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td colspan="10" class="text-muted small">+ Add task</td>
                            </tr>
                            @forelse($tasks->where('status.is_completed', '!=', true)->where('status.is_aborted', '!=', true) as $task)
                            <tr class="clickable-row" onclick="window.location.href='{{ route('task.show', ['id' => $task->id]) }}'">
                                <td onclick="event.stopPropagation();">
                                    <input type="checkbox" class="form-check-input">
                                </td>
                                <td>
                                    <div class="task-content">
                                        @if($task->priority)
                                        <svg class="priority-star" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        @endif
                                        <span>{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="color: #cbd5e0; cursor: pointer;">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                    </svg>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="color: #cbd5e0; cursor: pointer;" title="{{ $task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M d, Y H:i') : 'No deadline' }}">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="12" r="9" />
                                        <polyline points="12 7 12 12 15 15" />
                                    </svg>
                                </td>
                                <td>
                                    <div class="user-avatars">
                                        @if($task->assigned_users && $task->assigned_users->count() > 0)
                                            @foreach($task->assigned_users->take(3) as $user)
                                                <div class="user-avatar" 
                                                     style="background-color: {{ '#' . substr(md5($user->name), 0, 6) }}"
                                                     title="{{ $user->name }}">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endforeach
                                            @if($task->assigned_users->count() > 3)
                                            <div class="user-avatar" style="background-color: #757575" title="+{{ $task->assigned_users->count() - 3 }} more">
                                                +{{ $task->assigned_users->count() - 3 }}
                                            </div>
                                            @endif
                                        @else
                                        <div class="user-avatar" style="background-color: #e0e0e0; color: #757575;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="12" cy="7" r="4" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                            </svg>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge" style="background-color: {{ $task->getStatusColor() }}20; color: {{ $task->getStatusColor() }}">
                                        {{ $task->getStatusName() }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $taskTypeValue = is_numeric($task->task_type) ? (\App\Task::$taskTypes[$task->task_type] ?? 'General') : $task->task_type;
                                    @endphp
                                    @if($taskTypeValue == 'Personal')
                                    <span class="type-badge personal">Personal</span>
                                    @else
                                    <span class="type-badge general">{{ $taskTypeValue }}</span>
                                    @endif
                                </td>
                                <td><span class="text-muted">TREP-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    @if(isset($task->estimated_sp) && $task->estimated_sp > 0)
                                    <span class="sp-badge">{{ $task->estimated_sp }} SP</span>
                                    @elseif(isset($task->story_points) && $task->story_points > 0)
                                    <span class="sp-badge">{{ $task->story_points }} SP</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->epic)
                                    <span class="badge" style="background-color: {{ $task->epic->color ?? '#6c757d' }}20; color: {{ $task->epic->color ?? '#6c757d' }}">
                                        {{ $task->epic->name }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div class="action-buttons">
                                        <a href="{{ route('task.edit', ['id' => $task->id]) }}" title="Edit">
                                            <svg class="action-icon edit" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        <a href="#" onclick="event.preventDefault(); confirmDelete(event, '{{ route('task.destroy', $task->id) }}')" title="Delete">
                                            <svg class="action-icon delete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="text-muted">No pending tasks</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Completed Section -->
                <div class="section-header" data-bs-toggle="collapse" data-bs-target="#completedSection">
                    <svg class="section-icon text-success" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <h3 class="section-title">Completed</h3>
                    <span class="section-count completed">{{ $tasks->where('status.is_completed', true)->count() }}</span>
                </div>
                
                <div id="completedSection" class="collapse">
                    <table class="task-table table">
                        <thead>
                            <tr>
                                <th style="width: 40px"><input type="checkbox" class="form-check-input"></th>
                                <th style="width: 35%">TASK</th>
                                <th style="width: 40px"></th>
                                <th style="width: 40px"></th>
                                <th style="width: 140px">ASSIGN TO</th>
                                <th style="width: 120px">STATUS</th>
                                <th style="width: 120px">TYPE</th>
                                <th style="width: 100px">TASK ID</th>
                                <th style="width: 100px">ESTIMATED SP</th>
                                <th style="width: 100px">EPIC</th>
                                <th style="width: 80px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks->where('status.is_completed', true) as $task)
                            <tr class="clickable-row completed-row" onclick="window.location.href='{{ route('task.show', ['id' => $task->id]) }}'">
                                <td onclick="event.stopPropagation();">
                                    <input type="checkbox" class="form-check-input" checked>
                                </td>
                                <td>
                                    <div class="task-content">
                                        <span>{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class="user-avatars">
                                        @if($task->assigned_users && $task->assigned_users->count() > 0)
                                            @foreach($task->assigned_users->take(3) as $user)
                                                <div class="user-avatar" 
                                                     style="background-color: {{ '#' . substr(md5($user->name), 0, 6) }}"
                                                     title="{{ $user->name }}">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endforeach
                                            @if($task->assigned_users->count() > 3)
                                            <div class="user-avatar" style="background-color: #757575" title="+{{ $task->assigned_users->count() - 3 }} more">
                                                +{{ $task->assigned_users->count() - 3 }}
                                            </div>
                                            @endif
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td><span class="status-badge completed">Completed</span></td>
                                <td>
                                    @php
                                        $taskTypeValue = is_numeric($task->task_type) ? (\App\Task::$taskTypes[$task->task_type] ?? 'General') : $task->task_type;
                                    @endphp
                                    <span class="type-badge general">{{ $taskTypeValue }}</span>
                                </td>
                                <td><span class="text-muted">TREP-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    @if(isset($task->estimated_sp) && $task->estimated_sp > 0)
                                    <span class="sp-badge">{{ $task->estimated_sp }} SP</span>
                                    @elseif(isset($task->story_points) && $task->story_points > 0)
                                    <span class="sp-badge">{{ $task->story_points }} SP</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->epic)
                                    <span class="badge" style="background-color: {{ $task->epic->color ?? '#6c757d' }}20; color: {{ $task->epic->color ?? '#6c757d' }}">
                                        {{ $task->epic->name }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div class="action-buttons">
                                        <a href="{{ route('task.edit', ['id' => $task->id]) }}" title="Edit">
                                            <svg class="action-icon edit" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        <a href="#" onclick="event.preventDefault(); confirmDelete(event, '{{ route('task.destroy', $task->id) }}')" title="Delete">
                                            <svg class="action-icon delete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="text-muted">No completed tasks</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Aborted Section -->
                <div class="section-header" data-bs-toggle="collapse" data-bs-target="#abortedSection">
                    <svg class="section-icon text-danger" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 9l6 6" />
                        <path d="M15 9l-6 6" />
                    </svg>
                    <h3 class="section-title">Aborted</h3>
                    <span class="section-count aborted">{{ $tasks->where('status.is_aborted', true)->count() }}</span>
                </div>
                
                <div id="abortedSection" class="collapse">
                    <table class="task-table table">
                        <thead>
                            <tr>
                                <th style="width: 40px"><input type="checkbox" class="form-check-input"></th>
                                <th style="width: 35%">TASK</th>
                                <th style="width: 40px"></th>
                                <th style="width: 40px"></th>
                                <th style="width: 140px">ASSIGN TO</th>
                                <th style="width: 120px">STATUS</th>
                                <th style="width: 120px">TYPE</th>
                                <th style="width: 100px">TASK ID</th>
                                <th style="width: 100px">ESTIMATED SP</th>
                                <th style="width: 100px">EPIC</th>
                                <th style="width: 80px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks->where('status.is_aborted', true) as $task)
                            <tr class="clickable-row completed-row" onclick="window.location.href='{{ route('task.show', ['id' => $task->id]) }}'">
                                <td onclick="event.stopPropagation();">
                                    <input type="checkbox" class="form-check-input">
                                </td>
                                <td>
                                    <div class="task-content">
                                        <span>{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class="user-avatars">
                                        @if($task->assigned_users && $task->assigned_users->count() > 0)
                                            @foreach($task->assigned_users->take(3) as $user)
                                                <div class="user-avatar" 
                                                     style="background-color: {{ '#' . substr(md5($user->name), 0, 6) }}"
                                                     title="{{ $user->name }}">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endforeach
                                            @if($task->assigned_users->count() > 3)
                                            <div class="user-avatar" style="background-color: #757575" title="+{{ $task->assigned_users->count() - 3 }} more">
                                                +{{ $task->assigned_users->count() - 3 }}
                                            </div>
                                            @endif
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td><span class="status-badge aborted">Aborted</span></td>
                                <td>
                                    @php
                                        $taskTypeValue = is_numeric($task->task_type) ? (\App\Task::$taskTypes[$task->task_type] ?? 'General') : $task->task_type;
                                    @endphp
                                    <span class="type-badge general">{{ $taskTypeValue }}</span>
                                </td>
                                <td><span class="text-muted">TREP-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    @if(isset($task->estimated_sp) && $task->estimated_sp > 0)
                                    <span class="sp-badge">{{ $task->estimated_sp }} SP</span>
                                    @elseif(isset($task->story_points) && $task->story_points > 0)
                                    <span class="sp-badge">{{ $task->story_points }} SP</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->epic)
                                    <span class="badge" style="background-color: {{ $task->epic->color ?? '#6c757d' }}20; color: {{ $task->epic->color ?? '#6c757d' }}">
                                        {{ $task->epic->name }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div class="action-buttons">
                                        <a href="{{ route('task.edit', ['id' => $task->id]) }}" title="Edit">
                                            <svg class="action-icon edit" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        <a href="#" onclick="event.preventDefault(); confirmDelete(event, '{{ route('task.destroy', $task->id) }}')" title="Delete">
                                            <svg class="action-icon delete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="text-muted">No aborted tasks</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <h3 class="section-title">Completed</h3>
                    <span class="section-count completed">{{ $tasks->where('status.is_completed', true)->count() }}</span>
                </div>
                
                <div id="completedSection" class="collapse">
                    <table class="task-table table">
                        <thead>
                            <tr>
                                <th style="width: 40px"><input type="checkbox" class="form-check-input"></th>
                                <th style="width: 35%">TASK</th>
                                <th style="width: 40px"></th>
                                <th style="width: 40px"></th>
                                <th style="width: 140px">OWNER</th>
                                <th style="width: 120px">STATUS</th>
                                <th style="width: 120px">TYPE</th>
                                <th style="width: 100px">TASK ID</th>
                                <th style="width: 100px">ESTIMATED SP</th>
                                <th style="width: 100px">EPIC</th>
                                <th style="width: 80px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks->where('status.is_completed', true) as $task)
                            <tr class="clickable-row completed-row" onclick="window.location.href='{{ route('task.show', ['id' => $task->id]) }}'">
                                <td onclick="event.stopPropagation();">
                                    <input type="checkbox" class="form-check-input" checked>
                                </td>
                                <td>
                                    <div class="task-content">
                                        <span>{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td colspan="2"></td>
                                <td>{{ $task->show_assigned_users ?: '-' }}</td>
                                <td><span class="status-badge completed">Completed</span></td>
                                <td><span class="type-badge general">{{ $task->task_type }}</span></td>
                                <td><span class="text-muted">TREP-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    @if(isset($task->story_points) && $task->story_points > 0)
                                    <span class="sp-badge">{{ $task->story_points }} SP</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><span class="text-muted">-</span></td>
                                <td onclick="event.stopPropagation();">
                                    <div class="action-buttons">
                                        <a href="{{ route('task.edit', ['id' => $task->id]) }}" title="Edit">
                                            <svg class="action-icon edit" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        <a href="#" onclick="event.preventDefault(); confirmDelete(event, '{{ route('task.destroy', $task->id) }}')" title="Delete">
                                            <svg class="action-icon delete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="text-muted">No completed tasks</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kanban View -->
            <div id="kanbanView" class="kanban-view">
                @foreach($statuses as $status)
                <div class="kanban-column">
                    <div class="kanban-column-header">
                        <h3 class="kanban-column-title">{{ $status->name }}</h3>
                        <span class="badge bg-secondary">{{ $tasks->where('status_id', $status->id)->count() }}</span>
                    </div>
                    <div class="kanban-cards">
                        @foreach($tasks->where('status_id', $status->id) as $task)
                        <div class="kanban-card" onclick="window.location.href='{{ route('task.show', ['id' => $task->id]) }}'">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="kanban-card-title">{{ $task->content }}</div>
                                @if($task->priority)
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color: #ffa726;">
                                    <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                </svg>
                                @endif
                            </div>
                            
                            <div class="d-flex gap-2 mb-2 flex-wrap">
                                @php
                                    $taskTypeValue = is_numeric($task->task_type) ? (\App\Task::$taskTypes[$task->task_type] ?? 'General') : $task->task_type;
                                @endphp
                                @if($taskTypeValue == 'Personal')
                                <span class="badge bg-purple-lt">Personal</span>
                                @else
                                <span class="badge bg-cyan-lt">{{ $taskTypeValue }}</span>
                                @endif
                                
                                @if($task->tour)
                                <span class="badge bg-green-lt">{{ $task->tour->name }}</span>
                                @endif
                            </div>
                            
                            <div class="kanban-card-meta">
                                <span class="text-muted small">TREP-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    @if(isset($task->estimated_sp) && $task->estimated_sp > 0)
                                    <span class="sp-badge">{{ $task->estimated_sp }} SP</span>
                                    @elseif(isset($task->story_points) && $task->story_points > 0)
                                    <span class="sp-badge">{{ $task->story_points }} SP</span>
                                    @endif
                                    
                                    @if($task->dead_line)
                                    <span class="text-muted small" title="Deadline">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="vertical-align: middle;">
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($task->dead_line)->format('M d') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($task->assigned_users && $task->assigned_users->count() > 0)
                            <div class="mt-2 d-flex align-items-center gap-1">
                                @foreach($task->assigned_users->take(3) as $user)
                                <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px; background-color: {{ '#' . substr(md5($user->name), 0, 6) }}" title="{{ $user->name }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                @endforeach
                                @if($task->assigned_users->count() > 3)
                                <span class="text-muted small">+{{ $task->assigned_users->count() - 3 }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endforeach
                        
                        @if($tasks->where('status_id', $status->id)->count() == 0)
                        <div class="text-center text-muted py-3 small">
                            No tasks in this status
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // View Toggle
        document.getElementById('mainTableBtn').addEventListener('click', function() {
            document.getElementById('mainTableView').style.display = 'block';
            document.getElementById('kanbanView').classList.remove('active');
            this.classList.add('active');
            document.getElementById('kanbanBtn').classList.remove('active');
        });

        document.getElementById('kanbanBtn').addEventListener('click', function() {
            document.getElementById('mainTableView').style.display = 'none';
            document.getElementById('kanbanView').classList.add('active');
            this.classList.add('active');
            document.getElementById('mainTableBtn').classList.remove('active');
        });

        // Search Functionality
        document.getElementById('taskSearch').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.task-table tbody tr:not(:first-child)');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });

            // Also filter kanban cards
            const cards = document.querySelectorAll('.kanban-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Delete Confirmation
        function confirmDelete(event, url) {
            event.preventDefault();
            event.stopPropagation();
            
            if (confirm('Are you sure you want to delete this task?')) {
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
                        alert('Error deleting task');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting task');
                });
            }
        }
    </script>
@endsection