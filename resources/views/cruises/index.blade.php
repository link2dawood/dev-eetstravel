@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Cruises')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Service Management</div>
                <h2 class="page-title"><i class="ti ti-ship me-2"></i>Cruises</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                {!! \App\Helper\PermissionHelper::getCreateButton(route('cruises.create'), \App\Cruises::class, 'btn btn-primary') !!}
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

    <div class="card">
        <div class="card-header"><h3 class="card-title">Cruises List</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="cruise-search" class="form-control" placeholder="Search cruises..." onkeyup="filterTable('cruise-table', this.value)">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('cruise-table', 'cruises_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="cruise-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" onclick="sortTable(0, 'cruise-table')" class="cursor-pointer">ID <i class="ti ti-arrows-sort"></i></th>
                            <th onclick="sortTable(1, 'cruise-table')" class="cursor-pointer">{!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(2, 'cruise-table')" class="cursor-pointer">{!!trans('main.Datefrom')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell" onclick="sortTable(3, 'cruise-table')" class="cursor-pointer">{!!trans('main.Dateto')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(4, 'cruise-table')" class="cursor-pointer">{!!trans('main.CountryFrom')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-lg-table-cell" onclick="sortTable(5, 'cruise-table')" class="cursor-pointer">{!!trans('main.Cityfrom')!!} <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cruises as $cruise)
                        <tr>
                            <td><span class="text-muted">#{{ $cruise->id }}</span></td>
                            <td><span class="fw-bold">{{ $cruise->name ?? '—' }}</span></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $cruise->date_from ?? '—' }}</span></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $cruise->date_to ?? '—' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $cruise->country_from_name ?? '—' }}</span></td>
                            <td class="d-none d-lg-table-cell"><span class="text-muted">{{ $cruise->city_from_name ?? '—' }}</span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $cruise, 'routePrefix' => 'cruises'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon"><i class="ti ti-ship icon" style="font-size: 3rem;"></i></div>
                                    <p class="empty-title">No cruises found</p>
                                    <p class="empty-subtitle text-muted">Get started by adding your first cruise</p>
                                    <div class="empty-action">
                                        {!! \App\Helper\PermissionHelper::getCreateButton(route('cruises.create'), \App\Cruises::class, 'btn btn-primary') !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($cruises->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing {{ $cruises->firstItem() }} to {{ $cruises->lastItem() }} of {{ $cruises->total() }} entries</div>
                <div>{{ $cruises->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
<span id="service-name" hidden data-service-name='Cruises'></span>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>$(document).ready(function() { initializeBootstrapTable('cruise-table'); });</script>
@endpush
