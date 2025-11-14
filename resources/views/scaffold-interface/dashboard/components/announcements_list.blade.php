<div class="box box-primary">
    @if(Auth::user()->can('dashboard.announcements'))
    <div class="box-header">
        <h4>Announcements</h4>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        
        <table id="announcement-table" class="table table-striped table-hover" style='background:#fff'>
            <thead>
            <th>ID</th>
            <th>{{ trans('main.Title') }}</th>
            <th>{{ trans('main.Content') }}</th>
            <th>{{ trans('main.Date') }}</th>
            <th>{{ trans('main.Sender') }}</th>
            <th style="width: 140px">{{ trans('main.Actions') }}</th>
            </thead>
            <tbody>
            @foreach($announcements as $announcement)
                <tr data-href="{{ route('announcements.show', ['announcement' => $announcement->id]) }}">
                    
                    <td class="clickable-cell">{{ $announcement->id }}</td>
                    <td class="clickable-cell"><span style="font-size: 14px;font-weight: bold;">{{ $announcement->title }}</span></td>
                    <td class="clickable-cell">{{ \Illuminate\Support\Str::limit($announcement->content, 50) }}</td>
                    <td class="clickable-cell">{{ $announcement->created_at->format('Y-m-d') }}</td>
                    <td class="clickable-cell">{{ $announcement->sender }}</td>
                    
                    <td>
                        {!! $announcement->action_buttons !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="box-footer clearfix">
            @if(Auth::user()->can('announcements.create'))
            <a href="{{route('announcements.create')}}" class="btn btn-primary">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New Announcement
            </a>
            @endif
            @if(Auth::user()->can('announcements.index'))
            <a href="{{route('announcements.index')}}" class="btn btn-outline-secondary float-end">
                View All Announcements
            </a>
            @endif
        </div>
    </div>
    @else
        <div class="box-header">
            <h4>{{ trans('main.Announcements') }}</h4>
        </div>
        <div class="box-body">
            {{ trans('main.Youdonthavepermissions') }}
        </div>
    @endif
</div>

{{-- ================================== --}}
{{-- == START OF JAVASCRIPT FIX == --}}
{{-- ================================== --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handler for clickable cells
    document.querySelectorAll('#announcement-table .clickable-cell').forEach(function(cell) {
        cell.addEventListener('click', function(e) {
            const href = this.closest('tr').dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
    });

    // Handler for the action cell (to stop row click)
    document.querySelectorAll('#announcement-table td:not(.clickable-cell)').forEach(function(cell) {
        cell.addEventListener('click', function(e) {
            // This stops the click from "bubbling up" to the <tr>
            // and triggering the clickable row.
            e.stopPropagation();
        });
    });
});
</script>
<style>
    #announcement-table .clickable-cell {
        cursor: pointer;
    }
    #announcement-table tr:hover .clickable-cell {
        background-color: #f8f9fa; 
    }
</style>
@endpush
{{-- ================================== --}}
{{-- == END OF JAVASCRIPT FIX == --}}
{{-- ================================== --}}