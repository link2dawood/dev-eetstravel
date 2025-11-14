@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Drivers')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Fleet Management</div>
                <h2 class="page-title"><i class="ti ti-steering-wheel me-2"></i>Drivers</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                {!! \App\Helper\PermissionHelper::getCreateButton(route('driver.create'), \App\Driver::class, 'btn btn-primary') !!}
            </div>
        </div>
    </div>

    @if (Session::has('message'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex"><div><i class="ti ti-alert-circle me-2"></i></div><div class="flex-fill">{{ Session::get('message') }}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="card-title">Drivers List</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="driver-search" class="form-control" placeholder="Search drivers..." onkeyup="filterTable('driver-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('driver-table', 'drivers_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="driver-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" onclick="sortTable(0, 'driver-table')" class="cursor-pointer">ID <i class="ti ti-arrows-sort"></i></th>
                            <th onclick="sortTable(1, 'driver-table')" class="cursor-pointer">Name <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell" onclick="sortTable(2, 'driver-table')" class="cursor-pointer">Phone <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(3, 'driver-table')" class="cursor-pointer">Email <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(4, 'driver-table')" class="cursor-pointer">Bus Company <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                        <tr>
                            <td><span class="text-muted">#{{ $driver->id }}</span></td>
                            <td data-delete-label><span class="fw-bold">{{ $driver->name ?? '—' }}</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">{{ $driver->phone ?? '—' }}</span></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $driver->email ?? '—' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $driver->transfer_name ?? '—' }}</span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $driver, 'routePrefix' => 'driver'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon"><i class="ti ti-steering-wheel icon" style="font-size: 3rem;"></i></div>
                                    <p class="empty-title">No drivers found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first driver</p>
                                    <div class="empty-action">
                                        {!! \App\Helper\PermissionHelper::getCreateButton(route('driver.create'), \App\Driver::class, 'btn btn-primary') !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($drivers->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing {{ $drivers->firstItem() }} to {{ $drivers->lastItem() }} of {{ $drivers->total() }} entries</div>
                <div>{{ $drivers->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>$(document).ready(function() { initializeBootstrapTable('driver-table'); });</script>
@endpush

@include('component.delete_modal_simple')
