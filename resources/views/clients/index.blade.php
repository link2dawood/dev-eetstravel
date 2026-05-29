@extends('scaffold-interface.layouts.tabler-app')
@section('title','Clients')

@section('content')
<x-ui.page-header
    title="Clients"
    description="Travel agencies and direct clients booked into tours."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Clients'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('clients.create'))
            <x-ui.button as="a" href="{{ route('clients.create') }}" icon="plus">
                {!! trans('main.New') ?? 'New' !!} client
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

{{-- Toolbar: search + export. Search filters client-side via the existing
     `filterTable()` helper from js/bootstrap-tables.js — works per visible
     pagination page. Export CSV preserves the existing exportTableToCSV() JS. --}}
<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text"
                   id="clients-search"
                   onkeyup="filterTable('clients-table', this.value); filterCards('clients-cards', this.value);"
                   placeholder="Search clients on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('clients-table', 'clients_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($clients) === 0)
    {{-- Empty state — only render when there are zero records, not when search
         hides everything (search is client-side and reversible). --}}
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="users"
            title="No clients yet"
            message="Add your first client to start building quotes and tours.">
            @if(Auth::user()->can('clients.create'))
                <x-ui.button as="a" href="{{ route('clients.create') }}" icon="plus">Add a client</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- ============================================================ --}}
    {{-- Desktop table (md and up) --}}
    {{-- ============================================================ --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="clients-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'clients-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'clients-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Name') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'clients-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Country') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'clients-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.City') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'clients-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('Account No') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'clients-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.WorkPhone') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'clients-table')">
                            <span class="inline-flex items-center gap-1">{!! trans('main.WorkEmail') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($clients as $client)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $client->id }}</td>
                            <td class="px-4 py-3">
                                @if(Auth::user()->can('clients.show'))
                                    <a href="{{ route('clients.show', ['client' => $client->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $client->name }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $client->name }}</span>
                                @endif
                                @if(!empty($client->address))
                                    <p class="mt-0.5 text-xs text-slate-500 truncate max-w-xs">{{ $client->address }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $client->country_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $client->city_name ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $client->account_no ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($client->work_phone)
                                    <a href="tel:{{ $client->work_phone }}" class="hover:text-primary-700">{{ $client->work_phone }}</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($client->work_email)
                                    <a href="mailto:{{ $client->work_email }}" class="hover:text-primary-700 truncate inline-block max-w-[200px]" title="{{ $client->work_email }}">{{ $client->work_email }}</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('clients.show'))
                                        <a href="{{ route('clients.show', ['client' => $client->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('clients.edit'))
                                        <a href="{{ route('clients.edit', ['client' => $client->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('client.destroy'))
                                        <button type="button"
                                                onclick="clientDeleteConfirm({{ $client->id }}, @js($client->name))"
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

    {{-- ============================================================ --}}
    {{-- Mobile card list (below md) --}}
    {{-- ============================================================ --}}
    <div id="clients-cards" class="md:hidden space-y-3">
        @foreach($clients as $client)
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('clients.show'))
                            <a href="{{ route('clients.show', ['client' => $client->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $client->name }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $client->name }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $client->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('clients.edit'))
                            <a href="{{ route('clients.edit', ['client' => $client->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        @if(Auth::user()->can('client.destroy'))
                            <button type="button"
                                    onclick="clientDeleteConfirm({{ $client->id }}, @js($client->name))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                    aria-label="Delete">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        @endif
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if($client->country_name || $client->city_name)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Location</dt>
                            <dd class="text-slate-700">
                                {{ trim(($client->city_name ?? '') . (($client->city_name && $client->country_name) ? ', ' : '') . ($client->country_name ?? '')) ?: '—' }}
                            </dd>
                        </div>
                    @endif
                    @if($client->account_no)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Account</dt>
                            <dd class="text-slate-700 font-mono">{{ $client->account_no }}</dd>
                        </div>
                    @endif
                    @if($client->work_phone)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Phone</dt>
                            <dd><a href="tel:{{ $client->work_phone }}" class="text-primary-700">{{ $client->work_phone }}</a></dd>
                        </div>
                    @endif
                    @if($client->work_email)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Email</dt>
                            <dd><a href="mailto:{{ $client->work_email }}" class="text-primary-700 break-all">{{ $client->work_email }}</a></dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>

    {{-- ============================================================ --}}
    {{-- Pagination strip --}}
    {{-- ============================================================ --}}
    @if($clients->hasPages())
        <nav class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <p class="text-xs text-slate-500">
                Showing <span class="font-medium text-slate-700">{{ $clients->firstItem() }}</span>
                to <span class="font-medium text-slate-700">{{ $clients->lastItem() }}</span>
                of <span class="font-medium text-slate-700">{{ $clients->total() }}</span> clients
            </p>
            <div class="flex items-center gap-1 flex-wrap">
                @if($clients->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </span>
                @else
                    <a href="{{ $clients->previousPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </a>
                @endif

                @foreach($clients->onEachSide(1)->getUrlRange(max(1, $clients->currentPage() - 2), min($clients->lastPage(), $clients->currentPage() + 2)) as $page => $url)
                    @if($page == $clients->currentPage())
                        <span class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded bg-primary-600 px-3 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($clients->hasMorePages())
                    <a href="{{ $clients->nextPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
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

{{-- ============================================================ --}}
{{-- Delete confirmation modal (Tailwind, vanilla JS) --}}
{{-- ============================================================ --}}
<div id="clientDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="clientDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete client?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="clientDeleteName" class="text-slate-700"></strong>.
                        This will remove the client and unlink them from any future quotes. Past tours are preserved.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="clientDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="clientDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
                    <x-ui.icon name="trash-2" size="sm" />
                    Delete client
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
            initializeBootstrapTable('clients-table');
        }
    });

    // Filter the mobile card list by matching the visible client name. Mirrors
    // filterTable() from bootstrap-tables.js so the search input behaves the
    // same on phones (cards) and desktop (table rows).
    window.filterCards = function (containerId, value) {
        var c = document.getElementById(containerId);
        if (!c) return;
        var q = (value || '').toLowerCase();
        c.querySelectorAll('[data-card-row]').forEach(function (row) {
            var name = (row.querySelector('[data-card-name]')?.textContent || '').toLowerCase();
            row.style.display = (!q || name.indexOf(q) !== -1) ? '' : 'none';
        });
    };

    // Delete-confirm modal (vanilla JS — no Alpine dependency).
    window.clientDeleteConfirm = function (id, name) {
        var modal  = document.getElementById('clientDeleteModal');
        var label  = document.getElementById('clientDeleteName');
        var btn    = document.getElementById('clientDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('client #' + id);
        btn.href = '{{ url("clients") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.clientDeleteCancel = function () {
        var modal = document.getElementById('clientDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.clientDeleteCancel();
    });
</script>
@endpush
