@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
        ['title' => 'Current Offers', 'sub_title' => 'Offer List',
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
                            <input type="text" id="current-offers-search" class="form-control" placeholder="Search current offers..." onkeyup="filterTable('current-offers-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('current-offers-table', 'current_offers_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="current-offers-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%; table-layout: fixed'>
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'current-offers-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'current-offers-table')">{!!trans('Tour Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'current-offers-table')">{!!trans('City')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'current-offers-table')">{!!trans('Status')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'current-offers-table')">{!!trans('Departure Date')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'current-offers-table')">{!!trans('Return Date')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'current-offers-table')">{!!trans('PAX')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(7, 'current-offers-table')">{!!trans('Created At')!!} <i class="fa fa-sort"></i></th>
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
                                <td colspan="9" class="text-center">No current offers found</td>
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
<script src="{{ asset('js/loadtemplate.js') }}"></script>

<script>
$(document).ready(function () {
    // Initialize Bootstrap table
    initializeBootstrapTable('current-offers-table');

    // Tour Clone Modal Submission Confirmation
    $('#tour-clone-modal-form').submit(function (e) {
        if (!confirm('Are you sure? Do you really want to submit the form?')) {
            e.preventDefault();
            location.reload();
        }
    });

    // AJAX for dropdown (existing)
    function dropdown_ajax(tour_id, offer_date, option_date) {
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')}
        });

        $.ajax({
            type: "POST",
            url: `/offer/${tour_id}/days_dropdown`,
            data: { offer_date: offer_date, option_date: option_date },
            success: function(result) {
                if (result[0] === "") {
                    $("#service_div").show();
                    $("#services").hide();
                    $("#service_div").html(`<h3> Please Add Service in the tour </h3>`);
                } else {
                    $("#service_div").hide();
                    $("#services").show();
                    $("#services").html(result);
                }
            },
            error: function(result) { console.log(result); }
        });
    }

    setTimeout(function () {
        $('.tour_dropdown').on('change', function(){
            dropdown_ajax($(this).val(), $('#offer_date').val(), $('#option_date').val());
        });

        $('.change-tour-button').show().on('click', function(){ 
            let id = $(this).data('id');
            let tour_id = $(this).data('tour');
            let offer_date = $(this).data('offer_date');
            let option_date = $(this).data('option_date');

            dropdown_ajax(tour_id, offer_date, option_date);
            $('#offer_date').val(offer_date);
            $('#option_date').val(option_date);
            $('#tour_id').trigger('change');
            $('#tour-clone-modal-form').attr('action', '/offer/' + id + '/assign_to_tour');
        });
    }, 5000);

});
</script>
@endpush