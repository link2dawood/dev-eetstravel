@extends('scaffold-interface.layouts.tabler-app')
@section('title','Tours')

@section('post_styles')
<style>
    .tour-page .page-header { margin-bottom: 1.25rem; }
    .tour-page .status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }
    .tour-page .clickable-row { cursor: pointer; transition: background-color .15s ease, box-shadow .15s ease; }
    .tour-page .clickable-row:hover { box-shadow: inset 3px 0 0 var(--tblr-primary, #066fd1); }
    .tour-page .action-cell { white-space: nowrap; width: 1%; }
    .tour-page .action-cell .btn-list,
    .tour-page .action-cell .btn-list .btn-list { display: inline-flex; align-items: center; justify-content: flex-end; flex-wrap: nowrap; gap: .35rem; margin: 0; }
    .tour-page .action-cell .btn { display: inline-flex; align-items: center; justify-content: center; margin: 0; }
    .tour-page .empty { padding: 2.5rem 1rem; }
    .tour-page .empty-icon { font-size: 3rem; color: var(--tblr-muted); margin-bottom: 1rem; }
    .tour-page .tab-pane-title { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
    .tour-page .table-search { max-width: 360px; }
    .tour-page .pagination-wrap { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-top: 1rem; flex-wrap: wrap; }
    @media (max-width: 768px) {
        .tour-page .page-title { font-size: 1.4rem; }
        .tour-page .card-header-tabs { overflow-x: auto; overflow-y: hidden; flex-wrap: nowrap; }
        .tour-page .pagination-wrap { justify-content: center; text-align: center; }
    }
</style>
@endsection

@section('content')
<div class="container-xl tour-page">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Tour Management</div>
                <h2 class="page-title"><i class="ti ti-plane me-2"></i>Tours</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list align-items-center">
                    @include('legend.tour_legend')
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'btn btn-primary create-action-btn') !!}
                </div>
            </div>
        </div>
    </div>

    @if(session('message_buses'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="ti ti-info-circle me-2"></i>{{ session('message_buses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#tours-tab" class="nav-link active" data-bs-toggle="tab" role="tab" aria-selected="true">
                        <i class="ti ti-plane me-1"></i>Tours <span class="badge bg-blue-lt ms-1">{{ $tours->total() }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#client-tours-tab" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">
                        <i class="ti ti-users me-1"></i>Requested <span class="badge bg-purple-lt ms-1">{{ $clientTours->total() }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#monthly-chart-tab" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">
                        <i class="ti ti-chart-line me-1"></i>Monthly <span class="badge bg-green-lt ms-1">{{ $monthlyChartTours->total() + $cancelledChartTours->total() }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#archived-tours-tab" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">
                        <i class="ti ti-archive me-1"></i>Archived <span class="badge bg-secondary-lt ms-1">{{ $archivedTours->total() }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            {{-- Search/filter are applied server-side so they cover all tours, not just the current page --}}
            <form method="GET" action="{{ route('tour.index') }}" id="tour-filter-form" class="row mb-3 g-2 align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="input-icon table-search">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="search" name="search" id="tour-search" class="form-control" placeholder="Search all tours..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                        <select name="status" id="filterDropdown" class="form-select" style="max-width: 200px;">
                            <option value="">All Statuses</option>
                            <option value="quotation" {{ request('status') === 'quotation' ? 'selected' : '' }}>Quotations</option>
                            <option value="go ahead" {{ request('status') === 'go ahead' ? 'selected' : '' }}>Go Ahead</option>
                        </select>
                        @if(request('search') || request('status'))
                            <a href="{{ route('tour.index') }}" class="btn btn-ghost-secondary"><i class="ti ti-x me-1"></i>Clear</a>
                        @endif
                        <button class="btn btn-secondary export-csv" type="button">
                            <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tours-tab" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tour-table" class="table card-table table-vcenter tour-data-table">
                            <thead>
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th>{{ trans('main.Name') }}</th>
                                <th>{{ trans('main.DepDate') }}</th>
                                <th class="d-none d-lg-table-cell">{{ trans('Responsible Users') }}</th>
                                <th class="d-none d-xl-table-cell">{{ trans('Assigned Users') }}</th>
                                <th>{{ trans('main.Status') }}</th>
                                <th class="d-none d-md-table-cell">{{ trans('main.ExternalName') }}</th>
                                <th class="text-end">{{ trans('main.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tours as $tour)
                                <tr class="clickable-row" style="background: {{ $tour->getRowBackgroundColor() }};" data-href="{{ route('tour.show', ['tour' => $tour->id]) }}">
                                    <td><span class="text-muted">#{{ $tour->id }}</span></td>
                                    <td><div class="fw-bold">{{ $tour->name }}</div><small class="text-muted d-lg-none">{{ $tour->responsible_user_names ?? '' }}</small></td>
                                    <td><span class="text-muted"><span class="text-nowrap">{{ display_date($tour->departure_date) }}</span></span></td>
                                    <td class="d-none d-lg-table-cell">{{ $tour->responsible_user_names ?? '-' }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $tour->assigned_user_names ?? '-' }}</td>
                                    <td><span class="badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;"><span class="status-dot" style="background-color: {{ $tour->getStatusColor() }};"></span>{{ $tour->getStatusName() }}</span></td>
                                    <td class="d-none d-md-table-cell"><span class="text-muted">{{ $tour->external_name ?? '-' }}</span></td>
                                    <td class="text-end action-cell">@include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center py-5"><div class="empty"><div class="empty-icon"><i class="ti ti-plane"></i></div><p class="empty-title">No tours found</p><p class="empty-subtitle text-muted">Get started by creating your first tour</p></div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($tours->hasPages())<div class="pagination-wrap"><div class="text-muted">Showing {{ $tours->firstItem() }} to {{ $tours->lastItem() }} of {{ $tours->total() }} entries</div>{{ $tours->links() }}</div>@endif
                </div>

                <div class="tab-pane fade" id="client-tours-tab" role="tabpanel">
                    <div class="table-responsive">
                        <table id="client-tour-table" class="table card-table table-vcenter tour-data-table">
                            <thead><tr><th style="width:70px;">ID</th><th>{{ trans('main.Name') }}</th><th>Client</th><th>{{ trans('main.DepDate') }}</th><th>{{ trans('main.Status') }}</th><th class="d-none d-md-table-cell">{{ trans('main.ExternalName') }}</th><th class="text-end">{{ trans('main.Actions') }}</th></tr></thead>
                            <tbody>
                            @forelse($clientTours as $tour)
                                <tr class="clickable-row" style="background: {{ $tour->getRowBackgroundColor() }};" data-href="{{ route('tour.show', ['tour' => $tour->id]) }}">
                                    <td><span class="text-muted">#{{ $tour->id }}</span></td><td><div class="fw-bold">{{ $tour->name }}</div></td><td>{{ $tour->client_name ?: '-' }}</td><td><span class="text-nowrap">{{ display_date($tour->departure_date) }}</span></td><td><span class="badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;"><span class="status-dot" style="background-color: {{ $tour->getStatusColor() }};"></span>{{ $tour->getStatusName() }}</span></td><td class="d-none d-md-table-cell">{{ $tour->external_name ?? '-' }}</td><td class="text-end action-cell">@include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-5"><div class="empty"><div class="empty-icon"><i class="ti ti-users"></i></div><p class="empty-title">No records found</p><p class="empty-subtitle text-muted">There are no requested tours{{ request('search') || request('status') ? ' matching your filter' : '' }}.</p></div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($clientTours->hasPages())<div class="pagination-wrap"><div class="text-muted">Showing {{ $clientTours->firstItem() }} to {{ $clientTours->lastItem() }} of {{ $clientTours->total() }} entries</div>{{ $clientTours->links() }}</div>@endif
                </div>

                <div class="tab-pane fade" id="monthly-chart-tab" role="tabpanel">
                    <div class="tab-pane-title">
                        <h3 class="card-title mb-0">On Going Projects</h3>
                        <div class="d-flex gap-2 flex-wrap">
                            <select id="year-filter" class="form-select" style="max-width: 160px;"><option value="">All Years</option>@foreach($years as $year)<option value="{{ $year }}">{{ $year }}</option>@endforeach</select>
                            <select id="month-filter" class="form-select" style="max-width: 160px;"><option value="">All Months</option>@foreach($months as $key => $month)<option value="{{ $key }}">{{ $month }}</option>@endforeach</select>
                        </div>
                    </div>
                    <div class="table-responsive mb-4">
                        <table id="monthly-chart-table" class="table card-table table-vcenter tour-data-table">
                            <thead><tr><th>ID</th><th>{{ trans('main.Name') }}</th><th class="d-none d-lg-table-cell">{{ trans('Responsible Users') }}</th><th>{{ trans('main.Status') }}</th><th class="d-none d-md-table-cell">{{ trans('main.ExternalName') }}</th><th class="text-end">{{ trans('main.Actions') }}</th></tr></thead>
                            <tbody>
                            @forelse($monthlyChartTours as $tour)
                                <tr class="clickable-row" style="background: {{ $tour->getRowBackgroundColor() }};" data-href="{{ route('tour.show', ['tour' => $tour->id]) }}"><td>#{{ $tour->id }}</td><td><div class="fw-bold">{{ $tour->name }}</div><small class="text-muted"><span class="text-nowrap">{{ display_date($tour->departure_date, '') }}</span></small></td><td class="d-none d-lg-table-cell">{{ $tour->responsible_user_names ?? '-' }}</td><td><span class="badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">{{ $tour->getStatusName() }}</span></td><td class="d-none d-md-table-cell">{{ $tour->external_name ?? '-' }}</td><td class="text-end action-cell">@include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])</td></tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No ongoing projects found.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <h3 class="card-title">Cancelled Projects</h3>
                    <div class="table-responsive">
                        <table id="cancelled-chart-table" class="table card-table table-vcenter tour-data-table">
                            <thead><tr><th>ID</th><th>{{ trans('main.Name') }}</th><th class="d-none d-lg-table-cell">{{ trans('Responsible Users') }}</th><th>{{ trans('main.Status') }}</th><th class="d-none d-md-table-cell">{{ trans('main.ExternalName') }}</th><th class="text-end">{{ trans('main.Actions') }}</th></tr></thead>
                            <tbody>
                            @forelse($cancelledChartTours as $tour)
                                <tr class="clickable-row" style="background: {{ $tour->getRowBackgroundColor() }};" data-href="{{ route('tour.show', ['tour' => $tour->id]) }}"><td>#{{ $tour->id }}</td><td><div class="fw-bold">{{ $tour->name }}</div><small class="text-muted"><span class="text-nowrap">{{ display_date($tour->departure_date, '') }}</span></small></td><td class="d-none d-lg-table-cell">{{ $tour->responsible_user_names ?? '-' }}</td><td><span class="badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">{{ $tour->getStatusName() }}</span></td><td class="d-none d-md-table-cell">{{ $tour->external_name ?? '-' }}</td><td class="text-end action-cell">@include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])</td></tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No cancelled projects found.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="archived-tours-tab" role="tabpanel">
                    <div class="table-responsive">
                        <table id="archive-tour-table" class="table card-table table-vcenter tour-data-table">
                            <thead><tr><th>ID</th><th>{{ trans('main.Name') }}</th><th class="d-none d-lg-table-cell">{{ trans('Responsible Users') }}</th><th>{{ trans('main.DepDate') }}</th><th>{{ trans('main.Status') }}</th><th class="d-none d-md-table-cell">{{ trans('main.ExternalName') }}</th><th class="text-end">{{ trans('main.Actions') }}</th></tr></thead>
                            <tbody>
                            @forelse($archivedTours as $tour)
                                <tr class="clickable-row" style="background: {{ $tour->getRowBackgroundColor() }};" data-href="{{ route('tour.show', ['tour' => $tour->id]) }}"><td>#{{ $tour->id }}</td><td><div class="fw-bold">{{ $tour->name }}</div></td><td class="d-none d-lg-table-cell">{{ $tour->responsible_user_names ?? '-' }}</td><td><span class="text-nowrap">{{ display_date($tour->departure_date) }}</span></td><td><span class="badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">{{ $tour->getStatusName() }}</span></td><td class="d-none d-md-table-cell">{{ $tour->external_name ?? '-' }}</td><td class="text-end action-cell">@include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])</td></tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-5"><div class="empty"><div class="empty-icon"><i class="ti ti-archive"></i></div><p class="empty-title">No records found</p><p class="empty-subtitle text-muted">There are no archived tours{{ request('search') || request('status') ? ' matching your filter' : '' }}.</p></div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($archivedTours->hasPages())<div class="pagination-wrap"><div class="text-muted">Showing {{ $archivedTours->firstItem() }} to {{ $archivedTours->lastItem() }} of {{ $archivedTours->total() }} entries</div>{{ $archivedTours->links() }}</div>@endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="tour-clone-modal" tabindex="-1" aria-labelledby="tour-clone-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="tour-clone-modal-form" method="GET">
                <div class="modal-header"><h5 class="modal-title" id="tour-clone-label">Clone Tour</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <div class="alert alert-danger block-error" style="display:none;"></div>
                    <label class="form-label" for="departure_date">{{ trans('main.DepartureDate') }}</label>
                    {!! Form::text('departure_date', '', ['class' => 'form-control datepicker', 'id' => 'departure_date', 'autocomplete' => 'off', 'placeholder' => 'Select departure date']) !!}
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success pre-loader-func" id="clone_tour_send"><i class="ti ti-copy me-1"></i>{{ trans('main.Submit') }}</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" tabindex="-1" id="error_tour" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ trans('main.Warning') }}!</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><h3 class="error_tour_message mb-0"></h3></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ok</button></div></div></div>
</div>

<span id="permission" data-permission="{{ \App\Helper\PermissionHelper::checkPermission('tour.edit') }}"></span>
@endsection

@section('post_scripts')
<script>
(function() {
    function activeTable() {
        return document.querySelector('.tab-pane.active .tour-data-table');
    }

    // Search/status are filtered server-side across all tours; submit and keep the open tab
    let searchTimer = null;
    function submitFilters() {
        const form = document.getElementById('tour-filter-form');
        if (!form) return;
        const activeLink = document.querySelector('.card-header-tabs .nav-link.active');
        const hash = activeLink ? activeLink.getAttribute('href') : '';
        const params = new URLSearchParams(new FormData(form));
        Array.from(params.keys()).forEach(function(key) { if (!params.get(key)) params.delete(key); });
        const query = params.toString();
        window.location.href = form.action + (query ? '?' + query : '') + (hash || '');
    }

    function openTabFromHash() {
        if (!location.hash) return;
        const link = document.querySelector('.card-header-tabs .nav-link[href="' + location.hash + '"]');
        if (link && window.bootstrap) bootstrap.Tab.getOrCreateInstance(link).show();
    }

    function exportActiveTable() {
        const table = activeTable();
        if (!table) return;
        const rows = Array.from(table.querySelectorAll('tr')).filter(function(row) { return row.style.display !== 'none'; });
        const csv = rows.map(function(row) {
            const cells = Array.from(row.querySelectorAll('th,td')).slice(0, -1);
            return cells.map(function(cell) { return '"' + cell.innerText.replace(/"/g, '""').trim() + '"'; }).join(',');
        }).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'tours_export.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(function() { URL.revokeObjectURL(link.href); }, 100);
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(event) {
            const action = event.target.closest('.action-cell, .action-delete-btn, .clone-tour-button, a.btn, button.btn');
            if (action) event.stopPropagation();

            const row = event.target.closest('.clickable-row');
            if (row && !event.target.closest('.action-cell')) {
                const href = row.dataset.href;
                if (href) window.location.href = href;
            }

            const cloneButton = event.target.closest('.clone-tour-button');
            if (cloneButton) {
                event.preventDefault();
                const form = document.getElementById('tour-clone-modal-form');
                if (form) form.action = '/tour/' + cloneButton.dataset.id + '/clone';
                const error = document.querySelector('.block-error');
                if (error) { error.textContent = ''; error.style.display = 'none'; }
                // the button's data-bs-toggle opens the dialog; opening it here too stacked two backdrops
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('tour-clone-modal'));
                if (!cloneButton.hasAttribute('data-bs-toggle')) modal.show();
            }
        });

        openTabFromHash();
        const searchInput = document.getElementById('tour-search');
        if (searchInput && searchInput.value) {
            searchInput.focus();
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
        document.querySelectorAll('.card-header-tabs [data-bs-toggle="tab"]').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function() { history.replaceState(null, '', tab.getAttribute('href')); });
        });
        document.getElementById('tour-filter-form')?.addEventListener('submit', function(event) { event.preventDefault(); submitFilters(); });
        document.getElementById('tour-search')?.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(submitFilters, 600);
        });
        document.getElementById('filterDropdown')?.addEventListener('change', submitFilters);
        document.querySelector('.export-csv')?.addEventListener('click', exportActiveTable);

        document.getElementById('clone_tour_send')?.addEventListener('click', function(event) {
            const input = document.getElementById('departure_date');
            const error = document.querySelector('.block-error');
            if (!input || input.value.trim() !== '') return;
            event.preventDefault();
            if (error) { error.textContent = 'Enter Date'; error.style.display = 'block'; }
        });
    });
})();
</script>
@endsection




