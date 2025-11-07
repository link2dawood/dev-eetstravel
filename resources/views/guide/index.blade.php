@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Guides')

@section('content')
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
                            <th style="width:60px" onclick="sortTable(0, 'guides-table')" class="cursor-pointer">ID <i class="ti ti-arrows-sort"></i></th>
                            <th onclick="sortTable(1, 'guides-table')" class="cursor-pointer">{!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(2, 'guides-table')" class="cursor-pointer">{!!trans('main.Address')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(3, 'guides-table')" class="cursor-pointer">{!!trans('main.Country')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(4, 'guides-table')" class="cursor-pointer">{!!trans('main.City')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell" onclick="sortTable(5, 'guides-table')" class="cursor-pointer">{!!trans('main.WorkPhone')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-xl-table-cell" onclick="sortTable(6, 'guides-table')" class="cursor-pointer">{!!trans('main.WorkContact')!!} <i class="ti ti-arrows-sort"></i></th>
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
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $guide->address ?? '—' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $guide->country_name ?? '—' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $guide->city_name ?? '—' }}</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">{{ $guide->work_phone ?? '—' }}</span></td>
                            <td class="d-none d-xl-table-cell"><span class="text-muted">{{ $guide->contact_name ?? '—' }}</span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $guide, 'routePrefix' => 'guide'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon"><i class="ti ti-user-check icon" style="font-size: 3rem;"></i></div>
                                    <p class="empty-title">No guides found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first guide</p>
                                    <div class="empty-action">
                                        {!! \App\Helper\PermissionHelper::getCreateButton(route('guide.create'), \App\Guide::class, 'btn btn-primary') !!}
                                    </div>
                                </div>
                            </td>
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
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>$(document).ready(function() { initializeBootstrapTable('guides-table'); });</script>
@endpush
