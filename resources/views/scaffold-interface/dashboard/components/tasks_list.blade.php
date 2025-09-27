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
                    <table id="tasks-table" class="table table-striped table-bordered table-hover"
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
                        <tr class="{{ $task->priority ? 'task-priority' : '' }}">
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->content }}</td>
                            <td>{{ $task->dead_line }}</td>
                            <td class="click_tour_in_task">
                                @if($task->tour_link_show)
                                <span data-tour-link="{{ $task->tour_link_show }}" style="color: blue; text-decoration: underline; cursor: pointer">
                                    {{ $task->tour_name }}
                                </span>
                                @endif
                            </td>
                            <td>{{ $task->show_assigned_users }}</td>
                            <td>{{ $task->task_type }}</td>
                            <td class="status">
                                <select name="status" class="task-status form-control"
                                        data-update-link="{{ $task->data_update_link }}">
                                    @foreach($taskStatuses as $status)
                                    <option value="{{ $status->id }}" {{ $task->status == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{!! $task->action_buttons !!}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="box-footer clearfix">
                        @if(Auth::user()->can('task.create'))
                            <a href="#" data-target="#modalCreate1" data-toggle="modal"
                               class="popupCreate btn btn-success">
                                <i class="fa fa-plus fa-md" aria-hidden="true"></i> {{ trans('main.NewTask') }}
                            </a>
                        @endif
                        @if(Auth::user()->can('task.index'))
                            <a href="{{route('task.index')}}">
                                <button href="javascript:void(0)"
                                        class="btn btn-default btn-flat pull-right">{{ trans('main.ViewAllTasks') }}
                                </button>
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
</div>
{{--edn Tour Tasks table--}}