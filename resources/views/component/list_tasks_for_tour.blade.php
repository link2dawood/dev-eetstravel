<div class="panel panel-info">
    <div class="panel-heading">{!!trans('main.Tasks')!!}</div>
    <a data-info="{{ $listIdTasks }}" id="getListIdTasks"></a>
    <a data-info="{{ $tour->name }}" id="getNameTour"></a>
    <div class="panel-body">
        <div class="row">
            @if(Auth::user()->can('create.task'))
            <div class="col-md-12">
                <a href="{!!url("task")!!}/create?tour={!!$tour->id!!}" class='btn btn-success'>{!!trans('main.AddTask')!!}</a>
            </div>
            @endif
        </div>
        <br>
        <table class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
            <thead>
            <tr>
                <th>ID</th>
                <th>{!!trans('main.Content')!!}</th>
                <th>{!!trans('main.Deadline')!!}</th>
                <th>{!!trans('main.Assignto')!!}</th>
                <th>{!!trans('main.Status')!!}</th>
                <th>{!!trans('main.Priority')!!}</th>
                <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
            </tr>
            </thead>
            <tbody>
                @forelse($tasksData ?? [] as $task)
                <tr class="{{ $task['priority_value'] ? 'priority-high' : '' }}" style="{{ $task['priority_value'] ? 'background: #ffbbb2;' : '' }}">
                    <td>{{ $task['id'] }}</td>
                    <td>{{ $task['content'] ?? '' }}</td>
                    <td>{{ $task['dead_line'] ?? '' }}</td>
                    <td>{!! $task['assign'] ?? '' !!}</td>
                    <td>
                        <select name="status" class="task-status form-control" data-update-link="{{ route('task.update', ['task' => $task['id']]) }}" style="border:none; outline:0px; background-color:inherit; -moz-appearance: none; -webkit-appearance: none; width: 100%;">
                            @foreach($task['task_statuses'] as $status)
                            <option value="{{ $status->id }}" {{ $status->id == $task['status_id'] ? 'selected="selected"' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="priority" style="display: none;">{{ $task['priority'] }}</td>
                    <td>{!! $task['action_buttons'] ?? '' !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No tasks found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


@push('scripts')
<script>
$(document).ready(function() {
    // Handle task status updates via AJAX (keeping existing functionality)
    $('body').on('change', 'select.task-status', function() {
        var select = $(this);
        var updateUrl = select.data('update-link');
        var newStatus = select.val();

        $.ajax({
            url: updateUrl,
            method: 'PUT',
            data: {
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Optional: Show success message
                console.log('Task status updated successfully');
            },
            error: function(xhr) {
                // Optional: Show error message and revert selection
                console.error('Error updating task status');
                // Revert to previous selection
                select.prop('selectedIndex', 0);
            }
        });
    });
});
</script>
@endpush