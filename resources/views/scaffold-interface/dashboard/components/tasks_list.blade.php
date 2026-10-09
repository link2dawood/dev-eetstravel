    <style>
        .dashboard-task-list {
            --monday-primary: #066fd1;
            --monday-primary-hover: #055db0;
            --monday-primary-selected: #d9ebff;
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
        .dashboard-task-list,
        .dashboard-task-list * {
            box-sizing: border-box;
        }


        .dashboard-task-list {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background-color: transparent;
            padding: 0;
        }

        .wrapper {
            max-width: 1200px;
            margin: 0 auto;
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

        .monday-table {
            overflow-x: auto;
        }

        .monday-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .monday-table-header {
            background: var(--monday-background);
        }

        .monday-table-header th {
            padding: 12px 18px;
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
            padding: 10px 18px;
            vertical-align: middle;
            border-bottom: 1px solid var(--monday-border);
        }

        .monday-table-cell-task {
            min-width: 200px;
            max-width: 400px;
        }

        .monday-table-header th:first-child,
        .monday-table-row td:first-child {
            padding-left: 18px;
        }

        .monday-table-header th:last-child,
        .monday-table-row td:last-child {
            padding-right: 18px;
        }

        .monday-table-header th:last-child {
            text-align: right;
        }

        .monday-task-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .monday-task-priority-icon {
            color: var(--monday-warning);
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .monday-task-priority-icon:hover {
            transform: scale(1.2);
        }

        .monday-task-text {
            color: var(--monday-text-primary);
            font-size: 14px;
        }

        .monday-editable {
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer !important;
            outline: none;
            box-shadow: none;
            transition: background-color 0.2s ease;
            min-height: 32px;
            display: flex;
            align-items: center;
        }

        .monday-editable:hover {
            background: var(--monday-background-hover);
            outline: none;
            box-shadow: none;
        }

        .monday-editable:focus,
        .monday-editable:active {
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
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            min-width: 85px;
            gap: 4px;
        }

        .monday-status-clickable {
            min-width: 95px;
            font-weight: 600;
            user-select: none;
        }

        .monday-status-clickable:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            filter: brightness(0.98);
        }

        .monday-person {
            display: flex;
            align-items: center;
            gap: 4px;
            max-width: 100px;
            overflow: hidden;
        }

        .monday-person .monday-avatar:nth-child(n+4) {
            display: none;
        }

        .monday-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--monday-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
            color: white;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .monday-avatar-add {
            background: var(--monday-background-hover);
            border: 2px dashed var(--monday-border);
            color: var(--monday-text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .monday-avatar-add:hover {
            border-color: var(--monday-primary);
            color: var(--monday-primary);
        }

        .monday-date {
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--monday-text-secondary);
            font-size: 12px;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .monday-date:hover {
            background: var(--monday-background-hover);
        }

        .monday-date.overdue {
            color: var(--monday-danger);
        }

        .monday-actions {
            display: flex;
            gap: 4px;
            justify-content: flex-end;
            opacity: 0;
            transition: opacity 0.2s ease;
            width: 100%;
        }

        .monday-table-row:hover .monday-actions {
            opacity: 1;
        }

        .monday-action-btn {
            padding: 6px;
            border-radius: 8px;
            background: white;
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .monday-action-btn:hover {
            background: #f8fafc;
        }

        .monday-action-btn svg {
            width: 18px;
            height: 18px;
            color: currentColor !important;
            stroke: currentColor !important;
        }

        .monday-action-btn.edit {
            color: #d97706;
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .monday-action-btn.edit:hover {
            color: #b45309 !important;
            border-color: #fdba74 !important;
            background: #fffbeb !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12);
        }

        .monday-action-btn.edit svg,
        .monday-action-btn.edit:hover svg,
        .monday-action-btn.edit:focus svg,
        .monday-action-btn.edit:active svg {
            color: #b45309 !important;
            stroke: #b45309 !important;
        }

        .monday-action-btn.delete {
            color: #e2445c;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .monday-action-btn.delete:hover {
            color: #be123c !important;
            border-color: #fda4af !important;
            background: #fff1f2 !important;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.1);
        }

        .monday-action-btn.delete svg,
        .monday-action-btn.delete:hover svg,
        .monday-action-btn.delete:focus svg,
        .monday-action-btn.delete:active svg {
            color: #be123c !important;
            stroke: #be123c !important;
        }

        .task-toast-container {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 1080;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .task-toast {
            min-width: 280px;
            max-width: min(360px, calc(100vw - 36px));
            padding: 12px 14px;
            border: 1px solid #bfdbfe;
            border-left: 4px solid var(--monday-primary);
            border-radius: 10px;
            background: #ffffff;
            color: var(--monday-text-primary);
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.16);
            opacity: 0;
            transform: translateY(-8px);
            transition: opacity 0.18s ease, transform 0.18s ease;
            pointer-events: auto;
        }

        .task-toast.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .task-toast-title {
            margin-bottom: 3px;
            font-size: 13px;
            font-weight: 700;
            color: #0f4fb8;
        }

        .task-toast-message {
            font-size: 13px;
            line-height: 1.4;
            color: #475569;
        }

        .monday-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }

        .monday-epic {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }

        .monday-sp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 6px;
            background: #e6f7ff;
            color: #0073ea;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            min-width: 35px;
            cursor: pointer;
        }

        .monday-sp:hover {
            background: #bae6fd;
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

        .monday-pagination-btn.disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .monday-pagination-info {
            font-size: 13px;
            color: var(--monday-text-secondary);
            padding: 0 12px;
        }

        .monday-empty {
            text-align: center;
            padding: 60px 20px;
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

        .monday-dropdown-menu {
            position: fixed;
            z-index: 9999;
            background: white;
            border: 1px solid var(--monday-border);
            border-radius: 8px;
            box-shadow: var(--monday-shadow-hover);
            padding: 8px;
            min-width: 180px;
            max-height: 300px;
            overflow-y: auto;
        }

        .monday-dropdown-item {
            padding: 10px 14px;
            cursor: pointer;
            border-radius: 6px;
            margin: 3px 0;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .monday-dropdown-item:hover {
            transform: none;
        }

        .text-muted {
            color: #9ca3af;
        }

        .text-decoration-line-through {
            text-decoration: line-through;
        }

        .text-center {
            text-align: center;
        }

        .header {
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--monday-text-primary);
            margin-bottom: 8px;
        }

        .dashboard-task-list .monday-group-collapse-icon,
        .dashboard-task-list .monday-search-icon,
        .dashboard-task-list .monday-task-priority-icon,
        .dashboard-task-list .monday-action-btn.edit,
        .dashboard-task-list .monday-action-btn.edit:hover,
        .dashboard-task-list .monday-action-btn.edit:focus,
        .dashboard-task-list .monday-action-btn.edit:active {
            color: var(--monday-primary);
        }

        .dashboard-task-list .monday-action-btn.edit {
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .dashboard-task-list .monday-action-btn.edit svg,
        .dashboard-task-list .monday-action-btn.edit:hover svg,
        .dashboard-task-list .monday-action-btn.edit:focus svg,
        .dashboard-task-list .monday-action-btn.edit:active svg {
            color: var(--monday-primary) !important;
            stroke: var(--monday-primary) !important;
        }

        .dashboard-task-list .monday-status {
            color: var(--monday-primary);
            background-color: #eff6ff;
        }
        .dashboard-task-list .monday-action-btn {
            border: 0 !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .dashboard-task-list .monday-action-btn svg,
        .dashboard-task-list .monday-action-btn:hover svg,
        .dashboard-task-list .monday-action-btn:focus svg,
        .dashboard-task-list .monday-action-btn:active svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
            width: 19px;
            height: 19px;
            stroke-width: 2.5px;
        }

        .dashboard-task-list .monday-action-btn svg path,
        .dashboard-task-list .monday-action-btn svg line,
        .dashboard-task-list .monday-action-btn svg polyline {
            stroke: #ffffff !important;
        }

        .dashboard-task-list .monday-action-btn.edit,
        .dashboard-task-list .monday-action-btn.edit:hover,
        .dashboard-task-list .monday-action-btn.edit:focus,
        .dashboard-task-list .monday-action-btn.edit:active {
            background: #066fd1 !important;
            border-color: #066fd1 !important;
            color: #ffffff !important;
        }

        .dashboard-task-list .monday-action-btn.edit:hover,
        .dashboard-task-list .monday-action-btn.edit:focus {
            background: #055db0 !important;
        }

        .dashboard-task-list .monday-action-btn.delete,
        .dashboard-task-list .monday-action-btn.delete:hover,
        .dashboard-task-list .monday-action-btn.delete:focus,
        .dashboard-task-list .monday-action-btn.delete:active {
            background: #d63939 !important;
            border-color: #d63939 !important;
            color: #ffffff !important;
        }

        .dashboard-task-list .monday-action-btn.delete:hover,
        .dashboard-task-list .monday-action-btn.delete:focus {
            background: #b02a2a !important;
        }
    </style>
<div class="dashboard-task-list">
    <div class="wrapper">
        <div class="header">
            <h1>Tasks</h1>
        </div>

        <div class="monday-board">
            <div class="monday-toolbar">
                <button class="monday-btn monday-btn-primary create-action-btn" onclick="newTask()">
                    <span class="create-action-icon" aria-hidden="true">+</span>
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

            <!-- TO-DO GROUP -->
            <div class="monday-group" data-group="todo">
                <div class="monday-group-header" onclick="toggleGroup('todo')">
                    <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                    <div class="monday-group-color" style="background-color: #0073ea;"></div>
                    <h3 class="monday-group-title">To-Do</h3>
                    <span class="monday-group-count" title="Tasks on this page">{{ $todoTasks->count() }}</span>
                </div>

                <div class="monday-group-content" id="group-todo">
                    <table class="monday-table">
                        <thead class="monday-table-header">
                            <tr>
                                <th style="width: 35%">TASK</th>
                                <th style="width: 110px">PERSON</th>
                                <th style="width: 100px">STATUS</th>
                                <th style="width: 110px">DEADLINE</th>
                                <th style="width: 80px">PRIORITY</th>
                                <th style="width: 90px">EPIC</th>
                                <th style="width: 50px">SP</th>
                                <th style="width: 100px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todoTasks as $task)
                            @php
                                $taskPeople = $task->assigned_users->isNotEmpty()
                                    ? $task->assigned_users
                                    : collect([$task->assignedTo])->filter();
                            @endphp
                            <tr class="monday-table-row" data-task-id="{{ $task->id }}">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <svg class="monday-task-priority-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="{{ $task->priority ? 'currentColor' : 'none' }}" @if(!$task->priority) style="opacity: .3" @endif>
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        <span class="monday-task-text">{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        @forelse($taskPeople as $person)
                                            <div class="monday-avatar" title="{{ $person->name }}">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($person->name, 0, 2)) }}</div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status">{{ $task->getRelation('status')->name ?? 'Unknown' }}</div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-date {{ $task->isOverdue() ? 'overdue' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        <span>{{ $task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M j, H:i') : '—' }}</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-badge">{{ $task->priority ? 'High' : 'Normal' }}</span>
                                </td>
                                <td class="monday-table-cell">{{ $task->epic->name ?? '—' }}</td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-sp">{{ $task->story_points ?? '—' }}</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask({{ $task->id }})" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button class="monday-action-btn delete" onclick="deleteTask({{ $task->id }})" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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
                            <tr class="monday-table-row">
                                <td class="monday-table-cell text-center" colspan="8">No to-do tasks found.</td>
                            </tr>
                            @endforelse
                            @if(false)
                            <tr class="monday-table-row" data-task-id="1">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <svg class="monday-task-priority-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" onclick="togglePriority(1, event)">
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        <span class="monday-task-text monday-editable" data-field="content" data-task-id="1" onclick="editInline(this)">hello this is meeee</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #0073ea;">ZA</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status monday-status-clickable" style="background-color: #fdab3d20; color: #fdab3d;" onclick="openStatusPicker(1, this)">
                                        <span>Pending</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-date overdue" onclick="showTaskToast('Date editing is not available yet.')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        <span>Nov 20, 18:00</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-badge" style="background-color: #fef3c7; color: #d97706;">High</span>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">-</span>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-sp monday-editable" data-field="story_points" data-task-id="1" onclick="editInline(this)">0</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask(1)" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button class="monday-action-btn delete" onclick="deleteTask(1)" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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
                            <tr class="monday-table-row" data-task-id="2">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <svg class="monday-task-priority-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="opacity: 0.3;" onclick="togglePriority(2, event)">
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        <span class="monday-task-text monday-editable" data-field="content" data-task-id="2" onclick="editInline(this)">this is mine task</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #00c875;">ZA</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status monday-status-clickable" style="background-color: #fdab3d20; color: #fdab3d;" onclick="openStatusPicker(2, this)">
                                        <span>Pending</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-date" onclick="showTaskToast('Date editing is not available yet.')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        <span>Nov 28, 18:00</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-badge" style="background-color: #e5e7eb; color: #6b7280;">Normal</span>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">-</span>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-sp monday-editable" data-field="story_points" data-task-id="2" onclick="editInline(this)">0</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask(2)" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button class="monday-action-btn delete" onclick="deleteTask(2)" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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
                            @endif
                        </tbody>
                    </table>
                    <div class="monday-pagination">
                        <a class="monday-pagination-btn {{ $todoTasks->onFirstPage() ? 'disabled' : '' }}" href="{{ $todoTasks->previousPageUrl() ?? '#' }}" @if($todoTasks->onFirstPage()) aria-disabled="true" tabindex="-1" @endif>Previous</a>
                        <span class="monday-pagination-info">Page {{ $todoTasks->currentPage() }}</span>
                        <a class="monday-pagination-btn {{ $todoTasks->hasMorePages() ? '' : 'disabled' }}" href="{{ $todoTasks->nextPageUrl() ?? '#' }}" @unless($todoTasks->hasMorePages()) aria-disabled="true" tabindex="-1" @endunless>Next</a>
                    </div>
                </div>
            </div>

            <!-- COMPLETED GROUP -->
            <div class="monday-group" data-group="completed">
                <div class="monday-group-header" onclick="toggleGroup('completed')">
                    <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                    <div class="monday-group-color" style="background-color: #00c875;"></div>
                    <h3 class="monday-group-title">Completed</h3>
                    <span class="monday-group-count" title="Tasks on this page">{{ $completedTasks->count() }}</span>
                </div>

                <div class="monday-group-content" id="group-completed" style="display: none;">
                    <table class="monday-table">
                        <thead class="monday-table-header">
                            <tr>
                                <th style="width: 35%">TASK</th>
                                <th style="width: 110px">PERSON</th>
                                <th style="width: 100px">STATUS</th>
                                <th style="width: 110px">DEADLINE</th>
                                <th style="width: 80px">PRIORITY</th>
                                <th style="width: 90px">EPIC</th>
                                <th style="width: 50px">SP</th>
                                <th style="width: 100px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedTasks as $task)
                            @php
                                $taskPeople = $task->assigned_users->isNotEmpty()
                                    ? $task->assigned_users
                                    : collect([$task->assignedTo])->filter();
                            @endphp
                            <tr class="monday-table-row completed" data-task-id="{{ $task->id }}">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text">{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        @forelse($taskPeople as $person)
                                            <div class="monday-avatar" title="{{ $person->name }}">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($person->name, 0, 2)) }}</div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status">{{ $task->getRelation('status')->name ?? 'Unknown' }}</div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">{{ $task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M j, H:i') : '—' }}</span>
                                </td>
                                <td class="monday-table-cell text-center">{{ $task->priority ? 'High' : 'Normal' }}</td>
                                <td class="monday-table-cell">{{ $task->epic->name ?? '—' }}</td>
                                <td class="monday-table-cell text-center">{{ $task->story_points ?? '—' }}</td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask({{ $task->id }})" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button class="monday-action-btn delete" onclick="deleteTask({{ $task->id }})" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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
                            <tr class="monday-table-row">
                                <td class="monday-table-cell text-center" colspan="8">No completed tasks found.</td>
                            </tr>
                            @endforelse
                            @if(false)
                            <tr class="monday-table-row completed" data-task-id="100">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text">Velit</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #1f2937;">MZ</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: #00c87520; color: #00c875;">Completed</div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">Nov 08</span>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="text-muted">-</span>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">-</span>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="text-muted">-</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask(100)" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="monday-pagination">
                        <a class="monday-pagination-btn {{ $completedTasks->onFirstPage() ? 'disabled' : '' }}" href="{{ $completedTasks->previousPageUrl() ?? '#' }}" @if($completedTasks->onFirstPage()) aria-disabled="true" tabindex="-1" @endif>Previous</a>
                        <span class="monday-pagination-info">Page {{ $completedTasks->currentPage() }}</span>
                        <a class="monday-pagination-btn {{ $completedTasks->hasMorePages() ? '' : 'disabled' }}" href="{{ $completedTasks->nextPageUrl() ?? '#' }}" @unless($completedTasks->hasMorePages()) aria-disabled="true" tabindex="-1" @endunless>Next</a>
                    </div>
                </div>
            </div>

            <!-- ABORTED GROUP -->
            <div class="monday-group" data-group="aborted">
                <div class="monday-group-header" onclick="toggleGroup('aborted')">
                    <svg class="monday-group-collapse-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                    <div class="monday-group-color" style="background-color: #e2445c;"></div>
                    <h3 class="monday-group-title">Aborted</h3>
                    <span class="monday-group-count" title="Tasks on this page">{{ $abortedTasks->count() }}</span>
                </div>

                <div class="monday-group-content" id="group-aborted" style="display: none;">
                    <table class="monday-table">
                        <thead class="monday-table-header">
                            <tr>
                                <th style="width: 35%">TASK</th>
                                <th style="width: 110px">PERSON</th>
                                <th style="width: 100px">STATUS</th>
                                <th style="width: 110px">DEADLINE</th>
                                <th style="width: 100px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($abortedTasks as $task)
                            @php
                                $taskPeople = $task->assigned_users->isNotEmpty()
                                    ? $task->assigned_users
                                    : collect([$task->assignedTo])->filter();
                            @endphp
                            <tr class="monday-table-row" data-task-id="{{ $task->id }}">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text text-decoration-line-through text-muted">{{ $task->content }}</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        @forelse($taskPeople as $person)
                                            <div class="monday-avatar" title="{{ $person->name }}">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($person->name, 0, 2)) }}</div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status">{{ $task->getRelation('status')->name ?? 'Unknown' }}</div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">{{ $task->dead_line ? \Carbon\Carbon::parse($task->dead_line)->format('M j, H:i') : '—' }}</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask({{ $task->id }})" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button class="monday-action-btn delete" onclick="deleteTask({{ $task->id }})" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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
                            <tr class="monday-table-row">
                                <td class="monday-table-cell text-center" colspan="5">No aborted tasks found.</td>
                            </tr>
                            @endforelse
                            @if(false)
                            <tr class="monday-table-row" data-task-id="200">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text text-decoration-line-through text-muted">zubair</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #d946ef;">AM</div>
                                        <div class="monday-avatar" style="background-color: #00c875;">SC</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: #e2445c20; color: #e2445c;">Aborted</div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">Nov 30</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn delete" onclick="deleteTask(200)" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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
                            @endif
                        </tbody>
                    </table>
                    <div class="monday-pagination">
                        <a class="monday-pagination-btn {{ $abortedTasks->onFirstPage() ? 'disabled' : '' }}" href="{{ $abortedTasks->previousPageUrl() ?? '#' }}" @if($abortedTasks->onFirstPage()) aria-disabled="true" tabindex="-1" @endif>Previous</a>
                        <span class="monday-pagination-info">Page {{ $abortedTasks->currentPage() }}</span>
                        <a class="monday-pagination-btn {{ $abortedTasks->hasMorePages() ? '' : 'disabled' }}" href="{{ $abortedTasks->nextPageUrl() ?? '#' }}" @unless($abortedTasks->hasMorePages()) aria-disabled="true" tabindex="-1" @endunless>Next</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentEditingElement = null;

        function showTaskToast(message, heading = 'Tasks', icon = 'info') {
            if (window.jQuery && typeof jQuery.toast === 'function') {
                jQuery.toast({
                    heading: heading,
                    text: message,
                    icon: icon,
                    position: 'top-right',
                    showHideTransition: 'fade',
                    hideAfter: 2500,
                    loaderBg: '#0073ea'
                });
                return;
            }

            let container = document.querySelector('.task-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'task-toast-container';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = 'task-toast';
            toast.innerHTML = '<div class="task-toast-title"></div><div class="task-toast-message"></div>';
            toast.querySelector('.task-toast-title').textContent = heading;
            toast.querySelector('.task-toast-message').textContent = message;
            container.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('is-visible'));

            setTimeout(() => {
                toast.classList.remove('is-visible');
                setTimeout(() => toast.remove(), 220);
            }, 2500);
        }

        function showAddPersonToast(event) {
            event.stopPropagation();
            showTaskToast('Add person is not available yet.', 'People', 'info');
        }

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

        function editInline(element) {
            event.stopPropagation();
            event.preventDefault();

            if (currentEditingElement) {
                saveInlineEdit(currentEditingElement);
            }

            const field = element.dataset.field;
            const taskId = element.dataset.taskId;
            const currentValue = element.textContent.trim();

            currentEditingElement = element;

            const input = document.createElement('input');
            input.type = field === 'story_points' ? 'number' : 'text';
            input.className = 'monday-editable-input';
            input.value = currentValue;

            element.innerHTML = '';
            element.appendChild(input);
            input.focus();
            input.select();

            input.addEventListener('blur', () => saveInlineEdit(element));
            input.addEventListener('keydown', (e) => {
                e.stopPropagation();
                if (e.key === 'Enter') {
                    saveInlineEdit(element);
                } else if (e.key === 'Escape') {
                    cancelInlineEdit(element, currentValue);
                }
            });
        }

        function saveInlineEdit(element) {
            const input = element.querySelector('input');
            if (!input) return;

            const newValue = input.value.trim();
            element.textContent = newValue || '-';
            currentEditingElement = null;
        }

        function cancelInlineEdit(element, originalValue) {
            element.textContent = originalValue;
            currentEditingElement = null;
        }

        function togglePriority(taskId, event) {
            event.stopPropagation();
            const icon = event.target.closest('svg');
            const isFilled = icon.getAttribute('fill') === 'currentColor';

            if (isFilled) {
                icon.setAttribute('fill', 'none');
                icon.setAttribute('stroke', 'currentColor');
                icon.setAttribute('stroke-width', '2');
                icon.style.opacity = '0.3';
            } else {
                icon.setAttribute('fill', 'currentColor');
                icon.removeAttribute('stroke');
                icon.removeAttribute('stroke-width');
                icon.style.opacity = '1';
            }
        }

        function openStatusPicker(taskId, element) {
            event.stopPropagation();

            document.querySelectorAll('.monday-dropdown-menu').forEach(menu => menu.remove());

            const statuses = [
                { name: 'Pending', color: '#b45309', bg: '#ffedd5', hoverBg: '#fed7aa' },
                { name: 'In Progress', color: '#075985', bg: '#dbeafe', hoverBg: '#bfdbfe' },
                { name: 'Completed', color: '#047857', bg: '#d1fae5', hoverBg: '#a7f3d0' },
                { name: 'Aborted', color: '#be123c', bg: '#ffe4e6', hoverBg: '#fecdd3' }
            ];

            const dropdown = document.createElement('div');
            dropdown.className = 'monday-dropdown-menu';

            const rect = element.getBoundingClientRect();
            dropdown.style.top = (rect.bottom + 5) + 'px';
            dropdown.style.left = rect.left + 'px';

            statuses.forEach(status => {
                const item = document.createElement('div');
                item.className = 'monday-dropdown-item';
                item.style.backgroundColor = status.bg;
                item.style.color = status.color;
                item.textContent = status.name;

                item.onmouseover = () => {
                    item.style.backgroundColor = status.hoverBg;
                };
                item.onmouseout = () => {
                    item.style.backgroundColor = status.bg;
                };

                item.onclick = (e) => {
                    e.stopPropagation();
                    element.querySelector('span').textContent = status.name;
                    element.style.backgroundColor = status.bg;
                    element.style.color = status.color;
                    dropdown.remove();
                };
                dropdown.appendChild(item);
            });

            document.body.appendChild(dropdown);

            setTimeout(() => {
                document.addEventListener('click', function closeDropdown(e) {
                    if (!dropdown.contains(e.target) && e.target !== element) {
                        dropdown.remove();
                        document.removeEventListener('click', closeDropdown);
                    }
                });
            }, 10);
        }

        function editTask(taskId) {
            window.location.href = '/task/' + taskId + '/edit';
        }

        function deleteTask(taskId) {
            var confirmDelete = typeof window.appConfirm === 'function'
                ? window.appConfirm('Are you sure you want to delete this task?', {
                    title: 'Confirm delete',
                    confirmText: 'Delete',
                    cancelText: 'Cancel'
                })
                : Promise.resolve(true);

            confirmDelete.then(function(confirmed) {
                if (!confirmed) {
                    return;
                }

                fetch(`/task/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Error deleting task');
                    }

                    return response.json();
                })
                .then(function() {
                    const row = document.querySelector(`tr[data-task-id="${taskId}"]`);
                    const group = row ? row.closest('.monday-group') : null;

                    if (row) {
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        setTimeout(() => {
                            row.remove();
                            const groupHeader = group ? group.querySelector('.monday-group-header') : null;
                            if (groupHeader) {
                                const countSpan = groupHeader.querySelector('.monday-group-count');
                                if (countSpan) {
                                    let currentCount = parseInt(countSpan.textContent) || 0;
                                    if (currentCount > 0) {
                                         countSpan.textContent = currentCount - 1;
                                    }
                                }
                            }
                        }, 300);
                    }

                    if (typeof window.appToast === 'function') {
                        window.appToast('Task deleted successfully', 'success', 'Success');
                    }
                })
                .catch(function(error) {
                    console.error('Error deleting task:', error);
                    if (typeof window.appToast === 'function') {
                        window.appToast('Error deleting task', 'error', 'Error');
                    }
                });
            });
        }

        function newTask() {
            window.location.href = '/task/create';
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
    </script>
</div>




