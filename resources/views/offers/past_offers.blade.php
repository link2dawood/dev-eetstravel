@extends('scaffold-interface.layouts.tabler-app')
@section('title','Past Offers')
@section('content')
@include('layouts.title', ['title' => 'Past Offers', 'sub_title' => 'Offer List', 'breadcrumbs' => [['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')], ['title' => 'Past Offers', 'icon' => null, 'route' => null]]])
@php
    // Column header that toggles server-side sorting over all offers, not just this page
    $sortLink = function ($column, $label) use ($sort, $dir) {
        $nextDir = ($sort === $column && $dir === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($dir === 'asc' ? 'ti-sort-ascending' : 'ti-sort-descending') : 'ti-arrows-sort';
        $url = request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir, 'page' => null]);
        return '<a href="' . e($url) . '" class="text-reset text-decoration-none">' . e($label) . ' <i class="ti ' . $icon . '"></i></a>';
    };
@endphp
<section class="content offer-page"><div class="box box-primary"><div class="box-body">
    <div class="row align-items-center mb-3 toolbar-row">
        <div class="col-md-6">
            <form method="GET" action="{{ route('past_offers.index') }}" class="d-flex gap-2">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="dir" value="{{ $dir }}">
                <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Search by tour name, external name or ID" aria-label="Search past offers">
                <button type="submit" class="btn btn-primary">Search</button>
                @if($search !== '')
                    <a href="{{ route('past_offers.index') }}" class="btn btn-secondary">Clear</a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}" class="btn btn-success btn-sm"><i class="ti ti-download"></i> Export CSV</a>
        </div>
    </div>
    <p class="text-secondary small mb-2">Tours that have ended, plus cancelled and finished offers, latest return date first.</p>
    <div class="table-responsive"><table id="past-offers-table" class="table table-striped table-bordered table-hover"><thead><tr>
        <th>{!! $sortLink('id', 'ID') !!}</th>
        <th>{!! $sortLink('name', 'Tour Name') !!}</th>
        <th>City</th>
        <th>Status</th>
        <th>{!! $sortLink('departure_date', 'Departure Date') !!}</th>
        <th>{!! $sortLink('retirement_date', 'Return Date') !!}</th>
        <th>PAX</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr></thead><tbody>
    @forelse($tours as $tour)
        @php
            $status = $tour->getRelationValue('status'); // eager-loaded; the column shares the name
            $statusName = $status->name ?? null;
            // The color goes into a style attribute; accept only hex colors
            $statusColor = preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) ($status->color ?? '')) ? $status->color : '#6c757d';
        @endphp
        <tr>
            <td>{{ $tour->id }}</td>
            <td data-delete-label>{{ $tour->name }}</td>
            <td>{{ $cityNames->get($tour->id) ?: '-' }}</td>
            <td><span class="badge" style="background-color: {{ $statusColor }}20; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}40;">{{ $statusName ?: '-' }}</span></td>
            <td>{{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '-' }}</td>
            <td>{{ $tour->retirement_date ? \Carbon\Carbon::parse($tour->retirement_date)->format('Y-m-d') : '-' }}</td>
            <td>{{ $tour->pax ?? '-' }}</td>
            <td>{{ $tour->created_at ? \Carbon\Carbon::parse($tour->created_at)->format('Y-m-d H:i') : '-' }}</td>
            <td class="actions-cell">@include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])</td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center text-secondary py-4">
                {{ $search !== '' ? 'No past offers match your search.' : 'No past offers.' }}
            </td>
        </tr>
    @endforelse
    </tbody></table></div>
    @if($tours->hasPages())
        <div class="mt-3">{{ $tours->links() }}</div>
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
    .offer-page .actions-cell .btn-list { display: inline-flex; flex-wrap: nowrap; justify-content: center; gap: .35rem; }
</style>
@endpush
