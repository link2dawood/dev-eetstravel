{{--Tour Tasks table--}}
<div class="box box-primary">
    @if(Auth::user()->can('dashboard.tasks'))
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
                    <th style="width: 300px">{{ trans('main.Content') }}</th>
                    <th>{{ trans('main.Deadline') }}</th>
                    <th>{{ trans('main.Tour') }}</th>
                    <th>{{ trans('main.Assignto') }}</th>
                    <th>{{ trans('main.TaskType') }}</th>
                    <th>{{ trans('main.Status') }}</th>
                    <th style="width: 140px">{{ trans('main.Actions') }}</th>
                    </thead>
                    <tbody>
                    @foreach($tasks as $task)
                    <tr class="clickable-row {{ $task->priority ? 'task-priority' : '' }}" onclick="window.location.href='{{ route('task.show', ['task' => $task->id]) }}'">
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->content }}</td>
                        <td>{{ $task->dead_line }}</td>
                        <td class="click_tour_in_task">
                            @if($task->tour_link_show)
                            <span data-tour-link="{{ $task->tour_link_show }}" style="color: blue; text-decoration: underline; cursor: pointer"
                                  onclick="event.stopPropagation(); window.location.href='{{ $task->tour_link_show }}'">
                                {{ $task->tour_name }}
                            </span>
                            @endif
                        </td>
                        <td>{{ $task->show_assigned_users }}</td>
                        <td>{{ $task->task_type }}</td>
                        <td class="status" onclick="event.stopPropagation();">
                            <select name="status" class="task-status form-control"
                                    data-update-link="{{ $task->data_update_link }}">
                                @foreach($taskStatuses as $status)
                                <option value="{{ $status->id }}" {{ $task->status == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td onclick="event.stopPropagation();">
                            <div class="btn-list flex-nowrap">
                                @php
                                    $actionHtml = $task->action_buttons;
                                    preg_match_all('/href=["\']([^"\']+)["\']/', $actionHtml, $links);
                                    $editLink = $links[1][0] ?? null;
                                    $deleteLink = $links[1][1] ?? null;
                                @endphp
                                
                                @if($editLink)
                                    <a href="{{ $editLink }}" class="btn btn-icon btn-ghost-warning" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>
                                @endif
                                
                                @if($deleteLink)
                                    <button type="button" class="btn btn-icon btn-ghost-danger delete-btn"
                                            data-url="{{ $deleteLink }}"
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
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                @if(Auth::user()->can('task.create'))
                    <a href="#" data-target="#modalCreate1" data-toggle="modal"
                       class="popupCreate btn btn-success">
                        <i class="fa fa-plus fa-md" aria-hidden="true"></i> {{ trans('main.NewTask') }}
                    </a>
                @endif
                @if(Auth::user()->can('task.index'))
                    <a href="{{route('task.index')}}" class="btn btn-outline-secondary float-end">
                        {{ trans('main.ViewAllTasks') }}
                    </a>
                @endif
            </div>
        @else
            <div class="box-header">
                <h4>{{ trans('main.Tasks') }}</h4>
            </div>
            <div class="box-body">
                {{ trans('main.Youdonthavepermissions') }}
            </div>
        @endif
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
{{--end Tour Tasks table--}}