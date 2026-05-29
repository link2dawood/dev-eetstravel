@extends('scaffold-interface.layouts.tabler-app')
@section('title','Quotations')

@section('content')
<x-ui.page-header
    title="Tour Quotations"
    description="Quotation drafts and go-ahead tours."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Quotations'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('tour.create'))
            <x-ui.button as="a" href="{{ route('tour.create', ['is_quotation' => 1]) }}" icon="plus">
                New quotation
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

{{-- ============================================================ --}}
{{-- Segmented control: Quotations vs Go-ahead tours.              --}}
{{-- Replaces the legacy CSS pill-toggle (uses ::before/::after    --}}
{{-- pseudo-elements + a fake checkbox) with a clean Tailwind      --}}
{{-- button group. quotationViewSwitch() swaps which section is    --}}
{{-- visible and which button is "active".                          --}}
{{-- ============================================================ --}}
<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="inline-flex rounded-md border border-slate-200 bg-slate-50 p-1">
        <button type="button"
                onclick="quotationViewSwitch('quotations')"
                data-view-btn="quotations"
                class="inline-flex h-8 items-center gap-1.5 rounded px-3 text-sm font-medium transition-colors">
            <x-ui.icon name="file-text" size="sm" />
            Quotations
            <span class="ml-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-slate-100 px-1.5 text-xs font-medium text-slate-600">{{ $quotations->count() }}</span>
        </button>
        <button type="button"
                onclick="quotationViewSwitch('goahead')"
                data-view-btn="goahead"
                class="inline-flex h-8 items-center gap-1.5 rounded px-3 text-sm font-medium transition-colors">
            <x-ui.icon name="check-circle" size="sm" />
            Go-ahead tours
            <span class="ml-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-slate-100 px-1.5 text-xs font-medium text-slate-600">{{ $goAheadTours->count() }}</span>
        </button>
    </div>
</div>

{{-- ============================================================ --}}
{{-- Section 1 — QUOTATIONS                                        --}}
{{-- ============================================================ --}}
<section data-view-section="quotations">
    {{-- Toolbar --}}
    <div class="rounded border border-slate-200 bg-white p-3 mb-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-md">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-ui.icon name="search" size="sm" />
                </span>
                <input type="text" id="quotation-search"
                       onkeyup="filterTable('quotation_table', this.value); filterCards('quotation-cards', this.value);"
                       placeholder="Search quotations on this page..."
                       class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('quotation_table', 'quotations_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>

    @if($quotations->count() === 0)
        <div class="rounded border border-slate-200 bg-white">
            <x-ui.empty-state
                icon="file-text"
                title="No quotations yet"
                message="Create a tour with the quotation flag to start drafting prices.">
                @if(Auth::user()->can('tour.create'))
                    <x-ui.button as="a" href="{{ route('tour.create', ['is_quotation' => 1]) }}" icon="plus">New quotation</x-ui.button>
                @endif
            </x-ui.empty-state>
        </div>
    @else
        {{-- Desktop table --}}
        <div class="hidden md:block rounded border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table id="quotation_table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'quotation_table')">
                                <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'quotation_table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.Name') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'quotation_table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.Tour') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'quotation_table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.Assigned') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'quotation_table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.CreatedAt') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3">{{ trans('main.Frontsheet') }}</th>
                            <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($quotations as $quotation)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $quotation->id }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $quotation->name }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    @if($quotation->tour_id && $quotation->tour_name && Auth::user()->can('tour.show'))
                                        <a href="{{ route('tour.show', ['tour' => $quotation->tour_id]) }}" class="text-primary-700 hover:underline">
                                            {{ $quotation->tour_name }}
                                        </a>
                                    @elseif($quotation->tour_name)
                                        {{ $quotation->tour_name }}
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $quotation->user_name ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $quotation->formatted_created_at }}</td>
                                <td class="px-4 py-3">
                                    @if(Auth::user()->can('comparison.show'))
                                        <a href="{{ route('comparison.show', ['id' => $quotation->id]) }}"
                                           class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:text-primary-800">
                                            <x-ui.icon name="layout-grid" size="xs" />
                                            Front Sheet
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('quotation.edit', ['id' => $quotation->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                        <a href="{{ route('quotation.pdf', ['id' => $quotation->id]) }}" target="_blank"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="Print PDF">
                                            <x-ui.icon name="printer" size="sm" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile card list --}}
        <div id="quotation-cards" class="md:hidden space-y-3">
            @foreach($quotations as $quotation)
                <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $quotation->name }}</span>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $quotation->id }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <a href="{{ route('quotation.edit', ['id' => $quotation->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                            <a href="{{ route('quotation.pdf', ['id' => $quotation->id]) }}" target="_blank"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                               aria-label="Print">
                                <x-ui.icon name="printer" size="sm" />
                            </a>
                        </div>
                    </div>
                    <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                        @if($quotation->tour_name)
                            <div class="col-span-2">
                                <dt class="text-slate-500 uppercase tracking-wide">Tour</dt>
                                <dd>
                                    @if($quotation->tour_id && Auth::user()->can('tour.show'))
                                        <a href="{{ route('tour.show', ['tour' => $quotation->tour_id]) }}" class="text-primary-700">{{ $quotation->tour_name }}</a>
                                    @else
                                        <span class="text-slate-700">{{ $quotation->tour_name }}</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                        @if($quotation->user_name)
                            <div>
                                <dt class="text-slate-500 uppercase tracking-wide">Assigned</dt>
                                <dd class="text-slate-700">{{ $quotation->user_name }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Created</dt>
                            <dd class="text-slate-700">{{ $quotation->formatted_created_at }}</dd>
                        </div>
                        @if(Auth::user()->can('comparison.show'))
                            <div class="col-span-2">
                                <a href="{{ route('comparison.show', ['id' => $quotation->id]) }}" class="inline-flex items-center gap-1 text-xs font-medium text-primary-700">
                                    <x-ui.icon name="layout-grid" size="xs" />Front Sheet
                                </a>
                            </div>
                        @endif
                    </dl>
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- ============================================================ --}}
{{-- Section 2 — GO-AHEAD TOURS                                    --}}
{{-- ============================================================ --}}
<section data-view-section="goahead" class="hidden">
    <div class="rounded border border-slate-200 bg-white p-3 mb-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-md">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-ui.icon name="search" size="sm" />
                </span>
                <input type="text" id="goahead-search"
                       onkeyup="filterTable('go-ahead-table', this.value); filterCards('goahead-cards', this.value);"
                       placeholder="Search go-ahead tours..."
                       class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('go-ahead-table', 'goahead_tours_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>

    @if($goAheadTours->count() === 0)
        <div class="rounded border border-slate-200 bg-white">
            <x-ui.empty-state
                icon="check-circle"
                title="No go-ahead tours yet"
                message="Tours with a confirmed quotation show up here." />
        </div>
    @else
        {{-- Desktop table --}}
        <div class="hidden md:block rounded border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table id="go-ahead-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'go-ahead-table')">
                                <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'go-ahead-table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.Name') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'go-ahead-table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.DepDate') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'go-ahead-table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.CountryBegin') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'go-ahead-table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.CityBegin') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'go-ahead-table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.Status') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'go-ahead-table')">
                                <span class="inline-flex items-center gap-1">{{ trans('main.Externalname') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                            </th>
                            <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($goAheadTours as $tour)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $tour->id }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $tour->name }}</td>
                                <td class="px-4 py-3 text-slate-700 text-xs whitespace-nowrap">{{ $tour->departure_date ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $tour->country_begin ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $tour->city_begin ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    @if($tour->status_name)
                                        <span class="inline-flex items-center rounded bg-success-50 px-2 py-0.5 text-xs font-medium text-success-700">{{ $tour->status_name }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $tour->external_name ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        @if(Auth::user()->can('tour.show'))
                                            <a href="{{ route('tour.show', ['tour' => $tour->id]) }}"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                               title="View">
                                                <x-ui.icon name="eye" size="sm" />
                                            </a>
                                        @endif
                                        @if(Auth::user()->can('tour.edit'))
                                            <a href="{{ route('tour.edit', ['tour' => $tour->id]) }}"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                               title="Edit">
                                                <x-ui.icon name="edit" size="sm" />
                                            </a>
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
        <div id="goahead-cards" class="md:hidden space-y-3">
            @foreach($goAheadTours as $tour)
                <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $tour->name }}</span>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $tour->id }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            @if(Auth::user()->can('tour.show'))
                                <a href="{{ route('tour.show', ['tour' => $tour->id]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100">
                                    <x-ui.icon name="eye" size="sm" />
                                </a>
                            @endif
                            @if(Auth::user()->can('tour.edit'))
                                <a href="{{ route('tour.edit', ['tour' => $tour->id]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100">
                                    <x-ui.icon name="edit" size="sm" />
                                </a>
                            @endif
                        </div>
                    </div>
                    <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                        @if($tour->departure_date)
                            <div>
                                <dt class="text-slate-500 uppercase tracking-wide">Departure</dt>
                                <dd class="text-slate-700">{{ $tour->departure_date }}</dd>
                            </div>
                        @endif
                        @if($tour->status_name)
                            <div>
                                <dt class="text-slate-500 uppercase tracking-wide">Status</dt>
                                <dd><span class="inline-flex items-center rounded bg-success-50 px-2 py-0.5 text-xs font-medium text-success-700">{{ $tour->status_name }}</span></dd>
                            </div>
                        @endif
                        @if($tour->country_begin || $tour->city_begin)
                            <div class="col-span-2">
                                <dt class="text-slate-500 uppercase tracking-wide">From</dt>
                                <dd class="text-slate-700">
                                    {{ trim(($tour->city_begin ?? '') . (($tour->city_begin && $tour->country_begin) ? ', ' : '') . ($tour->country_begin ?? '')) }}
                                </dd>
                            </div>
                        @endif
                        @if($tour->external_name)
                            <div class="col-span-2">
                                <dt class="text-slate-500 uppercase tracking-wide">External name</dt>
                                <dd class="text-slate-700">{{ $tour->external_name }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('quotation_table');
            initializeBootstrapTable('go-ahead-table');
        }
        // Default to "Quotations" view on first render.
        quotationViewSwitch('quotations');
    });

    // Segmented control switcher. Swaps which section is visible and which
    // button is active; replaces the legacy checkbox-pseudo-element toggle.
    window.quotationViewSwitch = function (view) {
        var activeCls = ['bg-white', 'text-slate-900', 'shadow-subtle'];
        var idleCls   = ['text-slate-600', 'hover:text-slate-900'];

        document.querySelectorAll('[data-view-btn]').forEach(function (btn) {
            var on = btn.getAttribute('data-view-btn') === view;
            activeCls.forEach(function (c) { btn.classList.toggle(c, on); });
            idleCls.forEach(function (c)   { btn.classList.toggle(c, !on); });
            // Update the count-badge tint so the active button's count
            // reads as primary rather than slate.
            var badge = btn.querySelector('span.rounded-full');
            if (badge) {
                badge.classList.toggle('bg-primary-50',    on);
                badge.classList.toggle('text-primary-700', on);
                badge.classList.toggle('bg-slate-100',     !on);
                badge.classList.toggle('text-slate-600',   !on);
            }
        });
        document.querySelectorAll('[data-view-section]').forEach(function (sec) {
            sec.classList.toggle('hidden', sec.getAttribute('data-view-section') !== view);
        });
    };

    // Mobile-card search mirror (same pattern as /clients index).
    window.filterCards = window.filterCards || function (containerId, value) {
        var c = document.getElementById(containerId);
        if (!c) return;
        var q = (value || '').toLowerCase();
        c.querySelectorAll('[data-card-row]').forEach(function (row) {
            var name = (row.querySelector('[data-card-name]')?.textContent || '').toLowerCase();
            row.style.display = (!q || name.indexOf(q) !== -1) ? '' : 'none';
        });
    };
</script>
@endpush
