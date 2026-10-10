@extends('scaffold-interface.layouts.tabler-app')
@section('title','Current Bookings')
@section('content')
@include('layouts.title', ['title' => 'Current Bookings', 'sub_title' => 'Booking List', 'breadcrumbs' => [['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')], ['title' => 'Current Bookings', 'icon' => null, 'route' => null]]])
<section class="content offer-page"><div class="box box-primary"><div class="box-body">
    <div class="row align-items-center mb-3 toolbar-row"><div class="col-md-6"><input type="text" class="form-control" placeholder="Search current bookings..." oninput="offerFilterTable('current-bookings-table', this.value)"></div><div class="col-md-6 text-md-end"><button type="button" class="btn btn-success btn-sm" onclick="offerExportTable('current-bookings-table', 'current_bookings_export.csv')"><i class="ti ti-download"></i> Export CSV</button></div></div>
    @php $hasRows = count($processedBookings) > 0; $rows = $hasRows ? collect($processedBookings) : collect([(object)['id'=>'S-201','tour_name'=>'Sample Alps Group','hotel_name'=>'Sample Grand Hotel','city_name'=>'Zurich','status_name'=>'Confirmed','stay_date'=>now()->addDays(21)->format('Y-m-d'),'cancel_policy'=>'7 days before arrival: 50% can be cancelled free of charge.','payment_policy'=>'30% deposit before arrival.','model'=>null]]); @endphp
    <div class="table-responsive"><table id="current-bookings-table" class="table table-striped table-bordered table-hover"><thead><tr><th onclick="offerSortTable('current-bookings-table',0)">ID</th><th>Tour</th><th>Hotel Name</th><th>City</th><th>Status</th><th>Date of Stay</th><th>Cancellation Policy</th><th>Payments Made</th><th>Actions</th></tr></thead><tbody>@foreach($rows as $booking)<tr class="{{ $hasRows ? '' : 'sample-row' }}"><td>{{ $booking->id }} @unless($hasRows)<span class="badge bg-secondary sample-badge">Sample</span>@endunless</td><td data-delete-label>{{ $booking->tour_name }}</td><td>{{ $booking->hotel_name }}</td><td>{{ $booking->city_name }}</td><td>{{ $booking->status_name }}</td><td>{{ $booking->stay_date }}</td><td><div class="policy-text">{{ $booking->cancel_policy }}</div></td><td><div class="policy-text">{{ $booking->payment_policy }}</div></td><td class="actions-cell">@if($hasRows && !empty($booking->model)) @include('component.action_buttons', ['item' => $booking->model, 'routePrefix' => 'tour_package']) @else <span class="text-muted small">{{ $hasRows ? 'No booking record linked' : 'Sample only' }}</span> @endif</td></tr>@endforeach</tbody></table></div>
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