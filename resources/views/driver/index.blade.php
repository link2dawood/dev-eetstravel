@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Drivers', 'sub_title' => 'Drivers List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Drivers', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
                    <div>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('driver.create'), \App\Driver::class) !!}
                    </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="driver-search" class="form-control" placeholder="Search drivers..." onkeyup="filterTable('driver-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('driver-table', 'drivers_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="driver-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'driver-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'driver-table')">Name <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'driver-table')">Phone <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'driver-table')">Email <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'driver-table')">Bus Company <i class="fa fa-sort"></i></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drivers as $driver)
                            <tr>
                                <td>{{ $driver->id }}</td>
                                <td>{{ $driver->name ?? '' }}</td>
                                <td>{{ $driver->phone ?? '' }}</td>
                                <td>{{ $driver->email ?? '' }}</td>
                                <td>{{ $driver->transfer_name ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('driver.show', ['driver' => $driver->id]),
                                        'edit' => route('driver.edit', ['driver' => $driver->id]),
                                        'delete_msg' => "/driver/{$driver->id}/delete_msg"
                                    ], false, $driver) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No drivers found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <span id="service-name" hidden data-service-name='Driver'></span>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('driver-table');
});
</script>
@endpush