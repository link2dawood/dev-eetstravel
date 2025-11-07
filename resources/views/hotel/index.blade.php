@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Hotels')

@section('content')
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Service Management
                </div>
                <h2 class="page-title">
                    <i class="ti ti-building me-2"></i>Hotels
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('hotel.create'), \App\Hotel::class, 'btn btn-primary') !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
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
            <h3 class="card-title">Hotels List</h3>
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
                               id="hotels-search" 
                               class="form-control" 
                               placeholder="Search hotels by name, city, country..."
                               onkeyup="filterTable('hotels-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" 
                            onclick="exportTableToCSV('hotels-table', 'hotels_export.csv')">
                        <i class="ti ti-download me-1"></i>
                        <span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table id="hotels-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60px;" onclick="sortTable(0, 'hotels-table')" class="cursor-pointer">
                                ID <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th onclick="sortTable(1, 'hotels-table')" class="cursor-pointer">
                                {!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(2, 'hotels-table')" class="cursor-pointer">
                                {!!trans('main.Address')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(3, 'hotels-table')" class="cursor-pointer">
                                {!!trans('main.Country')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(4, 'hotels-table')" class="cursor-pointer">
                                {!!trans('main.City')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-sm-table-cell" onclick="sortTable(5, 'hotels-table')" class="cursor-pointer">
                                {!!trans('main.WorkPhone')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="d-none d-xl-table-cell" onclick="sortTable(6, 'hotels-table')" class="cursor-pointer">
                                {!!trans('main.ContactEmail')!!} <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th class="text-end">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hotels as $hotel)
                        <tr>
                            <td>
                                <span class="text-muted">#{{ $hotel->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $hotel->name }}</span>
                                    <small class="text-muted d-lg-none">{{ $hotel->city_name ?? '' }}</small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="text-muted">{{ $hotel->address ?? '—' }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="text-muted">{{ $hotel->country_name ?? '—' }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="text-muted">{{ $hotel->city_name ?? '—' }}</span>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="text-muted">{{ $hotel->work_phone ?? '—' }}</span>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <span class="text-muted">{{ $hotel->contact_email ?? '—' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $hotel, 'routePrefix' => 'hotel'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-building icon" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="empty-title">No hotels found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first hotel</p>
                                    <div class="empty-action">
                                        {!! \App\Helper\PermissionHelper::getCreateButton(route('hotel.create'), \App\Hotel::class, 'btn btn-primary') !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($hotels->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $hotels->firstItem() }} to {{ $hotels->lastItem() }} of {{ $hotels->total() }} entries
                </div>
                <div>
                    {{ $hotels->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('hotels-table');
    });
</script>
@endpush
