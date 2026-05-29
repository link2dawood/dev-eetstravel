@extends('scaffold-interface.layouts.tabler-app')
@section('title','Guides')

@section('content')
<x-ui.page-header
    title="Guides"
    description="Guide suppliers and negotiated rates."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Guides'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('guide.create'))
            <x-ui.button as="a" href="{{ route('guide.create') }}" icon="plus">
                {!! trans('main.New') ?? 'New' !!} guide
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

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
                   id="guides-search"
                   onkeyup="filterTable('guides-table', this.value); filterCards('guides-cards', this.value);"
                   placeholder="Search guides on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('guides-table', 'guides_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($guides) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="user-check"
            title="No guides yet"
            message="Add your first guide to start building tour packages.">
            @if(Auth::user()->can('guide.create'))
                <x-ui.button as="a" href="{{ route('guide.create') }}" icon="plus">Add a guide</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- Desktop table --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="guides-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'guides-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'guides-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Name') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'guides-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Country') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'guides-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.City') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'guides-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.WorkPhone') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'guides-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.WorkContact') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($guides as $guide)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $guide->id }}</td>
                            <td class="px-4 py-3">
                                @if(Auth::user()->can('guide.show'))
                                    <a href="{{ route('guide.show', ['guide' => $guide->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $guide->name }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $guide->name }}</span>
                                @endif
                                @if(!empty($guide->address))
                                    <p class="mt-0.5 text-xs text-slate-500 truncate max-w-xs">{{ $guide->address }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $guide->country_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $guide->city_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($guide->work_phone)
                                    <a href="tel:{{ $guide->work_phone }}" class="hover:text-primary-700">{{ $guide->work_phone }}</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $guide->work_contact ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('guide.show'))
                                        <a href="{{ route('guide.show', ['guide' => $guide->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('guide.edit'))
                                        <a href="{{ route('guide.edit', ['guide' => $guide->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('guide.destroy'))
                                        <button type="button"
                                                onclick="guideDeleteConfirm({{ $guide->id }}, @js($guide->name))"
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
    <div id="guides-cards" class="md:hidden space-y-3">
        @foreach($guides as $guide)
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('guide.show'))
                            <a href="{{ route('guide.show', ['guide' => $guide->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $guide->name }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $guide->name }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $guide->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('guide.edit'))
                            <a href="{{ route('guide.edit', ['guide' => $guide->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        @if(Auth::user()->can('guide.destroy'))
                            <button type="button"
                                    onclick="guideDeleteConfirm({{ $guide->id }}, @js($guide->name))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                    aria-label="Delete">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        @endif
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if($guide->country_name || $guide->city_name)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Location</dt>
                            <dd class="text-slate-700">
                                {{ trim(($guide->city_name ?? '') . (($guide->city_name && $guide->country_name) ? ', ' : '') . ($guide->country_name ?? '')) ?: '—' }}
                            </dd>
                        </div>
                    @endif
                    @if($guide->address)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Address</dt>
                            <dd class="text-slate-700">{{ $guide->address }}</dd>
                        </div>
                    @endif
                    @if($guide->work_phone)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Phone</dt>
                            <dd><a href="tel:{{ $guide->work_phone }}" class="text-primary-700">{{ $guide->work_phone }}</a></dd>
                        </div>
                    @endif
                    @if($guide->work_contact)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Contact</dt>
                            <dd class="text-slate-700">{{ $guide->work_contact }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>

    {{-- Pagination strip --}}
    @if($guides->hasPages())
        <nav class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <p class="text-xs text-slate-500">
                Showing <span class="font-medium text-slate-700">{{ $guides->firstItem() }}</span>
                to <span class="font-medium text-slate-700">{{ $guides->lastItem() }}</span>
                of <span class="font-medium text-slate-700">{{ $guides->total() }}</span> guides
            </p>
            <div class="flex items-center gap-1 flex-wrap">
                @if($guides->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </span>
                @else
                    <a href="{{ $guides->previousPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </a>
                @endif

                @foreach($guides->onEachSide(1)->getUrlRange(max(1, $guides->currentPage() - 2), min($guides->lastPage(), $guides->currentPage() + 2)) as $page => $url)
                    @if($page == $guides->currentPage())
                        <span class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded bg-primary-600 px-3 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($guides->hasMorePages())
                    <a href="{{ $guides->nextPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
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
<div id="guideDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="guideDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete guide?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="guideDeleteName" class="text-slate-700"></strong>.
                        This will remove the guide from your supplier catalog. Existing tours that reference it stay intact.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="guideDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="guideDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
                    <x-ui.icon name="trash-2" size="sm" />
                    Delete guide
                </a>
            </div>
        </div>
    </div>
</div>

<span id="service-name" hidden data-service-name='Guide'></span>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('guides-table');
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

    window.guideDeleteConfirm = function (id, name) {
        var modal = document.getElementById('guideDeleteModal');
        var label = document.getElementById('guideDeleteName');
        var btn   = document.getElementById('guideDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('guide #' + id);
        btn.href = '{{ url("guide") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.guideDeleteCancel = function () {
        var modal = document.getElementById('guideDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.guideDeleteCancel();
    });
</script>
@endpush
