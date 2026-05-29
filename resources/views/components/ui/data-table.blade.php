{{--
    <x-ui.data-table /> — Full data table with search, sort, pagination,
    empty / loading / error states.

    This is the canonical replacement for the legacy `$().DataTable(...)`
    + central CDN partial pattern across ~19 views.

    Server-side architecture:
      * The view passes a Laravel paginator (LengthAwarePaginator) via `paginator`.
      * Column definitions are passed as an array via `columns`.
      * Sort + search are URL query params (handled by the controller).

    Props
    -----
    paginator   (LengthAwarePaginator|null)  Paginated result set.
    columns     (array)                       Column defs: [
                                                ['key' => 'name', 'label' => 'Name', 'sortable' => true],
                                                ['key' => 'pax',  'label' => 'Pax',  'align' => 'right'],
                                              ]
    rowKey      (string)                      Property name used as row React-style key. Default "id".
    searchable  (bool)                        Show the search input + filter bar slot.
    searchParam (string)                      Query-string param name for search. Default "q".
    sortParam   (string)                      Query param for sort field. Default "sort".
    dirParam    (string)                      Query param for sort direction. Default "dir".
    loading     (bool)                        Shows loading skeleton instead of rows.
    error       (string|null)                 Renders error state with the message.
    emptyTitle  (string)                      Heading shown when paginator is empty.
    emptyMessage(string)                      Body text shown when paginator is empty.

    Slots
    -----
    actions      Right side of the toolbar (e.g. "New tour" button).
    filters      Below the search input (filter chips, date range, status select).
    cell-{key}   Override the cell rendering for a column. Receives $row variable.
                 Use: <x-slot name="cell-name">{{ $row->name }}</x-slot>
                 Falls back to {{ $row->{$col['key']} }} if no slot provided.

    Example
    -------
        <x-ui.data-table
            :paginator="$tours"
            :columns="[
                ['key' => 'name', 'label' => 'Name', 'sortable' => true],
                ['key' => 'departure_date', 'label' => 'Departure', 'sortable' => true],
                ['key' => 'pax', 'label' => 'Pax', 'align' => 'right'],
            ]"
            searchable
            empty-title="No tours yet"
            empty-message="Create your first tour to get started.">

            <x-slot name="actions">
                <x-ui.button as="a" href="{{ route('tour.create') }}" icon="plus">New tour</x-ui.button>
            </x-slot>
        </x-ui.data-table>
--}}

@props([
    'paginator' => null,
    'columns' => [],
    'rowKey' => 'id',
    'searchable' => false,
    'searchParam' => 'q',
    'sortParam' => 'sort',
    'dirParam' => 'dir',
    'loading' => false,
    'error' => null,
    'emptyTitle' => 'No data',
    'emptyMessage' => null,
])

@php
    $items = $paginator ? $paginator->items() : [];
    $hasRows = $paginator && count($items) > 0 && !$loading && !$error;
    $currentSort = request()->query($sortParam);
    $currentDir  = request()->query($dirParam) === 'desc' ? 'desc' : 'asc';
@endphp

<div {{ $attributes->class('bg-white border border-slate-200 rounded shadow-card overflow-hidden') }}>
    {{-- Toolbar --}}
    @if($searchable || isset($actions) || isset($filters))
        <div class="border-b border-slate-200 px-4 py-3 space-y-3">
            <div class="flex items-center gap-3">
                @if($searchable)
                    <form method="GET" class="flex-1 max-w-md">
                        <x-ui.input
                            name="{{ $searchParam }}"
                            type="search"
                            placeholder="Search…"
                            leadingIcon="search"
                            :value="request()->query($searchParam)"
                            size="sm"
                        />
                    </form>
                @else
                    <div class="flex-1"></div>
                @endif

                @if(isset($actions))
                    <div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
                @endif
            </div>

            @if(isset($filters))
                <div class="flex items-center gap-2 flex-wrap">{{ $filters }}</div>
            @endif
        </div>
    @endif

    {{-- Body: error → loading → empty → rows --}}
    @if($error)
        <x-ui.error-state :message="$error" />
    @elseif($loading)
        <x-ui.loading-state message="Loading…" />
    @elseif(!$hasRows)
        <x-ui.empty-state :title="$emptyTitle" :message="$emptyMessage" />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        @foreach($columns as $col)
                            @php
                                $align = $col['align'] ?? 'left';
                                $key = $col['key'];
                                $sortable = ($col['sortable'] ?? false);
                                $thisSort = $sortable && $currentSort === $key;
                                $thisDir  = $thisSort ? $currentDir : null;
                                $nextDir  = ($thisSort && $currentDir === 'asc') ? 'desc' : 'asc';
                                $sortUrl  = $sortable
                                    ? request()->fullUrlWithQuery([$sortParam => $key, $dirParam => $nextDir])
                                    : null;
                            @endphp
                            <x-ui.th
                                :align="$align"
                                :sort="$sortable ? $key : null"
                                :sortDir="$thisDir"
                                :sortUrl="$sortUrl"
                            >{{ $col['label'] }}</x-ui.th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($items as $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            @foreach($columns as $col)
                                @php
                                    $align = $col['align'] ?? 'left';
                                    $key = $col['key'];
                                    $slotName = 'cell-' . $key;
                                    $value = is_object($row) ? ($row->{$key} ?? null) : ($row[$key] ?? null);
                                @endphp
                                <x-ui.td :align="$align">
                                    @if(isset(${$slotName}))
                                        {{ ${$slotName}($row) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </x-ui.td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer: paginator --}}
        @if($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 flex items-center justify-between text-sm text-slate-500">
                <span>
                    Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
                </span>
                <div class="flex items-center gap-1">
                    {{-- Compact pagination: prev / page numbers / next --}}
                    @if($paginator->onFirstPage())
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-300">
                            <x-ui.icon name="chevron-left" />
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                            <x-ui.icon name="chevron-left" />
                        </a>
                    @endif

                    <span class="px-2 text-slate-600">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

                    @if($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                            <x-ui.icon name="chevron-right" />
                        </a>
                    @else
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-300">
                            <x-ui.icon name="chevron-right" />
                        </span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
