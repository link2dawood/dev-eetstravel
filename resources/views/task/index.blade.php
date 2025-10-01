@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
    ['title' => 'Tasks', 'sub_title' => 'Tasks List',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Tour Tasks', 'icon' => 'tasks', 'route' => null]]])
    <select hidden id="task-status-search" class="select2">
        @foreach($statuses as $status)
        <option value="{{is_object($status) ? $status->id : $status}}">{{is_object($status) ? $status->name : $status}}</option>
        @endforeach
    </select>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('task.create'), \App\Task::class) !!}
                </div>
                <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle " aria-hidden="true"></i>
                    @include('legend.task_legend')
                    </span>
                <br>
                <br>
				<div class="table-responsive">
                <table id="tasks-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff;width: 100%;'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'tasks-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'tasks-table')" style="max-width: 250px;">{!!trans('main.Content')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'tasks-table')">{!!trans('main.Deadline')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'tasks-table')">{!!trans('main.Tour')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'tasks-table')">{!!trans('main.Assignto')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'tasks-table')">{!!trans('main.TaskType')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'tasks-table')">{!!trans('main.Status')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(7, 'tasks-table')">{!!trans('main.Priority')!!} <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width:150px; text-align: center;">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td style="max-width: 250px;">{{ $task->content }}</td>
                            <td>{{ $task->deadline ? (\Carbon\Carbon::parse($task->deadline)->format('Y-m-d H:i')) : '' }}</td>
                            <td>{{ $task->tour && is_object($task->tour) ? $task->tour->name : '' }}</td>
                            <td>
                                @if($task->assignedTo && is_object($task->assignedTo))
                                    {{ $task->assignedTo->name }}
                                @endif
                            </td>
                            <td>{{ $task->task_type ? \App\Task::$taskTypes[$task->task_type] ?? $task->task_type : '' }}</td>
                            <td>
                                <span class="badge badge-primary" style="background-color: {{ $task->getStatusColor() }}">
                                    {{ $task->getStatusName() }}
                                </span>
                            </td>
                            <td>
                                @if($task->priority)
                                    <span class="badge badge-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @include('component.action_buttons', [
                                    'show_route' => route('task.show', ['task' => $task->id]),
                                    'edit_route' => route('task.edit', ['task' => $task->id]),
                                    'delete_route' => route('task.destroy', $task->id),
                                    'model' => $task
                                ])
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No tasks found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12">
                        {{ $tasks->links() }}
                    </div>
                </div>
				</div>
                <span id="task_types" data-info="{{ $task_types }}"></span>
                <span id="task_statuses" data-info="{{ json_encode($statuses) }}"></span>
                <span id="status_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('task.edit') ? 'status' : '' }}"></span>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    $(document).ready(function() {
        if($('#task-status-search').length >0){

            // $('#task-status-search').select2('destroy');
        }
        let classNameStatus = $('#status_permission').attr('data-info');
        // Initialize Bootstrap Table
        initializeBootstrapTable('tasks-table');
    });


</script>
@endpush