@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Buses')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Fleet Management</div>
                <h2 class="page-title"><i class="ti ti-bus me-2"></i>Buses</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                {!! \App\Helper\PermissionHelper::getCreateButton(route('bus.create'), \App\Bus::class, 'btn btn-primary') !!}
            </div>
        </div>
    </div>

    @if (Session::has('message'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex"><div><i class="ti ti-alert-circle me-2"></i></div><div class="flex-fill">{{ Session::get('message') }}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="card-title">Buses List</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input aria-label="Search buses..." type="text" id="bus-search" class="form-control" placeholder="Search buses..." name="search" value="{{ request('search') }}" data-list-search>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-success" onclick="exportTableToCSV('bus-table', 'buses_export.csv')">
                        <i class="ti ti-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="bus-table" class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px" class="cursor-pointer">ID <i class="ti ti-arrows-sort"></i></th>
                            <th class="cursor-pointer">Name <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-sm-table-cell cursor-pointer">Bus Number <i class="ti ti-arrows-sort"></i></th>
                            <th class="d-none d-md-table-cell cursor-pointer">Bus Company <i class="ti ti-arrows-sort"></i></th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buses as $bus)
                        <tr>
                            <td><span class="text-muted">#{{ $bus->id }}</span></td>
                            <td data-delete-label><span class="fw-bold">{{ $bus->name ?: '—' }}</span></td>
                            <td class="d-none d-sm-table-cell"><span class="text-muted">{{ $bus->bus_number ?: '—' }}</span></td>
                            <td class="d-none d-md-table-cell"><span class="text-muted">{{ $bus->transfer_name ?? '—' }}</span></td>
                            <td class="text-end">
                                <div class="btn-list justify-content-end">
                                    @include('component.action_buttons', ['item' => $bus, 'routePrefix' => 'bus'])
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon"><i class="ti ti-bus icon" style="font-size: 3rem;"></i></div>
                                    <p class="empty-title">{{ request('search') ? 'No buses match your search' : 'No buses found' }}</p>
                                    <p class="empty-subtitle text-muted">{{ request('search') ? 'Try another name, bus number, or company.' : 'Get started by adding your first bus' }}</p>
                                    <div class="empty-action">
                                        @if(request('search'))
                                        <a href="{{ route('bus.index') }}" class="btn btn-primary">Clear search</a>
                                        @else
                                        {!! \App\Helper\PermissionHelper::getCreateButton(route('bus.create'), \App\Bus::class, 'btn btn-primary') !!}
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($buses->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Showing {{ $buses->firstItem() }} to {{ $buses->lastItem() }} of {{ $buses->total() }} entries</div>
                <div>{{ $buses->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/list-search.js') }}"></script>

@endpush

@include('component.delete_modal_simple')
