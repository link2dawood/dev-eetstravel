<div class="panel">
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{!!trans('main.Content')!!}</th>
                    <th>{!!trans('main.Deadline')!!}</th>
                    <th>{!!trans('main.Tour')!!}</th>
                    <th>{!!trans('main.Status')!!}</th>
                    <th>{!!trans('main.Priority')!!}</th>
                    <th style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr style="{{ $task->priority ? 'background: #ffbbb2;' : '' }}">
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->content }}</td>
                        <td>{{ $task->dead_line }}</td>
                        <td class="click_tour_in_task">{!! $task->tour_link !!}</td>
                        <td class="status">{!! $task->status_display !!}</td>
                        <td class="priority">{{ $task->priority_text }}</td>
                        <td>{!! $task->action_buttons !!}</td>
                    </tr>
                @endforeach
                @if(empty($tasks))
                    <tr>
                        <td colspan="7" class="text-center">No tasks found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

