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
        <br>
        {{-- Added ID to the table --}}
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
                {{-- *** FIX: No more onclick="..." on the <tr> *** --}}
                <tr data-href="{{ route('announcements.show', ['announcement' => $announcement->id]) }}">
                    
                    {{-- Added 'clickable-cell' class to all cells EXCEPT the action cell --}}
                    <td class="clickable-cell">{{ $announcement->id }}</td>
                    <td class="clickable-cell"><span style="font-size: 14px;font-weight: bold;">{{ $announcement->title }}</span></td>
                    <td class="clickable-cell">{{ \Illuminate\Support\Str::limit($announcement->content, 50) }}</td>
                    <td class="clickable-cell">{{ $announcement->created_at->format('Y-m-d') }}</td>
                    <td class="clickable-cell">{{ $announcement->sender }}</td>
                    
                    {{-- This cell with buttons is NOT clickable --}}
                    <td>
                        <div class="btn-list flex-nowrap">
                            
                            {{-- This link will now work correctly --}}
                            <a href="{{ route('announcements.edit', ['announcement' => $announcement->id]) }}" 
                               class="btn btn-icon btn-ghost-warning" 
                               title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                            </a>

                            <button type="button" class="btn btn-icon btn-ghost-danger delete-btn"
                                    data-url="{{ route('announcements.destroy', ['announcement' => $announcement->id]) }}"
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
                        </div>
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

{{-- This script block must be loaded AFTER jQuery --}}
<!-- IMPORTANT: Replace the entire <script> and <style> sections at the bottom of your announcement list Blade file with this code -->

<script>
// Ensure jQuery is loaded before running this
$(document).ready(function() {
    // Click Handler for Clickable Cells - ONLY cells with clickable-cell class
    $('#announcement-table').on('click', '.clickable-cell', function(e) {
        // Don't navigate if clicking on a link or button
        if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('button').length) {
            return;
        }
        
        var href = $(this).closest('tr').data('href');
        if (href) {
            window.location.href = href;
        }
    });

    // Delete function - prevent event bubbling
    window.confirmDelete = function(event, button) {
        event.preventDefault();
        event.stopPropagation();
        
        if (confirm('Are you sure you want to delete this announcement?')) {
            const url = button.getAttribute('data-url');

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.closest('tr').remove();
                } else {
                    alert(data.message); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unknown error occurred.');
            });
        }
    }

    // Handle edit button click - ensure it navigates correctly
    $('#announcement-table').on('click', 'a.btn-ghost-warning', function(e) {
        // Allow the link to work normally
        e.stopPropagation();
    });

    // Handle delete button click - ensure it doesn't trigger row navigation
    $('#announcement-table').on('click', 'button.delete-btn', function(e) {
        e.stopPropagation();
    });
});
</script>

<style>
/* This CSS makes the clickable cells show a pointer cursor */
#announcement-table .clickable-cell {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

#announcement-table .clickable-cell:hover {
    background-color: #f8f9fa !important;
}

.table tbody td {
    vertical-align: middle;
}

/* Ensure action buttons are clearly clickable */
#announcement-table .btn {
    cursor: pointer !important;
}
</style>