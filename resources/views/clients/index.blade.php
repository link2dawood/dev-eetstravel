@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Clients', 'sub_title' => 'Clients List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Clients', 'icon' => 'handshake-o', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('clients.create'), \App\Client::class) !!}
                </div>
                <br>
                <br>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="clients-search" class="form-control" placeholder="Search clients..." onkeyup="filterTable('clients-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('clients-table', 'clients_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                <table id="clients-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'clients-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'clients-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'clients-table')">{!!trans('main.Country')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'clients-table')">{!!trans('main.City')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'clients-table')">{!!trans('main.Address')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'clients-table')">{!!trans('Account No')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'clients-table')">{!!trans('Company Address')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(7, 'clients-table')">{!!trans('Invoice Address')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(8, 'clients-table')">{!!trans('main.WorkPhone')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(9, 'clients-table')">{!!trans('main.WorkEmail')!!} <i class="fa fa-sort"></i></th>
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
                                @include('component.action_buttons', [
                                    'show_route' => route('clients.show', $client->id),
                                    'edit_route' => route('clients.edit', $client->id),
                                    'delete_route' => route('clients.destroy', $client->id),
                                    'model' => $client
                                ])
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">No clients found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12">
                        {{ $clients->links() }}
                    </div>
                </div>
				</div>
                <span id="service-name" hidden data-service-name='Event'></span>
            </div>
        </div>
    </section>
    {{-- <div id="service-name" data-service-name="clients"></div> --}}
@endsection


@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('clients-table');
    });
</script>
@endpush
