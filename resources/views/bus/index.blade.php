@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Buses', 'sub_title' => 'Buses List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Bus', 'icon' => 'bus', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('bus.create'), \App\Bus::class) !!}
                </div>
                <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                    @include('legend.buses_legend')
                    </span>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="bus-search" class="form-control" placeholder="Search buses..." onkeyup="filterTable('bus-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('bus-table', 'buses_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="bus-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'bus-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'bus-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'bus-table')">{!!trans('main.Busnumber')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'bus-table')">{!!trans('main.BusCompany')!!} <i class="fa fa-sort"></i></th>
                                <th>{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buses as $bus)
                            <tr>
                                <td>{{ $bus->id }}</td>
                                <td>{{ $bus->name ?? '' }}</td>
                                <td>{{ $bus->bus_number ?? '' }}</td>
                                <td>{{ $bus->transfer_name ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('bus.show', ['bu' => $bus->id]),
                                        'edit' => route('bus.edit', ['bu' => $bus->id]),
                                        'delete_msg' => "/bus/{$bus->id}/deleteMsg"
                                    ], false, $bus) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No buses found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('bus-table');
});
</script>
@endpush