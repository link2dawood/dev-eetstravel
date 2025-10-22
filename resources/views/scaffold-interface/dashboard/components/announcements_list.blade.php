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
        <table class="table table-striped table-hover clickable-rows" style='background:#fff'>
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
                <tr class="clickable-row" onclick="window.location.href='{{ route('announcements.show', ['announcement' => $announcement->id]) }}'">
                    <td>{{ $announcement->id }}</td>
                    <td><span style="font-size: 14px;font-weight: bold;">{{ $announcement->title }}</span></td>
                    <td>{{ $announcement->content }}</td>
                    <td>{{ $announcement->created_at }}</td>
                    <td>{{ $announcement->sender }}</td>
                    <td onclick="event.stopPropagation();">
                        <div class="btn-list flex-nowrap">
                            <!-- EDIT BUTTON -->
                            <a href="{{ route('announcements.edit', ['announcement' => $announcement->id]) }}" class="btn btn-icon btn-ghost-warning" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                            </a>

                            <!-- DELETE BUTTON -->
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

<script>
function confirmDelete(event, button) {
    event.preventDefault();
    if (confirm('Are you sure you want to delete this announcement?')) {
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

.table tbody td {
    vertical-align: middle;
}
</style>