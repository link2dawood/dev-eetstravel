@extends('scaffold-interface.layouts.tabler-app')
@section('title','Past offers')

@section('content')
<x-ui.page-header
    title="Past offers"
    description="Archived tour offers — no longer active."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Past offers'],
    ]"
/>

@if($tours->isEmpty())
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="archive" title="No past offers" message="When offers expire or are archived they will appear here." />
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-slate-200">
            <div class="w-full sm:max-w-xs">
                {{-- This client-side search only filters the visible page. --}}
                <input type="text" id="past-offers-search" placeholder="Search past offers…"
                       onkeyup="filterTable('past-offers-table', this.value)"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <button type="button" onclick="exportTableToCSV('past-offers-table', 'past_offers_export.csv')"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50 shadow-subtle">
                    <x-ui.icon name="download" size="sm" /> Export CSV
                </button>
            </div>
        </div>
        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table id="past-offers-table" class="min-w-full divide-y divide-slate-200 text-sm bootstrap-table" style="background:#fff; min-width: 900px;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'past-offers-table')">ID <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'past-offers-table')">{!! trans('Tour Name') !!} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'past-offers-table')">{!! trans('City') !!} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'past-offers-table')">{!! trans('Status') !!} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'past-offers-table')">{!! trans('Departure Date') !!} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'past-offers-table')">{!! trans('Return Date') !!} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'past-offers-table')">{!! trans('PAX') !!} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(7, 'past-offers-table')">{!! trans('Created At') !!} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
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

        {{-- Laravel server-side pagination --}}
        <div class="flex justify-end px-4 py-3 border-t border-slate-200">
            {{ $tours->withQueryString()->links() }}
        </div>
    </div>
@endif

@include('component.delete_modal_simple')
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('past-offers-table');
        }
    });
</script>
@endpush
