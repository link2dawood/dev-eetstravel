@extends('scaffold-interface.layouts.tabler-app')
@section('title','Clients')
@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Clients</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">Clients</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('clients.create'), \App\Client::class) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Clients List</h3>
                            <div class="col-auto ms-auto">
                                <div class="btn-list">
                                    <button class="btn btn-success btn-sm" onclick="exportTableToCSV('clients-table', 'clients_export.csv')">
                                        <i class="ti ti-download"></i> Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <input aria-label="Search by name, email, phone, country or city..." type="text" id="clients-search" class="form-control" placeholder="Search by name, email, phone, country or city..." name="search" value="{{ request('search') }}" data-list-search>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="clients-table" class="table card-table table-vcenter text-nowrap datatable" style='background:#fff; width: 100%;'>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{!!trans('main.Name')!!}</th>
                                        <th>{!!trans('main.Country')!!}</th>
                                        <th>{!!trans('main.City')!!}</th>
                                        <th class="d-none d-md-table-cell">{!!trans('main.WorkPhone')!!}</th>
                                        <th class="d-none d-md-table-cell">{!!trans('main.WorkEmail')!!}</th>
                                        <th class="d-none d-xxl-table-cell">Account No</th>
                                        <th class="d-none d-xxl-table-cell">{!!trans('main.Address')!!}</th>
                                        <th class="actions-button text-end" style="width: 140px">{!!trans('main.Actions')!!}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clients as $client)
                                    <tr>
                                        <td class="text-muted">#{{ $client->id }}</td>
                                        <td data-delete-label class="fw-bold"><a href="{{ route('clients.show', $client->id) }}" class="text-reset">{{ $client->name }}</a></td>
                                        <td>{{ $client->country_name ?: '-' }}</td>
                                        <td>{{ $client->city_name ?: '-' }}</td>
                                        <td class="d-none d-md-table-cell text-nowrap">{{ $client->work_phone ?: '-' }}</td>
                                        <td class="d-none d-md-table-cell">{{ $client->work_email ?: '-' }}</td>
                                        <td class="d-none d-xxl-table-cell">{{ $client->account_no ?: '-' }}</td>
                                        <td class="d-none d-xxl-table-cell text-muted">{{ \Illuminate\Support\Str::limit($client->address, 40) ?: '-' }}</td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                @include('component.action_buttons', [
                                                    'item' => $client,
                                                    'routePrefix' => 'clients'
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">{{ request('search') ? 'No clients match your search' : 'No clients yet' }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            {{ $clients->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="service-name" hidden data-service-name='Event'></span>
@endsection

@include('component.delete_modal_simple')


@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script src="{{ asset('js/list-search.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('clients-table');
    });
</script>
@endpush
