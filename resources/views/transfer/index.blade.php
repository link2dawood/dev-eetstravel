@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Bus Company', 'sub_title' => 'Bus Company List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Bus Company', 'icon' => 'exchange', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
                    <div>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('transfer.create'), \App\Transfer::class) !!}
                    </div>
                @if(session('export_all'))
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    {{session('export_all')}}
                </div>
                @endif
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="transfer-search" class="form-control" placeholder="Search transfers..." onkeyup="filterTable('transfer-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('transfer-table', 'transfers_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="transfer-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'transfer-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'transfer-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'transfer-table')">{!!trans('main.Address')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'transfer-table')">{!!trans('main.Country')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'transfer-table')">{!!trans('main.City')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'transfer-table')">{!!trans('main.Phone')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'transfer-table')">{!!trans('main.Contact')!!} <i class="fa fa-sort"></i></th>
                                <th>{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $transfer)
                            <tr>
                                <td>{{ $transfer->id }}</td>
                                <td>{{ $transfer->name ?? '' }}</td>
                                <td>{{ $transfer->address_first ?? '' }}</td>
                                <td>{{ $transfer->country_name ?? '' }}</td>
                                <td>{{ $transfer->city_name ?? '' }}</td>
                                <td>{{ $transfer->work_phone ?? '' }}</td>
                                <td>{{ $transfer->contact_name ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('transfer.show', ['transfer' => $transfer->id]),
                                        'edit' => route('transfer.edit', ['transfer' => $transfer->id]),
                                        'delete_msg' => "/transfer/{$transfer->id}/deleteMsg"
                                    ], false, $transfer) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No transfers found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{ $transfers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <span id="service-name" hidden data-service-name='Transfer'></span>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('transfer-table');
});
</script>
@endpush
