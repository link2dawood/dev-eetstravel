@extends('scaffold-interface.layouts.tabler-app')
@section('title','Cruises')

@section('content')
<x-ui.page-header
    title="Cruises"
    description="Cruise suppliers and scheduled routes."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Cruises'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('cruises.create'))
            <x-ui.button as="a" href="{{ route('cruises.create') }}" icon="plus">
                {!! trans('main.New') ?? 'New' !!} cruise
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if(Session::has('message'))
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

{{-- Toolbar: search + export --}}
<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text"
                   id="cruise-search"
                   onkeyup="filterTable('cruise-table', this.value); filterCards('cruises-cards', this.value);"
                   placeholder="Search cruises on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('cruise-table', 'cruises_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($cruises) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="ship"
            title="No cruises yet"
            message="Add your first cruise to start building tour packages.">
            @if(Auth::user()->can('cruises.create'))
                <x-ui.button as="a" href="{{ route('cruises.create') }}" icon="plus">Add a cruise</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- Desktop table --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="cruise-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'cruise-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'cruise-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Name') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'cruise-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Datefrom') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'cruise-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Dateto') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'cruise-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.CountryFrom') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'cruise-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Cityfrom') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($cruises as $cruise)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $cruise->id }}</td>
                            <td class="px-4 py-3">
                                @if(Auth::user()->can('cruises.show'))
                                    <a href="{{ route('cruises.show', ['cruise' => $cruise->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $cruise->name ?? '—' }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $cruise->name ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $cruise->date_from ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $cruise->date_to ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $cruise->country_from_name ?? ($cruise->country_from ?? '—') }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $cruise->city_from_name ?? ($cruise->city_from ?? '—') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('cruises.show'))
                                        <a href="{{ route('cruises.show', ['cruise' => $cruise->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('cruises.edit'))
                                        <a href="{{ route('cruises.edit', ['cruise' => $cruise->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('cruise.destroy'))
                                        <button type="button"
                                                onclick="cruiseDeleteConfirm({{ $cruise->id }}, @js($cruise->name))"
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
    <div id="cruises-cards" class="md:hidden space-y-3">
        @foreach($cruises as $cruise)
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('cruises.show'))
                            <a href="{{ route('cruises.show', ['cruise' => $cruise->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $cruise->name ?? '—' }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $cruise->name ?? '—' }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $cruise->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('cruises.edit'))
                            <a href="{{ route('cruises.edit', ['cruise' => $cruise->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        @if(Auth::user()->can('cruise.destroy'))
                            <button type="button"
                                    onclick="cruiseDeleteConfirm({{ $cruise->id }}, @js($cruise->name))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                    aria-label="Delete">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        @endif
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if(($cruise->country_from_name ?? $cruise->country_from ?? null) || ($cruise->city_from_name ?? $cruise->city_from ?? null))
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">From</dt>
                            <dd class="text-slate-700">
                                {{ trim((($cruise->city_from_name ?? $cruise->city_from ?? '') . (($cruise->city_from_name ?? $cruise->city_from ?? '') && ($cruise->country_from_name ?? $cruise->country_from ?? '') ? ', ' : '') . ($cruise->country_from_name ?? $cruise->country_from ?? ''))) ?: '—' }}
                            </dd>
                        </div>
                    @endif
                    @if($cruise->date_from)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Date from</dt>
                            <dd class="text-slate-700">{{ $cruise->date_from }}</dd>
                        </div>
                    @endif
                    @if($cruise->date_to)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Date to</dt>
                            <dd class="text-slate-700">{{ $cruise->date_to }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>

    {{-- Pagination strip --}}
    @if($cruises->hasPages())
        <nav class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <p class="text-xs text-slate-500">
                Showing <span class="font-medium text-slate-700">{{ $cruises->firstItem() }}</span>
                to <span class="font-medium text-slate-700">{{ $cruises->lastItem() }}</span>
                of <span class="font-medium text-slate-700">{{ $cruises->total() }}</span> cruises
            </p>
            <div class="flex items-center gap-1 flex-wrap">
                @if($cruises->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </span>
                @else
                    <a href="{{ $cruises->previousPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </a>
                @endif

                @foreach($cruises->onEachSide(1)->getUrlRange(max(1, $cruises->currentPage() - 2), min($cruises->lastPage(), $cruises->currentPage() + 2)) as $page => $url)
                    @if($page == $cruises->currentPage())
                        <span class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded bg-primary-600 px-3 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($cruises->hasMorePages())
                    <a href="{{ $cruises->nextPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
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
<div id="cruiseDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="cruiseDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete cruise?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="cruiseDeleteName" class="text-slate-700"></strong>.
                        This will remove the cruise from your supplier catalog. Existing tours that reference it stay intact.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="cruiseDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="cruiseDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
                    <x-ui.icon name="trash-2" size="sm" />
                    Delete cruise
                </a>
            </div>
        </div>
    </div>
</div>

<span id="service-name" hidden data-service-name='Cruises'></span>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('cruise-table');
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

    window.cruiseDeleteConfirm = function (id, name) {
        var modal = document.getElementById('cruiseDeleteModal');
        var label = document.getElementById('cruiseDeleteName');
        var btn   = document.getElementById('cruiseDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('cruise #' + id);
        btn.href = '{{ url("cruises") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.cruiseDeleteCancel = function () {
        var modal = document.getElementById('cruiseDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.cruiseDeleteCancel();
    });
</script>
@endpush
