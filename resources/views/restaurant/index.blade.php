@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Restaurants')

@section('content')
<style>.sample-row td{background:#f8fafc;color:#475569}.cursor-pointer{cursor:pointer}</style>
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Service Management</div>
                <h2 class="page-title"><i class="ti ti-tools-kitchen-2 me-2"></i>Restaurants</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                {!! \App\Helper\PermissionHelper::getCreateButton(route('restaurant.create'), \App\Restaurant::class, 'btn btn-primary') !!}
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
        <div class="card-header"><h3 class="card-title">Restaurants List</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="restaurants-search" class="form-control" placeholder="Search restaurants..." onkeyup="filterTable('restaurants-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('restaurants-table', 'restaurants_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="restaurants-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" class="cursor-pointer" onclick="sortTable(0, 'restaurants-table')">ID <i class="ti ti-arrows-sort"></i></th>
                            <th class="cursor-pointer" onclick="sortTable(1, 'restaurants-table')">{!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell cursor-pointer" onclick="sortTable(2, 'restaurants-table')">{!!trans('main.Address')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell cursor-pointer" onclick="sortTable(3, 'restaurants-table')">{!!trans('main.Country')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell cursor-pointer" onclick="sortTable(4, 'restaurants-table')">{!!trans('main.City')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell cursor-pointer" onclick="sortTable(5, 'restaurants-table')">{!!trans('main.WorkPhone')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-xl-table-cell cursor-pointer" onclick="sortTable(6, 'restaurants-table')">{!!trans('main.ContactEmail')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($restaurants as $restaurant)
                        <tr>
                            <td><span class="text-muted">#{{ $restaurant->id }}</span></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $restaurant->name }}</span>
                                    <small class="text-muted d-lg-none">{{ $restaurant->city_name ?? '' }}</small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $restaurant->address ?? '-' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $restaurant->country_name ?? '-' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $restaurant->city_name ?? '-' }}</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">{{ $restaurant->work_phone ?? '-' }}</span></td>
                            <td class="d-none d-xl-table-cell"><span class="text-muted">{{ $restaurant->contact_email ?? '-' }}</span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $restaurant, 'routePrefix' => 'restaurant'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="sample-row">
                            <td><span class="badge bg-secondary me-1">Sample</span>#S-101</td>
                            <td><div class="d-flex flex-column"><span class="fw-bold">Sample Bistro</span><small class="text-muted d-lg-none">Rome</small></div></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">Via Roma 10</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">Italy</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">Rome</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">+39 000 000</span></td>
                            <td class="d-none d-xl-table-cell"><span class="text-muted">Chef Sample</span></td>
                            <td class="text-end text-muted">Sample only</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($restaurants->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing {{ $restaurants->firstItem() }} to {{ $restaurants->lastItem() }} of {{ $restaurants->total() }} entries</div>
                <div>{{ $restaurants->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
<span id="service-name" hidden data-service-name='Restaurant'></span>
@endsection

@push('scripts')
<script>
    function filterTable(tableId, searchValue) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        const filter = (searchValue || '').toLowerCase();
        rows.forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(filter) - '' : 'none';
        });
    }

    function sortTable(columnIndex, tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const direction = table.dataset.sortColumn == columnIndex && table.dataset.sortDirection === 'asc' - 'desc' : 'asc';
        table.dataset.sortColumn = columnIndex;
        table.dataset.sortDirection = direction;
        rows.sort(function(a, b) {
            const left = (a.children[columnIndex]-.innerText || '').trim().toLowerCase();
            const right = (b.children[columnIndex]-.innerText || '').trim().toLowerCase();
            return direction === 'asc' - left.localeCompare(right) : right.localeCompare(left);
        });
        rows.forEach(function(row) { tbody.appendChild(row); });
    }

    function exportTableToCSV(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const rows = Array.from(table.querySelectorAll('tr')).filter(function(row) { return row.style.display !== 'none'; });
        const csv = rows.map(function(row) {
            return Array.from(row.querySelectorAll('th,td')).slice(0, -1).map(function(cell) {
                return '"' + cell.innerText.replace(/"/g, '""').trim() + '"';
            }).join(',');
        }).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(function() { URL.revokeObjectURL(link.href); }, 100);
    }
</script>
@endpush
