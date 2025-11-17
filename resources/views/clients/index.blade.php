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
                                <input type="text" id="clients-search" class="form-control" placeholder="Search clients..." onkeyup="filterTable('clients-table', this.value)">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="clients-table" class="table card-table table-vcenter text-nowrap datatable" style='background:#fff; width: 100%;'>
                                <thead>
                                    <tr>
                                        <th onclick="sortTable(0, 'clients-table')">ID <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(1, 'clients-table')">{!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(2, 'clients-table')">{!!trans('main.Country')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(3, 'clients-table')">{!!trans('main.City')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(4, 'clients-table')">{!!trans('main.Address')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(5, 'clients-table')">{!!trans('Account No')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(6, 'clients-table')">{!!trans('Company Address')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(7, 'clients-table')">{!!trans('Invoice Address')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(8, 'clients-table')">{!!trans('main.WorkPhone')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th onclick="sortTable(9, 'clients-table')">{!!trans('main.WorkEmail')!!} <i class="ti ti-arrows-sort"></i></th>
                                        <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clients as $client)
                                    <tr>
                                        <td>{{ $client->id }}</td>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->country_name ?? '' }}</td>
                                        <td>{{ $client->city_name ?? '' }}</td>
                                        <td>{{ $client->address }}</td>
                                        <td>{{ $client->account_no }}</td>
                                        <td>{{ $client->company_address }}</td>
                                        <td>{{ $client->invoice_address }}</td>
                                        <td>{{ $client->work_phone }}</td>
                                        <td>{{ $client->work_email }}</td>
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
                                        <td colspan="11" class="text-center">No clients found</td>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('clients-table');
    });
</script>
@endpush
