{{--
    Tours index — clean SaaS-style list page (Phase 3 redesign).

    Original work. Common list-page patterns: page header, filter row,
    tab bar, sortable table, pagination. No third-party code reproduced.

    Data passed in by TourController@index:
      $tours              LengthAwarePaginator   active (non-archived, non-cancelled)
      $clientTours        LengthAwarePaginator   tours with client_id
      $monthlyChartTours  LengthAwarePaginator   status = 4
      $cancelledChartTours LengthAwarePaginator  status = 46
      $archivedTours      LengthAwarePaginator   status in [6, 39]
      $years              Collection             distinct years
      $months             array<int,string>      1..12 month names
      $title              string                 "Tour"
--}}
@extends('scaffold-interface.layouts.tabler-app')

@section('title', 'Tours')

@section('content')

@php
    $user = \Illuminate\Support\Facades\Auth::user();

    // Which tab is active. Query param `tab=…` selects; default is active.
    $tab = request()->query('tab', 'active');
    $allowedTabs = ['active', 'client', 'monthly', 'cancelled', 'archived'];
    if (!in_array($tab, $allowedTabs, true)) { $tab = 'active'; }

    $tabs = [
        ['key' => 'active',    'label' => 'Active',     'count' => $tours->total(),              'paginator' => $tours],
        ['key' => 'client',    'label' => 'With client','count' => $clientTours->total(),        'paginator' => $clientTours],
        ['key' => 'monthly',   'label' => 'Monthly',    'count' => $monthlyChartTours->total(),  'paginator' => $monthlyChartTours],
        ['key' => 'cancelled', 'label' => 'Cancelled',  'count' => $cancelledChartTours->total(),'paginator' => $cancelledChartTours],
        ['key' => 'archived',  'label' => 'Archived',   'count' => $archivedTours->total(),      'paginator' => $archivedTours],
    ];
    $activeTab = collect($tabs)->firstWhere('key', $tab);
    $paginator = $activeTab['paginator'];

    // Map Tour::$statusColors to a Tailwind badge variant + human label
    $badgeForStatus = function ($statusRel, $statusId) {
        $name = optional($statusRel)->name;
        if (!$name) { return ['variant' => 'neutral', 'label' => '—']; }
        // crude buckets: green for completed/active, red for cancelled, neutral otherwise
        $low = strtolower($name);
        if (str_contains($low, 'cancel')) return ['variant' => 'danger',  'label' => $name];
        if (str_contains($low, 'complete') || str_contains($low, 'finish')) return ['variant' => 'success', 'label' => $name];
        if (str_contains($low, 'draft'))    return ['variant' => 'neutral', 'label' => $name];
        if (str_contains($low, 'archive'))  return ['variant' => 'neutral', 'label' => $name];
        if (str_contains($low, 'progress')) return ['variant' => 'info',    'label' => $name];
        return ['variant' => 'primary', 'label' => $name];
    };

    $searchTerm = trim((string) request()->query('q', ''));
@endphp

<x-ui.page-header
    title="Tours"
    description="All tours across statuses. Filter by tab to narrow the view."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tours'],
    ]"
>
    <x-slot name="actions">
        @if ($user->can('tour.create'))
            <x-ui.button as="a" href="{{ url('/tour/create') }}" variant="primary" icon="plus">
                New tour
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

{{-- Filter bar: search + year + month --}}
<form method="GET" action="{{ url('/tour') }}" class="mb-4 flex flex-wrap items-center gap-2">
    <input type="hidden" name="tab" value="{{ $tab }}" />

    <div class="relative flex-1 min-w-[220px] max-w-md">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <x-ui.icon name="search" />
        </span>
        <input
            type="search"
            name="q"
            value="{{ $searchTerm }}"
            placeholder="Search tour name…"
            class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
        />
    </div>

    @php
        // The controller can return $years as either an array or a Collection
        // depending on the path taken. Normalise to a simple list of year
        // ints so the view doesn't crash either way.
        $yearList = [];
        foreach ((array) $years as $row) {
            if (is_object($row) && isset($row->year)) { $yearList[] = (int) $row->year; }
            elseif (is_numeric($row))                  { $yearList[] = (int) $row; }
        }
        $yearList = array_values(array_unique(array_filter($yearList)));
        rsort($yearList);
    @endphp
    @if (count($yearList) > 0)
        <select name="year"
                class="h-9 rounded border border-slate-300 bg-white pl-3 pr-9 text-sm text-slate-700 shadow-subtle appearance-none focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            <option value="">All years</option>
            @foreach ($yearList as $year)
                <option value="{{ $year }}" @if (request()->query('year') == $year) selected @endif>{{ $year }}</option>
            @endforeach
        </select>
    @endif

    @if (!empty($months))
        <select name="month"
                class="h-9 rounded border border-slate-300 bg-white pl-3 pr-9 text-sm text-slate-700 shadow-subtle appearance-none focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            <option value="">All months</option>
            @foreach ($months as $num => $name)
                <option value="{{ $num }}" @if (request()->query('month') == $num) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
    @endif

    <x-ui.button type="submit" variant="secondary" icon="filter">Apply</x-ui.button>

    @if ($searchTerm !== '' || request()->query('year') || request()->query('month'))
        <a href="{{ url('/tour?tab=' . $tab) }}" class="text-xs text-slate-500 hover:text-slate-700 underline-offset-2 hover:underline">
            Clear
        </a>
    @endif
</form>

{{-- Tab bar --}}
<div class="border-b border-slate-200 mb-0" role="tablist" aria-label="Tour buckets">
    <nav class="-mb-px flex gap-6 overflow-x-auto">
        @foreach ($tabs as $t)
            @php
                $isActive = $t['key'] === $tab;
                $queryString = http_build_query(array_merge(request()->query(), ['tab' => $t['key']]));
            @endphp
            <a href="{{ url('/tour') }}?{{ $queryString }}"
               role="tab"
               aria-selected="{{ $isActive ? 'true' : 'false' }}"
               class="group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-2 text-sm transition-colors
                      {{ $isActive
                          ? 'border-primary-600 text-primary-700 font-medium'
                          : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                <span>{{ $t['label'] }}</span>
                <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1.5 text-xs font-medium
                             {{ $isActive ? 'bg-primary-50 text-primary-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $t['count'] }}
                </span>
            </a>
        @endforeach
    </nav>
</div>

{{-- Data table --}}
<div class="rounded-b border border-t-0 border-slate-200 bg-white">
    @if ($paginator->isEmpty())
        <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
            <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <x-ui.icon name="map" size="lg" />
            </span>
            <h3 class="text-sm font-semibold text-slate-900">No tours in this view</h3>
            <p class="mt-1 max-w-sm text-sm text-slate-500">
                @if ($tab === 'archived')
                    Archived tours appear here once they're moved out of the active workflow.
                @elseif ($tab === 'cancelled')
                    No cancelled tours yet.
                @elseif ($tab === 'client')
                    No tours have a client assigned yet.
                @else
                    Create your first tour to get started.
                @endif
            </p>
            @if ($tab === 'active' && $user->can('tour.create'))
                <div class="mt-4">
                    <x-ui.button as="a" href="{{ url('/tour/create') }}" variant="primary" icon="plus" size="sm">
                        New tour
                    </x-ui.button>
                </div>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Tour</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Dates</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Route</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Client</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Owner</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wide text-slate-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($paginator as $tour)
                        @php
                            $statusInfo = $badgeForStatus($tour->status_rel ?? optional($tour->status), $tour->status);
                            $departure = $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date) : null;
                            $route = collect([optional($tour->city_begin)->name, optional($tour->city_end)->name])
                                ->filter()->implode(' → ');
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ url('/tour/' . $tour->id) }}" class="block min-w-0 group">
                                    <span class="block truncate text-sm font-medium text-slate-900 group-hover:text-primary-700">
                                        {{ $tour->name }}
                                    </span>
                                    @if (!empty($tour->external_name))
                                        <span class="block truncate text-xs text-slate-500 mt-0.5">{{ $tour->external_name }}</span>
                                    @endif
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700 whitespace-nowrap">
                                @if ($departure)
                                    {{ $departure->translatedFormat('M j, Y') }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $route ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 max-w-[160px] truncate">
                                {{ $tour->client_name ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 max-w-[160px] truncate">
                                {{ $tour->responsible_user_names ?: '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge :variant="$statusInfo['variant']" dot>{{ $statusInfo['label'] }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ url('/tour/' . $tour->id) }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                       title="View">
                                        <x-ui.icon name="eye" />
                                    </a>
                                    @if ($user->can('tour.update'))
                                        <a href="{{ url('/tour/' . $tour->id . '/edit') }}"
                                           class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" />
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination footer --}}
        @if ($paginator->hasPages())
            <div class="flex items-center justify-between border-t border-slate-200 px-4 py-3 text-sm text-slate-500">
                <span>
                    Showing
                    <span class="font-medium text-slate-700">{{ $paginator->firstItem() }}</span>
                    to
                    <span class="font-medium text-slate-700">{{ $paginator->lastItem() }}</span>
                    of
                    <span class="font-medium text-slate-700">{{ $paginator->total() }}</span>
                </span>

                <div class="flex items-center gap-1">
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-300 cursor-not-allowed">
                            <x-ui.icon name="chevron-left" />
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}"
                           class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                           aria-label="Previous page">
                            <x-ui.icon name="chevron-left" />
                        </a>
                    @endif

                    <span class="px-3 text-slate-700">
                        Page <span class="font-medium">{{ $paginator->currentPage() }}</span>
                        / {{ $paginator->lastPage() }}
                    </span>

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}"
                           class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                           aria-label="Next page">
                            <x-ui.icon name="chevron-right" />
                        </a>
                    @else
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-300 cursor-not-allowed">
                            <x-ui.icon name="chevron-right" />
                        </span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

@endsection
