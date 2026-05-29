@extends('scaffold-interface.layouts.tabler-app')
@section('title','Flights')

@section('content')
<x-ui.page-header
    title="Flights"
    description="Flight suppliers and schedules."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Flights'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('flights.create'))
            <x-ui.button as="a" href="{{ route('flights.create') }}" icon="plus">
                {!! trans('main.New') ?? 'New' !!} flight
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if (Session::has('message'))
    <div class="mb-4 flex items-start gap-3 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <x-ui.icon name="alert-circle" class="mt-0.5 text-danger-600" />
        <div class="flex-1">{{ Session::get('message') }}</div>
    </div>
@endif

@if(session('export_all'))
    <div class="mb-4 flex items-start gap-3 rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700">
        <x-ui.icon name="info" class="mt-0.5 text-info-600" />
        <div class="flex-1">{{ session('export_all') }}</div>
    </div>
@endif

{{-- Toolbar: search + export. --}}
<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text"
                   id="flights-search"
                   onkeyup="filterTable('flights-table', this.value); filterCards('flights-cards', this.value);"
                   placeholder="Search flights on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('flights-table', 'flights_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($flights) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="plane"
            title="No flights found"
            message="Get started by adding your first flight.">
            @if(Auth::user()->can('flights.create'))
                <x-ui.button as="a" href="{{ route('flights.create') }}" icon="plus">Add a flight</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- Desktop table --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="flights-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'flights-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'flights-table')">
                            <span class="inline-flex items-center gap-1">Name <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'flights-table')">
                            <span class="inline-flex items-center gap-1">Date From <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'flights-table')">
                            <span class="inline-flex items-center gap-1">From <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'flights-table')">
                            <span class="inline-flex items-center gap-1">To <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($flights as $flight)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $flight->id }}</td>
                            <td class="px-4 py-3">
                                @if(Auth::user()->can('flights.show'))
                                    <a href="{{ route('flights.show', ['flight' => $flight->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $flight->name ?? '—' }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $flight->name ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $flight->date_from ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $flight->city_from ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $flight->city_to ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('flights.show'))
                                        <a href="{{ route('flights.show', ['flight' => $flight->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('flights.edit'))
                                        <a href="{{ route('flights.edit', ['flight' => $flight->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('flight.destroy'))
                                        <button type="button"
                                                onclick="flightDeleteConfirm({{ $flight->id }}, @js($flight->name))"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                                title="Delete">
                                            <x-ui.icon name="trash-2" size="sm" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile card list --}}
    <div id="flights-cards" class="md:hidden space-y-3">
        @foreach($flights as $flight)
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('flights.show'))
                            <a href="{{ route('flights.show', ['flight' => $flight->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $flight->name ?? '—' }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $flight->name ?? '—' }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $flight->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('flights.edit'))
                            <a href="{{ route('flights.edit', ['flight' => $flight->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        @if(Auth::user()->can('flight.destroy'))
                            <button type="button"
                                    onclick="flightDeleteConfirm({{ $flight->id }}, @js($flight->name))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                    aria-label="Delete">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        @endif
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if($flight->date_from)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Date From</dt>
                            <dd class="text-slate-700">{{ $flight->date_from }}</dd>
                        </div>
                    @endif
                    @if($flight->city_from || $flight->city_to)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Route</dt>
                            <dd class="text-slate-700">
                                {{ trim(($flight->city_from ?? '') . (($flight->city_from && $flight->city_to) ? ' → ' : '') . ($flight->city_to ?? '')) ?: '—' }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>

    {{-- Pagination strip --}}
    @if(method_exists($flights, 'hasPages') && $flights->hasPages())
        <nav class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <p class="text-xs text-slate-500">
                Showing <span class="font-medium text-slate-700">{{ $flights->firstItem() }}</span>
                to <span class="font-medium text-slate-700">{{ $flights->lastItem() }}</span>
                of <span class="font-medium text-slate-700">{{ $flights->total() }}</span> flights
            </p>
            <div class="flex items-center gap-1 flex-wrap">
                @if($flights->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </span>
                @else
                    <a href="{{ $flights->previousPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </a>
                @endif

                @foreach($flights->onEachSide(1)->getUrlRange(max(1, $flights->currentPage() - 2), min($flights->lastPage(), $flights->currentPage() + 2)) as $page => $url)
                    @if($page == $flights->currentPage())
                        <span class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded bg-primary-600 px-3 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($flights->hasMorePages())
                    <a href="{{ $flights->nextPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        Next<x-ui.icon name="chevron-right" size="xs" />
                    </a>
                @else
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        Next<x-ui.icon name="chevron-right" size="xs" />
                    </span>
                @endif
            </div>
        </nav>
    @endif

@endif

{{-- Delete confirmation modal (Tailwind, vanilla JS) --}}
<div id="flightDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="flightDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete flight?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="flightDeleteName" class="text-slate-700"></strong>.
                        This will remove the flight from your supplier catalog.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="flightDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="flightDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
                    <x-ui.icon name="trash-2" size="sm" />
                    Delete flight
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('flights-table');
        }
    });

    window.filterCards = window.filterCards || function (containerId, value) {
        var c = document.getElementById(containerId);
        if (!c) return;
        var q = (value || '').toLowerCase();
        c.querySelectorAll('[data-card-row]').forEach(function (row) {
            var name = (row.querySelector('[data-card-name]')?.textContent || '').toLowerCase();
            row.style.display = (!q || name.indexOf(q) !== -1) ? '' : 'none';
        });
    };

    window.flightDeleteConfirm = function (id, name) {
        var modal = document.getElementById('flightDeleteModal');
        var label = document.getElementById('flightDeleteName');
        var btn   = document.getElementById('flightDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('flight #' + id);
        btn.href = '{{ url("flights") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.flightDeleteCancel = function () {
        var modal = document.getElementById('flightDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.flightDeleteCancel();
    });
</script>
@endpush
