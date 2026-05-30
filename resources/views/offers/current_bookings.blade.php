@extends('scaffold-interface.layouts.tabler-app')
@section('title','Current bookings')

@section('content')
<x-ui.page-header
    title="Current bookings"
    description="Hotel bookings linked to active tours, with payment and cancellation status."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Current bookings'],
    ]"
/>

@if(empty($processedBookings) || count($processedBookings) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="bed" title="No current bookings" message="When a hotel booking is confirmed for an active tour, it will appear here." />
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-slate-200">
            <div class="w-full sm:max-w-xs">
                <input type="text" id="current-bookings-search" placeholder="Search current bookings…"
                       onkeyup="filterTable('current-bookings-table', this.value)"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <button type="button" onclick="exportTableToCSV('current-bookings-table', 'current_bookings_export.csv')"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50 shadow-subtle">
                    <x-ui.icon name="download" size="sm" /> Export CSV
                </button>
            </div>
        </div>
        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table id="current-bookings-table" class="min-w-full divide-y divide-slate-200 text-sm bootstrap-table" style="background:#fff; min-width: 1000px;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" style="width: 60px;" onclick="sortTable(0, 'current-bookings-table')">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'current-bookings-table')">{!! trans('Tour') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'current-bookings-table')">{!! trans('Hotel Name') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'current-bookings-table')">{!! trans('City') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'current-bookings-table')">{!! trans('Status') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'current-bookings-table')">{!! trans('Date of Stay') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" style="width: 60px;" onclick="sortTable(6, 'current-bookings-table')">SIN <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" style="width: 60px;" onclick="sortTable(7, 'current-bookings-table')">DOU <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" style="width: 60px;" onclick="sortTable(8, 'current-bookings-table')">TRI <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" style="width: 150px;" onclick="sortTable(9, 'current-bookings-table')">{!! trans('Cancellation Policy') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" style="width: 200px;" onclick="sortTable(10, 'current-bookings-table')">{!! trans('Payments Made') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 text-right actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($processedBookings as $booking)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $booking->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900" data-delete-label>{{ $booking->tour_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $booking->hotel_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $booking->city_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $booking->status_name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $booking->stay_date }}</td>
                            <td class="px-4 py-3 text-center text-slate-500">-</td>
                            <td class="px-4 py-3 text-center text-slate-500">-</td>
                            <td class="px-4 py-3 text-center text-slate-500">-</td>
                            <td class="px-4 py-3 text-xs text-slate-700">
                                <div class="max-w-[150px] whitespace-normal break-words leading-snug">{{ $booking->cancel_policy }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-700">
                                <div class="max-w-[200px] whitespace-normal break-words leading-snug">{{ $booking->payment_policy }}</div>
                            </td>
                            <td class="px-4 py-3" onclick="event.stopPropagation();">
                                @if(!empty($booking->model))
                                    <div class="flex items-center justify-end gap-1">
                                        @include('component.action_buttons', ['item' => $booking->model, 'routePrefix' => 'tour_package'])
                                    </div>
                                @else
                                    <span class="block text-right text-xs text-slate-400">No booking record linked</span>
                                @endif
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
<script>
$(document).ready(function () {
    if (typeof initializeBootstrapTable === 'function') {
        initializeBootstrapTable('current-bookings-table');
    }
});
</script>
@endpush
