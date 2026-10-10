@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Events')

@section('content')
<style>.sample-row td{background:#f8fafc;color:#475569}.cursor-pointer{cursor:pointer}</style>
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Service Management</div>
                <h2 class="page-title"><i class="ti ti-calendar-event me-2"></i>Events</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                {!! \App\Helper\PermissionHelper::getCreateButton(route('event.create'), \App\Event::class, 'btn btn-primary') !!}
            </div>
        </div>
    </div>

    @if (Session::has('message'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex"><div><i class="ti ti-alert-circle me-2"></i></div><div class="flex-fill">{{ Session::get('message') }}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    @endif
    @if(session('export_all'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex"><div><i class="ti ti-info-circle me-2"></i></div><div class="flex-fill">{{ session('export_all') }}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="card-title">Events List</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="events-search" class="form-control" placeholder="Search events..." onkeyup="filterTable('events-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('events-table', 'events_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="events-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" class="cursor-pointer" onclick="sortTable(0, 'events-table')">ID <i class="ti ti-arrows-sort"></i></th>
                            <th class="cursor-pointer" onclick="sortTable(1, 'events-table')">{!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell cursor-pointer" onclick="sortTable(2, 'events-table')">{!!trans('main.Address')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell cursor-pointer" onclick="sortTable(3, 'events-table')">{!!trans('main.Country')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell cursor-pointer" onclick="sortTable(4, 'events-table')">{!!trans('main.City')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell cursor-pointer" onclick="sortTable(5, 'events-table')">{!!trans('main.WorkPhone')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-xl-table-cell cursor-pointer" onclick="sortTable(6, 'events-table')">{!!trans('main.ContactEmail')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td><span class="text-muted">#{{ $event->id }}</span></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $event->name }}</span>
                                    <small class="text-muted d-lg-none">{{ $event->city_name ?? '' }}</small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $event->address ?? '-' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $event->country_name ?? '-' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $event->city_name ?? '-' }}</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">{{ $event->work_phone ?? '-' }}</span></td>
                            <td class="d-none d-xl-table-cell"><span class="text-muted">{{ $event->contact_email ?? '-' }}</span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $event, 'routePrefix' => 'event'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="sample-row">
                            <td><span class="badge bg-secondary me-1">Sample</span>#S-101</td>
                            <td><div class="d-flex flex-column"><span class="fw-bold">Sample Conference</span><small class="text-muted d-lg-none">Rome</small></div></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">Via Roma 10</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">Italy</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">Rome</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">+39 000 000</span></td>
                            <td class="d-none d-xl-table-cell"><span class="text-muted">events@example.com</span></td>
                            <td class="text-end text-muted">Sample only</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($events->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} entries</div>
                <div>{{ $events->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
<span id="service-name" hidden data-service-name='Event'></span>
@endsection

@push('scripts')
<script>
    function filterTable(tableId, searchValue) {
        const table = document.getElementById(tableId);
        const tr = table.getElementsByTagName('tr');
        const filter = searchValue.toUpperCase();
        
        for (let i = 1; i < tr.length; i++) {
            let txtValue = tr[i].textContent || tr[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
    
    function sortTable(n, tableId) {
        const table = document.getElementById(tableId);
        let switching = true;
        let dir = 'asc';
        let switchcount = 0;
        
        while (switching) {
            switching = false;
            const rows = table.rows;
            
            for (let i = 1; i < (rows.length - 1); i++) {
                let shouldSwitch = false;
                const x = rows[i].getElementsByTagName('TD')[n];
                const y = rows[i + 1].getElementsByTagName('TD')[n];
                
                if (dir == 'asc') {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == 'desc') {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount++;
            } else {
                if (switchcount == 0 && dir == 'asc') {
                    dir = 'desc';
                    switching = true;
                }
            }
        }
    }
    
    function exportTableToCSV(tableId, filename) {
        const table = document.getElementById(tableId);
        let csv = [];
        const rows = table.querySelectorAll('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length - 1; j++) { // Exclude last column (Actions)
                row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
            }
            
            csv.push(row.join(','));
        }
        
        const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
        const downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
@endpush