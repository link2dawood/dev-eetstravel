@extends('scaffold-interface.layouts.tabler-app')
@section('title','Buses')

@section('content')
<x-ui.page-header
    title="Buses"
    description="Fleet management — buses and transport."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Buses'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('bus.create'))
            <x-ui.button as="a" href="{{ route('bus.create') }}" icon="plus">
                {!! trans('main.New') ?? 'New' !!} bus
            </x-ui.button>
        @endif
        <x-ui.button as="a" href="{{ route('bus_calendar') }}" variant="secondary" icon="calendar">
            Calendar
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(Session::has('message'))
    <div class="mb-4 flex items-start gap-3 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <x-ui.icon name="alert-circle" class="mt-0.5 text-danger-600" />
        <div class="flex-1">{{ Session::get('message') }}</div>
    </div>
@endif

{{-- Toolbar: search + export --}}
<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text"
                   id="bus-search"
                   onkeyup="filterTable('bus-table', this.value); filterCards('bus-cards', this.value);"
                   placeholder="Search buses on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('bus-table', 'buses_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($buses) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="bus"
            title="No buses yet"
            message="Add your first bus to start managing your fleet.">
            @if(Auth::user()->can('bus.create'))
                <x-ui.button as="a" href="{{ route('bus.create') }}" icon="plus">Add a bus</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- Desktop table --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="bus-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'bus-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'bus-table')">
                            <span class="inline-flex items-center gap-1">License Plate <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'bus-table')">
                            <span class="inline-flex items-center gap-1">Bus Company <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'bus-table')">
                            <span class="inline-flex items-center gap-1">Seats <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'bus-table')">
                            <span class="inline-flex items-center gap-1">Type <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($buses as $bus)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $bus->id }}</td>
                            <td class="px-4 py-3">
                                @if(Auth::user()->can('bus.show'))
                                    <a href="{{ route('bus.show', ['bus' => $bus->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $bus->license_plate ?? '—' }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $bus->license_plate ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $bus->transfer_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $bus->seats ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $bus->type ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('bus.show'))
                                        <a href="{{ route('bus.show', ['bus' => $bus->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('bus.edit'))
                                        <a href="{{ route('bus.edit', ['bus' => $bus->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('bus.destroy'))
                                        <button type="button"
                                                onclick="busDeleteConfirm({{ $bus->id }}, @js($bus->license_plate ?? $bus->name))"
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
    <div id="bus-cards" class="md:hidden space-y-3">
        @foreach($buses as $bus)
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('bus.show'))
                            <a href="{{ route('bus.show', ['bus' => $bus->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $bus->license_plate ?? $bus->name ?? '—' }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $bus->license_plate ?? $bus->name ?? '—' }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $bus->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('bus.edit'))
                            <a href="{{ route('bus.edit', ['bus' => $bus->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        @if(Auth::user()->can('bus.destroy'))
                            <button type="button"
                                    onclick="busDeleteConfirm({{ $bus->id }}, @js($bus->license_plate ?? $bus->name))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                    aria-label="Delete">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        @endif
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if($bus->transfer_name)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Bus Company</dt>
                            <dd class="text-slate-700">{{ $bus->transfer_name }}</dd>
                        </div>
                    @endif
                    @if($bus->seats)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Seats</dt>
                            <dd class="text-slate-700">{{ $bus->seats }}</dd>
                        </div>
                    @endif
                    @if($bus->type)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Type</dt>
                            <dd class="text-slate-700">{{ $bus->type }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>

    {{-- Pagination strip --}}
    @if($buses->hasPages())
        <nav class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <p class="text-xs text-slate-500">
                Showing <span class="font-medium text-slate-700">{{ $buses->firstItem() }}</span>
                to <span class="font-medium text-slate-700">{{ $buses->lastItem() }}</span>
                of <span class="font-medium text-slate-700">{{ $buses->total() }}</span> buses
            </p>
            <div class="flex items-center gap-1 flex-wrap">
                @if($buses->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </span>
                @else
                    <a href="{{ $buses->previousPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </a>
                @endif

                @foreach($buses->onEachSide(1)->getUrlRange(max(1, $buses->currentPage() - 2), min($buses->lastPage(), $buses->currentPage() + 2)) as $page => $url)
                    @if($page == $buses->currentPage())
                        <span class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded bg-primary-600 px-3 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($buses->hasMorePages())
                    <a href="{{ $buses->nextPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
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

{{-- Delete confirmation modal --}}
<div id="busDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="busDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete bus?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="busDeleteName" class="text-slate-700"></strong>.
                        This will remove the bus from your fleet catalog.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="busDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="busDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
                    <x-ui.icon name="trash-2" size="sm" />
                    Delete bus
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
            initializeBootstrapTable('bus-table');
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

    window.busDeleteConfirm = function (id, name) {
        var modal = document.getElementById('busDeleteModal');
        var label = document.getElementById('busDeleteName');
        var btn   = document.getElementById('busDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('bus #' + id);
        btn.href = '{{ url("bus") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.busDeleteCancel = function () {
        var modal = document.getElementById('busDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.busDeleteCancel();
    });
</script>
@endpush
