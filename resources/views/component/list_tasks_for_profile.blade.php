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
                    {{-- ================================== --}}
                    {{-- == START OF CLICKABLE ROW FIX == --}}
                    {{-- ================================== --}}
                    {{-- This makes the row clickable but ignores the action cell --}}
                    <tr style="{{ $task->priority ? 'background: #ffbbb2;' : '' }}" 
                        data-href="{{ route('task.show', ['id' => $task->id]) }}" 
                        class="clickable-row-task">
                    {{-- ================================== --}}
                    {{-- == END OF CLICKABLE ROW FIX == --}}
                    {{-- ================================== --}}

                        <td>{{ $task->id }}</td>
                        <td data-delete-label>{{ $task->content }}</td>
                        <td>{{ $task->dead_line }}</td>
                        <td class="click_tour_in_task">{!! $task->tour_link !!}</td>
                        <td class="status">{!! $task->status_display !!}</td>
                        <td class="priority">{{ $task->priority_text }}</td>
                        
                        {{-- ================================== --}}
                        {{-- == START OF ACTION BUTTONS FIX == --}}
                        {{-- ================================== --}}
                        {{-- This stops the row-click from breaking the buttons --}}
                        <td onclick="event.stopPropagation();">
                            {{-- This includes the modern buttons from your main task page --}}
                            @include('component.action_buttons', [
                                'item' => $task,
                                'routePrefix' => 'task'
                            ])
                        </td>
                        {{-- ================================== --}}
                        {{-- == END OF ACTION BUTTONS FIX == --}}
                        {{-- ================================== --}}
                        
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

{{-- ================================== --}}
{{-- == START OF MODAL FIX == --}}
{{-- You must include the delete modal on this page --}}
{{-- ================================== --}}
@include('scaffold-interface.dashboard.components.delete-modal')
{{-- ================================== --}}
{{-- == END OF MODAL FIX == --}}
{{-- ================================== --}}


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // This script makes your rows clickable without breaking the buttons
    document.querySelectorAll('.clickable-row-task').forEach(function(row) {
        row.addEventListener('click', function(e) {
            // Check if the click was on a button, link, or inside the action cell
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.action-cell')) {
                return; // Do nothing, let the button work
            }
            
            // Otherwise, navigate to the "show" page
            const href = this.dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
    });
});
</script>
@endpush