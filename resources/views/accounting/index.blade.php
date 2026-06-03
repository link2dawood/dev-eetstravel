@extends('scaffold-interface.layouts.tabler-app')
@section('title','Client Invoices')

@section('content')
<x-ui.page-header
    title="Client Invoices"
    description="Invoices issued to clients, grouped by tour."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Client Invoices'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('transactions.create'))
            <x-ui.button as="a" href="{{ route('accounting.create') }}" icon="plus">
                {!! trans('main.New') ?? 'New' !!} invoice
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if(session('message_buses'))
    <div class="mb-4 rounded border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-800">
        {{ session('message_buses') }}
    </div>
@endif

{{-- Toolbar: search + export (client-side via bootstrap-tables.js). --}}
<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text"
                   id="accounting-search"
                   onkeyup="filterTable('transactions-table', this.value); filterCards('accounting-cards', this.value);"
                   placeholder="Search client invoices on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('transactions-table', 'client_invoices_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($accountingData) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="receipt"
            title="No client invoices yet"
            message="Client invoices created from tours will appear here.">
            @if(Auth::user()->can('transactions.create'))
                <x-ui.button as="a" href="{{ route('accounting.create') }}" icon="plus">Add an invoice</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- ============================================================ --}}
    {{-- Desktop table (md and up) --}}
    {{-- ============================================================ --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="transactions-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'transactions-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'transactions-table')">
                            <span class="inline-flex items-center gap-1">Date <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'transactions-table')">
                            <span class="inline-flex items-center gap-1">Invoice No <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'transactions-table')">
                            <span class="inline-flex items-center gap-1">Tour <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'transactions-table')">
                            <span class="inline-flex items-center gap-1">Client <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'transactions-table')">
                            <span class="inline-flex items-center gap-1">Receivable <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'transactions-table')">
                            <span class="inline-flex items-center gap-1">Status <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($accountingData as $transaction)
                        @php $isPaid = trim($transaction->Status ?? '') === 'Paid'; @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $transaction->id }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $transaction->date ?: '—' }}</td>
                            <td class="px-4 py-3" data-delete-label>
                                @if(Auth::user()->can('transactions.show'))
                                    <a href="{{ route('accounting.show', ['accounting' => $transaction->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $transaction->invoice_no ?: '—' }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $transaction->invoice_no ?: '—' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $transaction->tourName ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $transaction->clientName ?: '—' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $transaction->amount_receiveable ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if(!is_null($transaction->Status ?? null))
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $isPaid ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700' }}">
                                        {{ $transaction->Status }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('transactions.show'))
                                        <a href="{{ route('accounting.show', ['accounting' => $transaction->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('transactions.edit'))
                                        <a href="{{ route('accounting.edit', ['accounting' => $transaction->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('transactions.destroy'))
                                        <button type="button"
                                                onclick="accountingDeleteConfirm({{ $transaction->id }}, @js($transaction->invoice_no ?: ('invoice #'.$transaction->id)))"
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

        @if(method_exists($accountingData, 'hasPages') && $accountingData->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 bg-slate-50 flex items-center justify-between gap-3 text-sm text-slate-600">
                <div>
                    Showing <span class="font-medium text-slate-900">{{ $accountingData->firstItem() }}</span>–<span class="font-medium text-slate-900">{{ $accountingData->lastItem() }}</span>
                    of <span class="font-medium text-slate-900">{{ $accountingData->total() }}</span>
                </div>
                <div>{{ $accountingData->onEachSide(1)->links() }}</div>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- Mobile card list (below md) --}}
    {{-- ============================================================ --}}
    <div id="accounting-cards" class="md:hidden space-y-3">
        @foreach($accountingData as $transaction)
            @php $isPaid = trim($transaction->Status ?? '') === 'Paid'; @endphp
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('transactions.show'))
                            <a href="{{ route('accounting.show', ['accounting' => $transaction->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $transaction->invoice_no ?: ('Invoice #'.$transaction->id) }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $transaction->invoice_no ?: ('Invoice #'.$transaction->id) }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $transaction->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('transactions.edit'))
                            <a href="{{ route('accounting.edit', ['accounting' => $transaction->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        @if(Auth::user()->can('transactions.destroy'))
                            <button type="button"
                                    onclick="accountingDeleteConfirm({{ $transaction->id }}, @js($transaction->invoice_no ?: ('invoice #'.$transaction->id)))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                    aria-label="Delete">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        @endif
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if(!is_null($transaction->Status ?? null))
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Status</dt>
                            <dd>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $isPaid ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700' }}">
                                    {{ $transaction->Status }}
                                </span>
                            </dd>
                        </div>
                    @endif
                    @if($transaction->tourName)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Tour</dt>
                            <dd class="text-slate-700">{{ $transaction->tourName }}</dd>
                        </div>
                    @endif
                    @if($transaction->clientName)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Client</dt>
                            <dd class="text-slate-700">{{ $transaction->clientName }}</dd>
                        </div>
                    @endif
                    @if(!is_null($transaction->amount_receiveable ?? null))
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Receivable</dt>
                            <dd class="text-slate-900 font-medium">{{ $transaction->amount_receiveable }}</dd>
                        </div>
                    @endif
                    @if($transaction->date)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Date</dt>
                            <dd class="text-slate-700">{{ $transaction->date }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>

@endif

{{-- ============================================================ --}}
{{-- Delete confirmation modal (Tailwind, vanilla JS) --}}
{{-- ============================================================ --}}
<div id="accountingDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="accountingDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete client invoice?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="accountingDeleteName" class="text-slate-700"></strong>.
                        This will remove the invoice and its payment records. This action cannot be undone.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="accountingDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="accountingDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
                    <x-ui.icon name="trash-2" size="sm" />
                    Delete invoice
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
            initializeBootstrapTable('transactions-table');
        }
    });

    window.filterCards = function (containerId, value) {
        var c = document.getElementById(containerId);
        if (!c) return;
        var q = (value || '').toLowerCase();
        c.querySelectorAll('[data-card-row]').forEach(function (row) {
            var name = (row.querySelector('[data-card-name]')?.textContent || '').toLowerCase();
            row.style.display = (!q || name.indexOf(q) !== -1) ? '' : 'none';
        });
    };

    window.accountingDeleteConfirm = function (id, name) {
        var modal = document.getElementById('accountingDeleteModal');
        var label = document.getElementById('accountingDeleteName');
        var btn   = document.getElementById('accountingDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('invoice #' + id);
        btn.href = '{{ url("accounting") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.accountingDeleteCancel = function () {
        var modal = document.getElementById('accountingDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.accountingDeleteCancel();
    });
</script>
@endpush
