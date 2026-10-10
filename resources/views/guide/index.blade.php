@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Guides')

@section('content')
<style>.sample-row td{background:#f8fafc;color:#475569}.cursor-pointer{cursor:pointer}</style>
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Service Management</div>
                <h2 class="page-title">
                    <i class="ti ti-user-check me-2"></i>Guides
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('guide.create'), \App\Guide::class, 'btn btn-primary') !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if (Session::has('message'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-circle me-2"></i></div>
                <div class="flex-fill">{{ Session::get('message') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('export_all'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-info-circle me-2"></i></div>
                <div class="flex-fill">{{ session('export_all') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Guides List</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="guides-search" class="form-control" placeholder="Search guides..." onkeyup="filterTable('guides-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('guides-table', 'guides_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="guides-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" class="cursor-pointer" onclick="sortTable(0, 'guides-table')">ID <i class="ti ti-arrows-sort"></i></th>
                            <th class="cursor-pointer" onclick="sortTable(1, 'guides-table')">{!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell cursor-pointer" onclick="sortTable(2, 'guides-table')">{!!trans('main.Address')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell cursor-pointer" onclick="sortTable(3, 'guides-table')">{!!trans('main.Country')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell cursor-pointer" onclick="sortTable(4, 'guides-table')">{!!trans('main.City')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell cursor-pointer" onclick="sortTable(5, 'guides-table')">{!!trans('main.WorkPhone')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-xl-table-cell cursor-pointer" onclick="sortTable(6, 'guides-table')">{!!trans('main.WorkContact')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guides as $guide)
                        <tr>
                            <td><span class="text-muted">#{{ $guide->id }}</span></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $guide->name }}</span>
                                    <small class="text-muted d-lg-none">{{ $guide->city_name ?? '' }}</small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $guide->address ?? '-' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $guide->country_name ?? '-' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $guide->city_name ?? '-' }}</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">{{ $guide->work_phone ?? '-' }}</span></td>
                            <td class="d-none d-xl-table-cell"><span class="text-muted">{{ $guide->work_contact ?? '-' }}</span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $guide, 'routePrefix' => 'guide'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="sample-row">
                            <td><span class="badge bg-secondary me-1">Sample</span>#S-101</td>
                            <td><div class="d-flex flex-column"><span class="fw-bold">Sample Local Guide</span><small class="text-muted d-lg-none">Rome</small></div></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">Via Roma 10</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">Italy</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">Rome</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">+39 000 000</span></td>
                            <td class="d-none d-xl-table-cell"><span class="text-muted">Ameer Sample</span></td>
                            <td class="text-end text-muted">Sample only</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($guides->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing {{ $guides->firstItem() }} to {{ $guides->lastItem() }} of {{ $guides->total() }} entries</div>
                <div>{{ $guides->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
<span id="service-name" hidden data-service-name='Guide'></span>
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
