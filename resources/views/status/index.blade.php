@extends('scaffold-interface.layouts.tabler-app')
@section('title','Statuses')

@section('content')
<x-ui.page-header
    title="Statuses"
    description="Tour, bus, and rate status labels used across the system."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Statuses'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('status.create'))
            <x-ui.button as="a" href="{{ route('status.create') }}" icon="plus">New status</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<div id="errors_message" class="mb-4 hidden rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700"></div>

@if(count($status) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="circle-check" title="No statuses yet" message="Create your first status label to categorise tours, buses, or rates.">
            @if(Auth::user()->can('status.create'))
                <x-ui.button as="a" href="{{ route('status.create') }}" icon="plus">New status</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-slate-200">
            <div class="w-full sm:max-w-xs">
                <input type="text" id="status-search" placeholder="{{ trans('main.Search') ?? 'Search statuses…' }}"
                       onkeyup="filterTable('status-table', this.value)"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <button type="button" onclick="exportTableToCSV('status-table', 'statuses_export.csv')"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50 shadow-subtle">
                    <x-ui.icon name="download" size="sm" /> Export CSV
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table id="status-table" class="min-w-full divide-y divide-slate-200 text-sm bootstrap-table" style="background:#fff">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'status-table')">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'status-table')">{!! trans('main.Name') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'status-table')">{!! trans('main.Type') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'status-table')">{!! trans('main.SortOrder') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($status as $statusItem)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $statusItem->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900" data-delete-label>{{ $statusItem->name ?? '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $statusItem->status_type ?? '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $statusItem->sort_order ?? '' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @include('component.action_buttons', ['item' => $statusItem, 'routePrefix' => 'status'])
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
            initializeBootstrapTable('status-table');
        }
    });
</script>
@endpush

@include('component.delete_modal_simple')
