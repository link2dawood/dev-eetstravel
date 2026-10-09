@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Flights')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Service Management</div>
                <h2 class="page-title"><i class="ti ti-plane me-2"></i>Flights</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                {!! \App\Helper\PermissionHelper::getCreateButton(route('flights.create'), \App\Flight::class, 'btn btn-primary') !!}
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

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">Filter Flights</h3></div>
        <div class="card-body">
            <form id="search-form" method="GET" action="{{ route('flights.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="btn-list w-100">
                            <button type="submit" class="btn btn-primary flex-fill"><i class="ti ti-filter me-1"></i>Filter</button>
                            <a href="{{ route('flights.index') }}" class="btn btn-ghost-secondary flex-fill"><i class="ti ti-x me-1"></i>Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Flights List</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="flights-search" class="form-control" placeholder="Search flights..." onkeyup="filterTable('flights-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('flights-table', 'flights_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="flights-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" onclick="sortTable(0, 'flights-table')" class="cursor-pointer">ID <i class="ti ti-arrows-sort"></i></th>
                            <th onclick="sortTable(1, 'flights-table')" class="cursor-pointer">Flight No <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(2, 'flights-table')" class="cursor-pointer">Date <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(3, 'flights-table')" class="cursor-pointer">From <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(4, 'flights-table')" class="cursor-pointer">To <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell" onclick="sortTable(5, 'flights-table')" class="cursor-pointer">Airline <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flights as $flight)
                        <tr>
                            <td><span class="text-muted">#{{ $flight->id }}</span></td>
                            <td><span class="fw-bold">{{ $flight->flight_no ?? '—' }}</span></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $flight->date ?? '—' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $flight->city_from_name ?? '—' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $flight->city_to_name ?? '—' }}</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">{{ $flight->airline ?? '—' }}</span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $flight, 'routePrefix' => 'flights'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon"><i class="ti ti-plane icon" style="font-size: 3rem;"></i></div>
                                    <p class="empty-title">No flights found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first flight</p>
                                    <div class="empty-action">
                                        {!! \App\Helper\PermissionHelper::getCreateButton(route('flights.create'), \App\Flight::class, 'btn btn-primary') !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($flights->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing {{ $flights->firstItem() }} to {{ $flights->lastItem() }} of {{ $flights->total() }} entries</div>
                <div>{{ $flights->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>$(document).ready(function() { initializeBootstrapTable('flights-table'); });</script>
@endpush
