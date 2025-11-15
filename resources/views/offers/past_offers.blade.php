@extends('scaffold-interface.layouts.tabler-app')
@section('title','Index')
@section('content')
@include('layouts.title',
       ['title' => 'Past Offers', 'sub_title' => 'Offer List',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Past Offers', 'icon' => null, 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <br><br>
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        {{-- Note: This client-side search will only search the 10 records currently visible on this page --}}
                        <input type="text" id="past-offers-search" class="form-control" placeholder="Search past offers..." onkeyup="filterTable('past-offers-table', this.value)">
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success btn-sm" onclick="exportTableToCSV('past-offers-table', 'past_offers_export.csv')">
                            <i class="fa fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table id="past-offers-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%; min-width: 900px;'>
                    <thead>
                        <tr>
                            {{-- Note: These client-side sort functions will only sort the 10 visible records --}}
                            <th onclick="sortTable(0, 'past-offers-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'past-offers-table')">{!!trans('Tour Name')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'past-offers-table')">{!!trans('City')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'past-offers-table')">{!!trans('Status')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'past-offers-table')">{!!trans('Departure Date')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'past-offers-table')">{!!trans('Return Date')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'past-offers-table')">{!!trans('PAX')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(7, 'past-offers-table')">{!!trans('Created At')!!} <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tours as $tour)
                        <tr>
                            <td>{{ $tour->id }}</td>
                            <td>{{ $tour->name }}</td>
                            <td>{{ $tour->city ? $tour->city->name : '' }}</td>
                            <td>
                                <span class="badge badge-primary" style="background-color: {{ $tour->getStatusColor() }}">
                                    {{ $tour->getStatusName() }}
                                </span>
                            </td>
                            <td>{{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '' }}</td>
                            <td>{{ $tour->retirement_date ? \Carbon\Carbon::parse($tour->retirement_date)->format('Y-m-d') : '' }}</td>
                            <td>{{ $tour->pax }}</td>
                            <td>{{ $tour->created_at ? $tour->created_at->format('Y-m-d H:i') : '' }}</td>
                            <td onclick="event.stopPropagation();">
                                @include('component.action_buttons', [
                                    'item' => $tour,
                                    'routePrefix' => 'tour'
                                ])
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No past offers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-end mt-4">
                {{ $tours->withQueryString()->links() }}
            </div>

        </div>
    </div>
</section>
@include('scaffold-interface.dashboard.components.delete-modal')
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // If your initializeBootstrapTable function interferes with standard Laravel pagination
        // you might need to disable it, but try keeping it first.
        initializeBootstrapTable('past-offers-table');
    });
</script>
@endpush