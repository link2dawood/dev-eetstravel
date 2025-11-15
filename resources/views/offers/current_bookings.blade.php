@extends('scaffold-interface.layouts.tabler-app')
@section('title','Index')
@section('content')
    @include('layouts.title',
        ['title' => 'Current Bookings', 'sub_title' => 'Booking List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Current Bookings', 'icon' => null, 'route' => null]
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
                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table id="current-bookings-table" class="table table-striped table-bordered table-hover bootstrap-table" style="background:#fff; width: 100%; min-width: 1000px;">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'current-bookings-table')" style="width: 60px;">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'current-bookings-table')">{!! trans('Tour') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'current-bookings-table')">{!! trans('Hotel Name') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'current-bookings-table')">{!! trans('City') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'current-bookings-table')">{!! trans('Status') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'current-bookings-table')">{!! trans('Date of Stay') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'current-bookings-table')" style="width: 60px;">SIN <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(7, 'current-bookings-table')" style="width: 60px;">DOU <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(8, 'current-bookings-table')" style="width: 60px;">TRI <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(9, 'current-bookings-table')" style="width: 150px;">{!! trans('Cancellation Policy') !!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(10, 'current-bookings-table')" style="width: 200px;">{!! trans('Payments Made') !!} <i class="fa fa-sort"></i></th>
                                <th class="actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
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
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td>
                                    <div style="max-width: 150px; white-space: normal; word-wrap: break-word;">
                                        {{ $booking->cancel_policy }}
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 200px; white-space: normal; word-wrap: break-word;">
                                        {{ $booking->payment_policy }}
                                    </div>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    @if(!empty($booking->model))
                                        @include('component.action_buttons', [
                                            'item' => $booking->model,
                                            'routePrefix' => 'tour_package'
                                        ])
                                    @else
                                        <span class="text-muted small">No booking record linked</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center">No current bookings found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @include('scaffold-interface.dashboard.components.delete-modal')
@endsection

@push('styles')
<style>
/* Action Buttons Container */
.action-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

/* Individual Action Button */
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    padding: 0;
    border: 2px solid;
    border-radius: 8px;
    background-color: transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0;
    text-decoration: none;
}

.btn-action:hover {
    transform: translateY(-2px);
}

.btn-action svg {
    width: 20px;
    height: 20px;
}

/* Edit Button */
.edit-action {
    color: #f9a825;
    border-color: #f9a825;
}

.edit-action:hover {
    background-color: #f9a825;
    color: white;
}

/* Delete Button */
.delete-action {
    color: #ef5350;
    border-color: #ef5350;
}

.delete-action:hover {
    background-color: #ef5350;
    color: white;
}

/* Table Cell Text Wrapping */
.table td {
    vertical-align: middle;
}

/* Improved column width control */
#current-bookings-table th:nth-child(11),
#current-bookings-table td:nth-child(11) {
    min-width: 200px;
    max-width: 250px;
}

#current-bookings-table th:nth-child(10),
#current-bookings-table td:nth-child(10) {
    min-width: 150px;
    max-width: 180px;
}

/* Better text display in long columns */
.table td > div {
    line-height: 1.4;
}

/* Actions column */
#current-bookings-table th:nth-child(12),
#current-bookings-table td:nth-child(12) {
    text-align: center;
}

.actions-cell {
    padding: 12px 8px !important;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('current-bookings-table');
});
</script>
@endpush