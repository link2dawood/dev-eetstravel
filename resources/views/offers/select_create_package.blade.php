@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create Offer')
@section('content')
@include('layouts.title', [
    'title' => 'Create Offer',
    'sub_title' => 'Select a hotel package',
    'breadcrumbs' => [
        ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
        ['title' => 'Current Offers', 'icon' => null, 'route' => route('current_offers.index')],
        ['title' => 'Create Offer', 'route' => null],
    ]
])
<section class="content offer-page">
    <div class="box box-primary">
        <div class="box-body">
            <div class="alert alert-info">
                Offers are created for a hotel service/package. Select a package below, then fill the offer form.
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Search package, tour, status..." oninput="offerFilterTable('offer-package-table', this.value)">
                </div>
            </div>
            <div class="table-responsive">
                <table id="offer-package-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th onclick="offerSortTable('offer-package-table',0)">ID <i class="ti ti-arrows-sort"></i></th>
                            <th onclick="offerSortTable('offer-package-table',1)">Package <i class="ti ti-arrows-sort"></i></th>
                            <th>Tour</th>
                            <th>Status</th>
                            <th>Stay Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td>#{{ $package->id }}</td>
                                <td>{{ $package->name }}</td>
                                <td>{{ optional($package->getTour())->name ?: '—' }}</td>
                                <td>{{ $package->getStatusName() ?: '—' }}</td>
                                <td>{{ $package->time_from ? \Carbon\Carbon::parse($package->time_from)->format('Y-m-d') : '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('offers.create', $package->id) }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-plus me-1"></i>Create Offer
                                    </a>
                                    <a href="{{ route('offers', $package->id) }}" class="btn btn-outline-secondary btn-sm">
                                        View Offers
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hotel packages are available for offer creation.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
@push('styles')
<style>
    .offer-page .table-responsive { overflow-x: auto; }
    .offer-page th { white-space: nowrap; cursor: pointer; }
    .offer-page td { vertical-align: middle; }
</style>
@endpush
@push('scripts')
<script>
(function () {
    function getCellText(row, index) { const cell = row.children[index]; return cell ? cell.innerText.trim().toLowerCase() : ''; }
    window.offerFilterTable = window.offerFilterTable || function(tableId, value) {
        const table = document.getElementById(tableId); if (!table) return;
        const query = (value || '').toLowerCase().trim();
        table.querySelectorAll('tbody tr').forEach(function(row) { row.style.display = !query || row.innerText.toLowerCase().includes(query) ? '' : 'none'; });
    };
    window.offerSortTable = window.offerSortTable || function(tableId, columnIndex) {
        const table = document.getElementById(tableId); if (!table) return;
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const direction = table.dataset.sortColumn == columnIndex && table.dataset.sortDirection === 'asc' ? 'desc' : 'asc';
        table.dataset.sortColumn = columnIndex; table.dataset.sortDirection = direction;
        rows.sort(function(a, b) { const left = getCellText(a, columnIndex); const right = getCellText(b, columnIndex); return direction === 'asc' ? left.localeCompare(right) : right.localeCompare(left); });
        rows.forEach(function(row) { tbody.appendChild(row); });
    };
})();
</script>
@endpush
