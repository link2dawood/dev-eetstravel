@extends('scaffold-interface.layouts.tabler-app')
@section('title','Current Offers')
@section('content')
@include('layouts.title', ['title' => 'Current Offers', 'sub_title' => 'Offer List', 'breadcrumbs' => [['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')], ['title' => 'Current Offers', 'icon' => null, 'route' => null]]])
<section class="content offer-page"><div class="box box-primary"><div class="box-body">
    <div class="row align-items-center mb-3 toolbar-row"><div class="col-md-6"><input type="text" class="form-control" placeholder="Search current offers..." oninput="offerFilterTable('current-offers-table', this.value)"></div><div class="col-md-6 text-md-end"><button type="button" class="btn btn-success btn-sm" onclick="offerExportTable('current-offers-table', 'current_offers_export.csv')"><i class="ti ti-download"></i> Export CSV</button></div></div>
    @php
        $hasRows = $tours->count() > 0;
        $sampleRows = collect([(object)['id' => 'S-101', 'name' => 'Sample Rome Spring Offer', 'city_name' => 'Rome', 'status_name' => 'Offered with Option', 'status_color' => '#066fd1', 'departure_date' => now()->addDays(14), 'retirement_date' => now()->addDays(20), 'pax' => 24, 'created_at' => now()]]);
        $rows = $hasRows ? $tours : $sampleRows;
    @endphp
    <div class="table-responsive"><table id="current-offers-table" class="table table-striped table-bordered table-hover"><thead><tr><th onclick="offerSortTable('current-offers-table',0)">ID <i class="ti ti-arrows-sort"></i></th><th onclick="offerSortTable('current-offers-table',1)">Tour Name <i class="ti ti-arrows-sort"></i></th><th>City</th><th>Status</th><th>Departure Date</th><th>Return Date</th><th>PAX</th><th>Created At</th><th>Actions</th></tr></thead><tbody>
    @foreach($rows as $tour)
        @php $cityName = $hasRows ? optional($tour->city_begin)->name : $tour->city_name; $statusName = $hasRows ? $tour->getStatusName() : $tour->status_name; $statusColor = $hasRows ? $tour->getStatusColor() : $tour->status_color; @endphp
        <tr class="{{ $hasRows ? '' : 'sample-row' }}"><td>{{ $tour->id }} @unless($hasRows)<span class="badge bg-secondary sample-badge">Sample</span>@endunless</td><td data-delete-label>{{ $tour->name }}</td><td>{{ $cityName ?: '—' }}</td><td><span class="badge" style="background-color: {{ $statusColor }}20; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}40;">{{ $statusName ?: '—' }}</span></td><td>{{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—' }}</td><td>{{ $tour->retirement_date ? \Carbon\Carbon::parse($tour->retirement_date)->format('Y-m-d') : '—' }}</td><td>{{ $tour->pax ?? '—' }}</td><td>{{ $tour->created_at ? \Carbon\Carbon::parse($tour->created_at)->format('Y-m-d H:i') : '—' }}</td><td class="actions-cell">@if($hasRows) @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour']) @else <span class="text-muted small">Sample only</span> @endif</td></tr>
    @endforeach
    </tbody></table></div>
</div></div></section>
@include('component.delete_modal_simple')
@endsection
@push('styles')
<style>
    .offer-page .toolbar-row { gap: .75rem; }
    .offer-page .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .offer-page table { background: #fff; min-width: 980px; }
    .offer-page th { white-space: nowrap; cursor: pointer; }
    .offer-page td { vertical-align: middle; }
    .offer-page .policy-text { max-width: 260px; white-space: normal; line-height: 1.35; }
    .offer-page .actions-cell .btn-list { display: inline-flex; flex-wrap: nowrap; justify-content: center; gap: .35rem; }
    .offer-page .sample-row { background: #f8fafc; }
    .offer-page .sample-badge { font-size: .7rem; }
</style>
@endpush
@push('scripts')
<script>
(function () {
    function getCellText(row, index) { const cell = row.children[index]; return cell ? cell.innerText.trim().toLowerCase() : ''; }
    window.offerFilterTable = function(tableId, value) {
        const table = document.getElementById(tableId); if (!table) return;
        const query = (value || '').toLowerCase().trim();
        table.querySelectorAll('tbody tr').forEach(function(row) { row.style.display = !query || row.innerText.toLowerCase().includes(query) ? '' : 'none'; });
    };
    window.offerSortTable = function(tableId, columnIndex) {
        const table = document.getElementById(tableId); if (!table) return;
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const direction = table.dataset.sortColumn == columnIndex && table.dataset.sortDirection === 'asc' ? 'desc' : 'asc';
        table.dataset.sortColumn = columnIndex; table.dataset.sortDirection = direction;
        rows.sort(function(a, b) {
            const left = getCellText(a, columnIndex); const right = getCellText(b, columnIndex);
            const leftNumber = parseFloat(left.replace(/[^0-9.-]/g, '')); const rightNumber = parseFloat(right.replace(/[^0-9.-]/g, ''));
            if (!Number.isNaN(leftNumber) && !Number.isNaN(rightNumber)) return direction === 'asc' ? leftNumber - rightNumber : rightNumber - leftNumber;
            return direction === 'asc' ? left.localeCompare(right) : right.localeCompare(left);
        });
        rows.forEach(function(row) { tbody.appendChild(row); });
    };
    window.offerExportTable = function(tableId, filename) {
        const table = document.getElementById(tableId); if (!table) return;
        const rows = Array.from(table.querySelectorAll('tr')).filter(function(row) { return row.style.display !== 'none'; });
        const csv = rows.map(function(row) { return Array.from(row.querySelectorAll('th,td')).map(function(cell) { return '"' + cell.innerText.replace(/"/g, '""').trim() + '"'; }).join(','); }).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = filename || 'offers_export.csv';
        document.body.appendChild(link); link.click(); document.body.removeChild(link); setTimeout(function() { URL.revokeObjectURL(link.href); }, 100);
    };
})();
</script>
@endpush