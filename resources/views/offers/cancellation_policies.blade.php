@extends('scaffold-interface.layouts.tabler-app')
@section('title','Cancellation Policies')
@section('content')
@include('layouts.title', ['title' => 'Cancellation Policies', 'sub_title' => 'Policies List', 'breadcrumbs' => [['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')], ['title' => 'Cancellation Policies', 'icon' => null, 'route' => null]]])
<section class="content offer-page"><div class="box box-primary"><div class="box-body">
    <div class="row align-items-center mb-3 toolbar-row">
        <div class="col-md-6">
            <form method="GET" action="{{ route('cancellation_policies.index') }}" class="d-flex gap-2">
                <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Search by hotel, tour or policy ID" aria-label="Search cancellation policies">
                <button type="submit" class="btn btn-primary">Search</button>
                @if($search !== '')
                    <a href="{{ route('cancellation_policies.index') }}" class="btn btn-secondary">Clear</a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}" class="btn btn-success btn-sm"><i class="ti ti-download"></i> Export CSV</a>
        </div>
    </div>
    <p class="text-secondary small mb-2">Policies in force: from each hotel booking's latest offer, for stays that have not ended yet, by check-in date.</p>
    <div class="table-responsive"><table id="cancellation-policies-table" class="table table-striped table-bordered table-hover"><thead><tr>
        <th>ID</th>
        <th>Policy</th>
        <th>Hotel Name</th>
        <th>City</th>
        <th>Status</th>
        <th>Stay</th>
        <th title="Single room price">SIN</th>
        <th title="Double room price">DOU</th>
        <th title="Triple room price">TRI</th>
        <th>Offer Date</th>
        <th>Option Date</th>
        <th>Tour Name</th>
        <th>Actions</th>
    </tr></thead><tbody>
    @forelse($processedOffers as $offer)
        <tr>
            <td>{{ $offer->id }}</td>
            <td><div class="policy-text">{{ $offer->cancel_policy ?: 'N/A' }}</div></td>
            <td>{{ $offer->hotel_name ?: '-' }}</td>
            <td>{{ $offer->city_name ?: '-' }}</td>
            <td>{{ $offer->status ?: '-' }}</td>
            <td class="text-nowrap">{{ $offer->stay_date ?: '-' }}@if($offer->stay_end && $offer->stay_end !== $offer->stay_date) &ndash; {{ $offer->stay_end }}@endif</td>
            <td class="text-nowrap">{{ $offer->SIN ?: '-' }}</td>
            <td class="text-nowrap">{{ $offer->DOU ?: '-' }}</td>
            <td class="text-nowrap">{{ $offer->TRI ?: '-' }}</td>
            <td class="text-nowrap">{{ $offer->offer_date ?: '-' }}</td>
            <td class="text-nowrap">{{ $offer->option_date ?: '-' }}</td>
            <td>{{ $offer->tour_name ?: '-' }}</td>
            <td class="actions-cell">
                {{-- A policy belongs to a hotel offer: open the offer, never the tour (its delete would remove the whole tour) --}}
                <a href="{{ route('offers', $offer->package_id) }}" class="btn btn-sm btn-warning" title="View offer" aria-label="View offer">
                    <i class="ti ti-eye"></i>
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="13" class="text-center text-secondary py-4">
                {{ $search !== '' ? 'No cancellation policies match your search.' : 'No cancellation policies for upcoming stays.' }}
            </td>
        </tr>
    @endforelse
    </tbody></table></div>
    @if($policies->hasPages())
        <div class="mt-3">{{ $policies->links() }}</div>
    @endif
</div></div></section>
@endsection
@push('styles')
<style>
    .offer-page .toolbar-row { gap: .75rem; }
    .offer-page .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .offer-page table { background: #fff; min-width: 980px; }
    .offer-page th { white-space: nowrap; }
    .offer-page td { vertical-align: middle; }
    .offer-page .policy-text { max-width: 260px; white-space: normal; line-height: 1.35; }
</style>
@endpush
