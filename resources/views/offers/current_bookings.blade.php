@extends('scaffold-interface.layouts.tabler-app')
@section('title','Current Bookings')
@section('content')
@include('layouts.title', ['title' => 'Current Bookings', 'sub_title' => 'Booking List', 'breadcrumbs' => [['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')], ['title' => 'Current Bookings', 'icon' => null, 'route' => null]]])
<section class="content offer-page"><div class="box box-primary"><div class="box-body">
    <div class="row align-items-center mb-3 toolbar-row">
        <div class="col-md-6">
            <form method="GET" action="{{ route('current_bookings.index') }}" class="d-flex gap-2">
                <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Search by hotel, tour or booking ID" aria-label="Search current bookings">
                <button type="submit" class="btn btn-primary">Search</button>
                @if($search !== '')
                    <a href="{{ route('current_bookings.index') }}" class="btn btn-secondary">Clear</a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}" class="btn btn-success btn-sm"><i class="ti ti-download"></i> Export CSV</a>
        </div>
    </div>
    <p class="text-secondary small mb-2">Confirmed hotel bookings whose stay has not ended yet, by check-in date.</p>
    <div class="table-responsive"><table id="current-bookings-table" class="table table-striped table-bordered table-hover"><thead><tr>
        <th>ID</th>
        <th>Tour</th>
        <th>Hotel Name</th>
        <th>City</th>
        <th>Status</th>
        <th>Stay</th>
        <th>Cancellation Policy</th>
        <th>Payment Policy</th>
        <th>Actions</th>
    </tr></thead><tbody>
    @forelse($processedBookings as $booking)
        <tr>
            <td>{{ $booking->id }}</td>
            <td data-delete-label>{{ $booking->tour_name ?: '-' }}</td>
            <td>{{ $booking->hotel_name ?: '-' }}</td>
            <td>{{ $booking->city_name ?: '-' }}</td>
            <td>{{ $booking->status_name ?: '-' }}</td>
            <td class="text-nowrap">{{ $booking->stay_date ?: '-' }}@if($booking->stay_end && $booking->stay_end !== $booking->stay_date) &ndash; {{ $booking->stay_end }}@endif</td>
            <td><div class="policy-text">{{ $booking->cancel_policy ?: 'N/A' }}</div></td>
            <td><div class="policy-text">{{ $booking->payment_policy ?: 'N/A' }}</div></td>
            <td class="actions-cell">@include('component.action_buttons', ['item' => $booking->model, 'routePrefix' => 'tour_package'])</td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center text-secondary py-4">
                {{ $search !== '' ? 'No current bookings match your search.' : 'No current bookings.' }}
            </td>
        </tr>
    @endforelse
    </tbody></table></div>
    @if($packages->hasPages())
        <div class="mt-3">{{ $packages->links() }}</div>
    @endif
</div></div></section>
@include('component.delete_modal_simple')
@endsection
@push('styles')
<style>
    .offer-page .toolbar-row { gap: .75rem; }
    .offer-page .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .offer-page table { background: #fff; min-width: 980px; }
    .offer-page th { white-space: nowrap; }
    .offer-page td { vertical-align: middle; }
    .offer-page .policy-text { max-width: 260px; white-space: normal; line-height: 1.35; }
    .offer-page .actions-cell .btn-list { display: inline-flex; flex-wrap: nowrap; justify-content: center; gap: .35rem; }
</style>
@endpush
