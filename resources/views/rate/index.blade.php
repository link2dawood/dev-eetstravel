@extends('scaffold-interface.layouts.tabler-app')
@section('title','Rates')

@section('content')
<x-ui.page-header
    title="Rates"
    description="Rate codes used across tours, hotels, and accounting."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Rates'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('rate.create'))
            <x-ui.button as="a" href="{{ route('rate.create') }}" icon="plus">New rate</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if(Session::has('message'))
    <div class="mb-4 flex items-start gap-3 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <x-ui.icon name="alert-octagon" class="mt-0.5 text-danger-600" />
        <div class="flex-1">{{ Session::get('message') }}</div>
    </div>
@endif

@if(count($rates) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="receipt" title="No rates yet" message="Add your first rate code to start tagging tours and bookings.">
            @if(Auth::user()->can('rate.create'))
                <x-ui.button as="a" href="{{ route('rate.create') }}" icon="plus">New rate</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-slate-200">
            <div class="w-full sm:max-w-xs">
                <input type="text" id="rate-search" placeholder="{{ trans('main.Search') ?? 'Search rates…' }}"
                       onkeyup="filterTable('rate-table', this.value)"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <button type="button" onclick="exportTableToCSV('rate-table', 'rates_export.csv')"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50 shadow-subtle">
                    <x-ui.icon name="download" size="sm" /> Export CSV
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table id="rate-table" class="min-w-full divide-y divide-slate-200 text-sm bootstrap-table" style="background:#fff">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'rate-table')">ID <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'rate-table')">{{ trans('main.Name') }} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'rate-table')">{{ trans('main.Mark') }} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'rate-table')">{{ trans('main.Ratetype') }} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'rate-table')">{{ trans('main.Sortorder') }} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rates as $rate)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $rate->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900" data-delete-label>{{ $rate->name ?? '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $rate->mark ?? '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $rate->rate_type ?? '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $rate->sort_order ?? '' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @include('component.action_buttons', ['item' => $rate, 'routePrefix' => 'rate'])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    $(document).ready(function () {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('rate-table');
        }
    });
</script>
@endpush

@include('component.delete_modal_simple')
