@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Bus Companies')

@section('content')
<!-- Delete Confirmation Modal -->
<div class="modal modal-blur fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle icon mb-2 text-danger icon-lg"></i>
                <h3>Are you sure?</h3>
                <div class="text-muted" id="delete-message">Do you really want to delete this record?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                        <div class="col">
                            <form id="deleteForm" method="GET" style="display: inline;">
                                <button type="submit" class="btn btn-danger w-100">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Service Management
                </div>
                <h2 class="page-title">
                    <i class="ti ti-bus me-2"></i>Bus Companies
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('transfer.create'), \App\Transfer::class, 'btn btn-primary') !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (Session::has('message'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-alert-circle me-2"></i>
                </div>
                <div class="flex-fill">
                    {{ Session::get('message') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('export_all'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-info-circle me-2"></i>
                </div>
                <div class="flex-fill">
                    {{ session('export_all') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- Main Content Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bus Company List</h3>
        </div>
        <div class="card-body">
            {{-- Search & Export Toolbar --}}
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" 
                               id="transfer-search" 
                               class="form-control" 
                               placeholder="Search transfers by name, city, country..."
                               onkeyup="filterTable('transfer-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" 
                            onclick="exportTableToCSV('transfer-table', 'transfers_export.csv')">
                        <i class="ti ti-download me-1"></i>
                        <span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table id="transfer-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60px;" onclick="sortTable(0, 'transfer-table')" class="cursor-pointer">
                                ID <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th onclick="sortTable(1, 'transfer-table')" class="cursor-pointer">
                                {!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(2, 'transfer-table')" class="cursor-pointer">
                                {!!trans('main.Address')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(3, 'transfer-table')" class="cursor-pointer">
                                {!!trans('main.Country')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(4, 'transfer-table')" class="cursor-pointer">
                                {!!trans('main.City')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-sm-table-cell" onclick="sortTable(5, 'transfer-table')" class="cursor-pointer">
                                {!!trans('main.Phone')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-xl-table-cell" onclick="sortTable(6, 'transfer-table')" class="cursor-pointer">
                                {!!trans('main.Contact')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="text-end">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                        <tr>
                            <td>
                                <span class="text-muted">#{{ $transfer->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $transfer->name ?? '—' }}</span>
                                    <small class="text-muted d-md-none">{{ $transfer->city_name ?? '' }}</small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="text-muted">{{ $transfer->address_first ?? '—' }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="text-muted">{{ $transfer->country_name ?? '—' }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="text-muted">{{ $transfer->city_name ?? '—' }}</span>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="text-muted">{{ $transfer->work_phone ?? '—' }}</span>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <span class="text-muted">{{ $transfer->contact_name ?? '—' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $transfer, 'routePrefix' => 'transfer'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-bus icon" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="empty-title">No bus companies found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first bus company</p>
                                    <div class="empty-action">
                                        {!! \App\Helper\PermissionHelper::getCreateButton(route('transfer.create'), \App\Transfer::class, 'btn btn-primary') !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($transfers->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $transfers->firstItem() }} to {{ $transfers->lastItem() }} of {{ $transfers->total() }} entries
                </div>
                <div>
                    {{ $transfers->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<span id="service-name" hidden data-service-name='Transfer'></span>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('transfer-table');

    // Handle delete button click
    $('.delete').on('click', function(e) {
        e.preventDefault();
        var link = $(this).data('link');
        
        // If it's a deleteMsg URL, fetch the confirmation message
        if (link.includes('deleteMsg')) {
            $.get(link, function(response) {
                $('#delete-message').html(response);
                // Convert deleteMsg URL to actual delete URL
                var deleteUrl = link.replace('deleteMsg', 'delete');
                // Set the form action
                $('#deleteForm').attr('action', deleteUrl);
            });
        
        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('myModal'));
        myModal.show();
    });
});
</script>
@endpush
