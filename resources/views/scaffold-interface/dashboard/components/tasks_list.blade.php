<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - TMS</title>
    <style>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .wrapper {
            max-width: 1400px;
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
            width: 100%;
            border-collapse: collapse;
        }

        .monday-table-header {
            background: var(--monday-background);
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
            transition: all 0.2s ease;
            min-height: 32px;
            display: flex;
            align-items: center;
        }

        .monday-editable:hover {
            background: var(--monday-background-hover);
            outline: 2px solid rgba(0, 115, 234, 0.3);
            box-shadow: 0 0 0 3px rgba(0, 115, 234, 0.1);
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
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 100px;
            gap: 6px;
        }

        .monday-status-clickable {
            min-width: 120px;
            font-weight: 600;
            user-select: none;
        }

        .monday-status-clickable:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            filter: brightness(1.05);
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
            gap: 6px;
            color: var(--monday-text-secondary);
            font-size: 13px;
            cursor: pointer;
            padding: 4px 8px;
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
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .monday-table-row:hover .monday-actions {
            opacity: 1;
        }

        .monday-action-btn {
            padding: 6px;
            border-radius: 6px;
            background: white;
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .monday-action-btn:hover {
            background: white;
        }

        .monday-action-btn svg {
            width: 18px;
            height: 18px;
        }

        .monday-action-btn.edit {
            color: #f59e0b;
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .monday-action-btn.edit:hover {
            color: #d97706;
            border-color: #fb923c;
        }

        .monday-action-btn.delete {
            color: var(--monday-danger);
            border-color: #fecaca;
            background: #fef2f2;
        }

        .monday-action-btn.delete:hover {
            color: #b91c1c;
            border-color: #ef4444;
        }

        .monday-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
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
            transition: all 0.2s ease;
        }

        .monday-dropdown-item:hover {
            transform: translateX(4px);
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
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Tasks</h1>
        </div>

        <div class="monday-board">
            <div class="monday-toolbar">
                <button class="monday-btn monday-btn-primary" onclick="newTask()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="width: 18px; height: 18px;">
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
                    <span class="monday-group-count">56</span>
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
                            <tr class="monday-table-row" data-task-id="1">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <svg class="monday-task-priority-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" onclick="togglePriority(1, event)">
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        <span class="monday-task-text monday-editable" data-field="content" data-task-id="1" onclick="editInline(this)">check this tour now</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #a3e635;">AM</div>
                                        <div class="monday-avatar monday-avatar-add" onclick="alert('Add person')">+</div>
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
                                    <div class="monday-date overdue" onclick="alert('Change date')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        <span>Aug 17, 20:00</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-badge" style="background-color: #e5e7eb; color: #6b7280;">Normal</span>
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
                                        <span class="monday-task-text monday-editable" data-field="content" data-task-id="2" onclick="editInline(this)">Test</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #a3e635;">AM</div>
                                        <div class="monday-avatar" style="background-color: #1f2937;">YI</div>
                                        <div class="monday-avatar monday-avatar-add" onclick="alert('Add person')">+</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status monday-status-clickable" style="background-color: #fdab3d20; color: #fdab3d;" onclick="openStatusPicker(2, this)">
                                        <span>Pending</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-date overdue" onclick="alert('Change date')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        <span>Sep 25, 20:00</span>
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
                            <tr class="monday-table-row" data-task-id="3">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <svg class="monday-task-priority-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="opacity: 0.3;" onclick="togglePriority(3, event)">
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        <span class="monday-task-text monday-editable" data-field="content" data-task-id="3" onclick="editInline(this)">Buy Eggs</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #1f2937;">YI</div>
                                        <div class="monday-avatar monday-avatar-add" onclick="alert('Add person')">+</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status monday-status-clickable" style="background-color: #fdab3d20; color: #fdab3d;" onclick="openStatusPicker(3, this)">
                                        <span>Pending</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-date overdue" onclick="alert('Change date')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        <span>Sep 25, 20:00</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-badge" style="background-color: #e5e7eb; color: #6b7280;">Normal</span>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">-</span>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-sp monday-editable" data-field="story_points" data-task-id="3" onclick="editInline(this)">0</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask(3)" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button class="monday-action-btn delete" onclick="deleteTask(3)" title="Delete">
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
                            <tr class="monday-table-row" data-task-id="4">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <svg class="monday-task-priority-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" onclick="togglePriority(4, event)">
                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                        </svg>
                                        <span class="monday-task-text monday-editable" data-field="content" data-task-id="4" onclick="editInline(this)">Book Restaurant in Nice</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #a3e635;">AM</div>
                                        <div class="monday-avatar" style="background-color: #d946ef;">AD</div>
                                        <div class="monday-avatar monday-avatar-add" onclick="alert('Add person')">+</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status monday-status-clickable" style="background-color: #fdab3d20; color: #fdab3d;" onclick="openStatusPicker(4, this)">
                                        <span>Pending</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-date overdue" onclick="alert('Change date')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <circle cx="12" cy="12" r="9" />
                                            <polyline points="12 7 12 12 15 15" />
                                        </svg>
                                        <span>Sep 28, 20:00</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-badge" style="background-color: #fef3c7; color: #d97706;">High</span>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">-</span>
                                </td>
                                <td class="monday-table-cell text-center">
                                    <span class="monday-sp monday-editable" data-field="story_points" data-task-id="4" onclick="editInline(this)">0</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn edit" onclick="editTask(4)" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button class="monday-action-btn delete" onclick="deleteTask(4)" title="Delete">
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
                        </tbody>
                    </table>
                    <div class="monday-pagination">
                        <button class="monday-pagination-btn" onclick="previousPage('todo')">Previous</button>
                        <span class="monday-pagination-info">Page 1 of 6</span>
                        <button class="monday-pagination-btn" onclick="nextPage('todo')">Next</button>
                    </div>
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
                    <span class="monday-group-count">21</span>
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
                            <tr class="monday-table-row completed" data-task-id="5">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text">testing check</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person"></div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: #00c87520; color: #00c875;">Completed</div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">Apr 05</span>
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
                                        <button class="monday-action-btn edit" onclick="editTask(5)" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="monday-table-row completed" data-task-id="6">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text">Meeting: TMS</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #a3e635;">AM</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: #00c87520; color: #00c875;">Completed</div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">Jun 20</span>
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
                                        <button class="monday-action-btn edit" onclick="editTask(6)" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="monday-pagination">
                        <button class="monday-pagination-btn" onclick="previousPage('completed')">Previous</button>
                        <span class="monday-pagination-info">Page 1 of 3</span>
                        <button class="monday-pagination-btn" onclick="nextPage('completed')">Next</button>
                    </div>
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
                    <span class="monday-group-count">8</span>
                </div>

                <div class="monday-group-content" id="group-aborted" style="display: none;">
                    <table class="monday-table">
                        <thead class="monday-table-header">
                            <tr>
                                <th style="width: 40%">TASK</th>
                                <th style="width: 150px">PERSON</th>
                                <th style="width: 120px">STATUS</th>
                                <th style="width: 130px">DEADLINE</th>
                                <th style="width: 80px" class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="monday-table-row" data-task-id="7">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text text-decoration-line-through text-muted">Hi-this-is-AMEER</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #d946ef;">AD</div>
                                        <div class="monday-avatar" style="background-color: #a3e635;">AM</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: #e2445c20; color: #e2445c;">Abort</div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">Apr 05</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn delete" onclick="deleteTask(7)" title="Delete">
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
                            <tr class="monday-table-row" data-task-id="8">
                                <td class="monday-table-cell-task">
                                    <div class="monday-task-content">
                                        <span class="monday-task-text text-decoration-line-through text-muted">Confirm reservation of Leonardo Hotel Frankfurt Airport</span>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-person">
                                        <div class="monday-avatar" style="background-color: #a3e635;">AM</div>
                                    </div>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-status" style="background-color: #e2445c20; color: #e2445c;">Abort</div>
                                </td>
                                <td class="monday-table-cell">
                                    <span class="text-muted">May 02</span>
                                </td>
                                <td class="monday-table-cell">
                                    <div class="monday-actions" style="display: flex !important; opacity: 1;">
                                        <button class="monday-action-btn delete" onclick="deleteTask(8)" title="Delete">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentEditingElement = null;

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
                { name: 'Pending', color: '#fdab3d' },
                { name: 'In Progress', color: '#0073ea' },
                { name: 'Completed', color: '#00c875' },
                { name: 'Aborted', color: '#e2445c' }
            ];

            const dropdown = document.createElement('div');
            dropdown.className = 'monday-dropdown-menu';

            const rect = element.getBoundingClientRect();
            dropdown.style.top = (rect.bottom + 5) + 'px';
            dropdown.style.left = rect.left + 'px';

            statuses.forEach(status => {
                const item = document.createElement('div');
                item.className = 'monday-dropdown-item';
                item.style.backgroundColor = status.color + '20';
                item.style.color = status.color;
                item.textContent = status.name;

                item.onmouseover = () => {
                    item.style.backgroundColor = status.color + '30';
                    item.style.transform = 'translateX(4px)';
                };
                item.onmouseout = () => {
                    item.style.backgroundColor = status.color + '20';
                    item.style.transform = 'translateX(0)';
                };

                item.onclick = (e) => {
                    e.stopPropagation();
                    element.querySelector('span').textContent = status.name;
                    element.style.backgroundColor = status.color + '20';
                    element.style.color = status.color;
                    dropdown.remove();
                    // setTimeout(() => location.reload(), 500); // Removed this line to prevent reload on static page
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

       // ==========================================
        // == 📍 THIS IS THE FIX ==
        // ==========================================
        function editTask(taskId) {
            // OLD CODE:
            // alert('Redirecting to edit task ' + taskId);
            
            // NEW CODE:
            // This redirects to the correct route (e.g., /task/3/edit)
            window.location.href = '/task/' + taskId + '/edit';
        }
        // ==========================================
        // == END OF FIX ==
        //
        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                const row = document.querySelector(`tr[data-task-id="${taskId}"]`);
                if (row) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        row.remove();
                        // Update count after removal
                        const groupHeader = row.closest('.monday-group').querySelector('.monday-group-header');
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
            }
        }

        // ==========================================
        // == UPDATED NEW TASK FUNCTION ==
        // ==========================================
        function newTask() {
            // This now redirects to your 'create' page, 
            // which will show the full form and save permanently.
            window.location.href = '/task/create';
        }
        // ==========================================
        // == END OF UPDATED FUNCTION ==
        // ==========================================


        function previousPage(group) {
            alert('Previous page for ' + group);
        }

        function nextPage(group) {
            alert('Next page for ' + group);
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
</body>
</html>