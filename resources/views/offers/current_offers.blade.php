@extends('scaffold-interface.layouts.tabler-app')
@section('title','Current offers')

@section('content')
<x-ui.page-header
    title="Current offers"
    description="Active tour offers awaiting confirmation."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Current offers'],
    ]"
/>

@if($tours->isEmpty() ?? count($tours) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="receipt" title="No current offers" message="When a tour is offered to a client and still in flight, it will appear here." />
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-slate-200">
            <div class="w-full sm:max-w-xs">
                <input type="text" id="current-offers-search" placeholder="Search current offers…"
                       onkeyup="filterTable('current-offers-table', this.value)"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <button type="button" onclick="exportTableToCSV('current-offers-table', 'current_offers_export.csv')"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50 shadow-subtle">
                    <x-ui.icon name="download" size="sm" /> Export CSV
                </button>
            </div>
        </div>
        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table id="current-offers-table" class="min-w-full divide-y divide-slate-200 text-sm bootstrap-table" style="background:#fff; min-width: 900px;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'current-offers-table')">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'current-offers-table')">{!! trans('Tour Name') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'current-offers-table')">{!! trans('City') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'current-offers-table')">{!! trans('Status') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'current-offers-table')">{!! trans('Departure Date') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'current-offers-table')">{!! trans('Return Date') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'current-offers-table')">{!! trans('PAX') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(7, 'current-offers-table')">{!! trans('Created At') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 text-right actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tours as $tour)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $tour->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900" data-delete-label>{{ $tour->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $tour->city ? $tour->city->name : '' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium text-white" style="background-color: {{ $tour->getStatusColor() }}">
                                    {{ $tour->getStatusName() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $tour->retirement_date ? \Carbon\Carbon::parse($tour->retirement_date)->format('Y-m-d') : '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $tour->pax }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $tour->created_at ? $tour->created_at->format('Y-m-d H:i') : '' }}</td>
                            <td class="px-4 py-3" onclick="event.stopPropagation();">
                                <div class="flex items-center justify-end gap-1">
                                    @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@include('component.delete_modal_simple')
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script src="{{ asset('js/loadtemplate.js') }}"></script>
<script>
$(document).ready(function () {
    if (typeof initializeBootstrapTable === 'function') {
        initializeBootstrapTable('current-offers-table');
    }

    // Tour-clone modal: confirm submission
    $('#tour-clone-modal-form').submit(function (e) {
        if (!confirm('Are you sure? Do you really want to submit the form?')) {
            e.preventDefault();
            location.reload();
        }
    });

    // AJAX for tour-day dropdown (clone tour flow)
    function dropdown_ajax(tour_id, offer_date, option_date) {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') } });
        $.ajax({
            type: "POST",
            url: `/offer/${tour_id}/days_dropdown`,
            data: { offer_date: offer_date, option_date: option_date },
            success: function (result) {
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
            error: function (result) { console.log(result); }
        });
    }

    $('.tour_dropdown').on('change', function () {
        dropdown_ajax($(this).val(), $('#offer_date').val(), $('#option_date').val());
    });

    $('.change-tour-button').show().on('click', function () {
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
});
</script>
@endpush
