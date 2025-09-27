@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
@include('layouts.title',
       ['title' => 'Past Offers', 'sub_title' => 'Offer List',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Currencies', 'icon' => null, 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <br><br>
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="past-offers-search" class="form-control" placeholder="Search past offers..." onkeyup="filterTable('past-offers-table', this.value)">
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success btn-sm" onclick="exportTableToCSV('past-offers-table', 'past_offers_export.csv')">
                            <i class="fa fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="past-offers-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%; table-layout: fixed'>
                    <thead>
                        <tr>
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
                            <td>
                                @include('component.action_buttons', [
                                    'show_route' => route('tour.show', ['tour' => $tour->id]),
                                    'edit_route' => route('tour.edit', ['tour' => $tour->id]),
                                    'delete_route' => route('tour.destroy', $tour->id),
                                    'model' => $tour
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
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('past-offers-table');
    });
</script>
@endpush

