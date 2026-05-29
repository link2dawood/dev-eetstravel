@extends('scaffold-interface.layouts.tabler-app')
@section('title','Bus Companies')

@section('content')
<x-ui.page-header
    title="Bus Companies"
    description="Transfer suppliers and negotiated rates."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Bus Companies'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('transfer.create'))
            <x-ui.button as="a" href="{{ route('transfer.create') }}" icon="plus">
                {!! trans('main.New') ?? 'New' !!} bus company
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
                   id="transfer-search"
                   onkeyup="filterTable('transfer-table', this.value); filterCards('transfer-cards', this.value);"
                   placeholder="Search bus companies on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('transfer-table', 'transfers_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($transfers) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="bus"
            title="No bus companies yet"
            message="Add your first bus company to start building tour packages.">
            @if(Auth::user()->can('transfer.create'))
                <x-ui.button as="a" href="{{ route('transfer.create') }}" icon="plus">Add a bus company</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- Desktop table --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="transfer-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'transfer-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'transfer-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Name') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'transfer-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Address') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'transfer-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Country') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'transfer-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.City') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'transfer-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Phone') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'transfer-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Contact') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($transfers as $transfer)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $transfer->id }}</td>
                            <td class="px-4 py-3">
                                @if(Auth::user()->can('transfer.show'))
                                    <a href="{{ route('transfer.show', ['transfer' => $transfer->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $transfer->name ?? '—' }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $transfer->name ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $transfer->address_first ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $transfer->country_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $transfer->city_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($transfer->work_phone)
                                    <a href="tel:{{ $transfer->work_phone }}" class="hover:text-primary-700">{{ $transfer->work_phone }}</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $transfer->contact_name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('transfer.show'))
                                        <a href="{{ route('transfer.show', ['transfer' => $transfer->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('transfer.edit'))
                                        <a href="{{ route('transfer.edit', ['transfer' => $transfer->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    <button type="button"
                                            onclick="transferDeleteConfirm({{ $transfer->id }}, @js($transfer->name))"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                            title="Delete">
                                        <x-ui.icon name="trash-2" size="sm" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile card list --}}
    <div id="transfer-cards" class="md:hidden space-y-3">
        @foreach($transfers as $transfer)
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('transfer.show'))
                            <a href="{{ route('transfer.show', ['transfer' => $transfer->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $transfer->name ?? '—' }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $transfer->name ?? '—' }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $transfer->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('transfer.edit'))
                            <a href="{{ route('transfer.edit', ['transfer' => $transfer->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        <button type="button"
                                onclick="transferDeleteConfirm({{ $transfer->id }}, @js($transfer->name))"
                                class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                aria-label="Delete">
                            <x-ui.icon name="trash-2" size="sm" />
                        </button>
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if($transfer->country_name || $transfer->city_name)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Location</dt>
                            <dd class="text-slate-700">
                                {{ trim(($transfer->city_name ?? '') . (($transfer->city_name && $transfer->country_name) ? ', ' : '') . ($transfer->country_name ?? '')) ?: '—' }}
                            </dd>
                        </div>
                    @endif
                    @if($transfer->address_first)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Address</dt>
                            <dd class="text-slate-700">{{ $transfer->address_first }}</dd>
                        </div>
                    @endif
                    @if($transfer->work_phone)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Phone</dt>
                            <dd><a href="tel:{{ $transfer->work_phone }}" class="text-primary-700">{{ $transfer->work_phone }}</a></dd>
                        </div>
                    @endif
                    @if($transfer->contact_name)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Contact</dt>
                            <dd class="text-slate-700">{{ $transfer->contact_name }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>

    {{-- Pagination strip --}}
    @if($transfers->hasPages())
        <nav class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <p class="text-xs text-slate-500">
                Showing <span class="font-medium text-slate-700">{{ $transfers->firstItem() }}</span>
                to <span class="font-medium text-slate-700">{{ $transfers->lastItem() }}</span>
                of <span class="font-medium text-slate-700">{{ $transfers->total() }}</span> bus companies
            </p>
            <div class="flex items-center gap-1 flex-wrap">
                @if($transfers->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </span>
                @else
                    <a href="{{ $transfers->previousPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </a>
                @endif

                @foreach($transfers->onEachSide(1)->getUrlRange(max(1, $transfers->currentPage() - 2), min($transfers->lastPage(), $transfers->currentPage() + 2)) as $page => $url)
                    @if($page == $transfers->currentPage())
                        <span class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded bg-primary-600 px-3 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($transfers->hasMorePages())
                    <a href="{{ $transfers->nextPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
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
<div id="transferDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="transferDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete bus company?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="transferDeleteName" class="text-slate-700"></strong>.
                        This will remove the bus company from your supplier catalog. Existing tours that reference it stay intact.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="transferDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="transferDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
                    <x-ui.icon name="trash-2" size="sm" />
                    Delete bus company
                </a>
            </div>
        </div>
    </div>
</div>

<span id="service-name" hidden data-service-name='Transfer'></span>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('transfer-table');
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

    window.transferDeleteConfirm = function (id, name) {
        var modal = document.getElementById('transferDeleteModal');
        var label = document.getElementById('transferDeleteName');
        var btn   = document.getElementById('transferDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('bus company #' + id);
        btn.href = '{{ url("transfer") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.transferDeleteCancel = function () {
        var modal = document.getElementById('transferDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.transferDeleteCancel();
    });
</script>
@endpush
