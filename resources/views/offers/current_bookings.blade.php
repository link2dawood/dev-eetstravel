@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
        ['title' => 'Current Bookings', 'sub_title' => 'Offer List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Currencies', 'icon' => null, 'route' => null]
        ]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="current-bookings-search" class="form-control" placeholder="Search current bookings..." onkeyup="filterTable('current-bookings-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('current-bookings-table', 'current_bookings_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="current-bookings-table" class="table table-striped table-bordered table-hover bootstrap-table" style="background:#fff; width: 100%;">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'current-bookings-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'current-bookings-table')">{!! trans('Tour') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'current-bookings-table')">{!! trans('Hotel Name') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'current-bookings-table')">{!! trans('City') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'current-bookings-table')">{!! trans('Status') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'current-bookings-table')">{!! trans('Date of Stay') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'current-bookings-table')">SIN <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(7, 'current-bookings-table')">DOU <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(8, 'current-bookings-table')">TRI <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(9, 'current-bookings-table')">{!! trans('Cancellation Policy') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(10, 'current-bookings-table')">{!! trans('Payments Made') !!} <i class="fa fa-sort"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($processedBookings as $booking)
                            <tr>
                                <td>{{ $booking->id }}</td>
                                <td>{{ $booking->tour_name }}</td>
                                <td>{{ $booking->hotel_name }}</td>
                                <td>{{ $booking->city_name }}</td>
                                <td>{{ $booking->status_name }}</td>
                                <td>{{ $booking->stay_date }}</td>
                                <td>-</td> <!-- SIN -->
                                <td>-</td> <!-- DOU -->
                                <td>-</td> <!-- TRI -->
                                <td>{{ $booking->cancel_policy }}</td>
                                <td>{{ $booking->payment_policy }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center">No current bookings found</td>
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
    // Initialize Bootstrap table
    initializeBootstrapTable('current-bookings-table');
});
</script>
@endpush