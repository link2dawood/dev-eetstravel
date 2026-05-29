@extends('scaffold-interface.layouts.tabler-app')
@section('title','Supplier Invoices')

@section('content')
<x-ui.page-header
    title="Supplier Invoices"
    description="Invoices received from suppliers, linked to tours and services."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Supplier Invoices'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('invoices.create'))
            <x-ui.button as="a" href="{{ route('invoices.create') }}" icon="plus">
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

{{-- Toolbar: search + export. Search filters client-side via filterTable() from
     js/bootstrap-tables.js (per visible page). Export preserves exportTableToCSV(). --}}
<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text"
                   id="invoices-search"
                   onkeyup="filterTable('inovices-table', this.value); filterCards('invoices-cards', this.value);"
                   placeholder="Search invoices on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('inovices-table', 'supplier_invoices_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($invoicesData) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="file-text"
            title="No invoices yet"
            message="Supplier invoices linked to tours will appear here.">
            @if(Auth::user()->can('invoices.create'))
                <x-ui.button as="a" href="{{ route('invoices.create') }}" icon="plus">Add an invoice</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- ============================================================ --}}
    {{-- Desktop table (md and up) --}}
    {{-- ============================================================ --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="inovices-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">Invoice No <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">Due Date <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">Received <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">Tour <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">Service <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">Office <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(7, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">Total <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(8, 'inovices-table')">
                            <span class="inline-flex items-center gap-1">Status <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoicesData as $invoice)
                        @php $isPaid = trim($invoice->status ?? '') === 'Paid'; @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $invoice->id }}</td>
                            <td class="px-4 py-3" data-delete-label>
                                @if(Auth::user()->can('invoices.show'))
                                    <a href="{{ route('invoices.show', ['invoice' => $invoice->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $invoice->invoice_no ?: '—' }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $invoice->invoice_no ?: '—' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $invoice->dueDate ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $invoice->receivedDate ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $invoice->tour ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $invoice->package ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $invoice->officeName ?: '—' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $invoice->total_amount ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if(!is_null($invoice->status ?? null))
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $isPaid ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700' }}">
                                        {{ $invoice->status }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('invoices.show'))
                                        <a href="{{ route('invoices.show', ['invoice' => $invoice->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('invoices.edit'))
                                        <a href="{{ route('invoices.edit', ['invoice' => $invoice->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('invoices.destroy'))
                                        <button type="button"
                                                onclick="invoiceDeleteConfirm({{ $invoice->id }}, @js($invoice->invoice_no ?: ('invoice #'.$invoice->id)))"
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
    <div id="invoices-cards" class="md:hidden space-y-3">
        @foreach($invoicesData as $invoice)
            @php $isPaid = trim($invoice->status ?? '') === 'Paid'; @endphp
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('invoices.show'))
                            <a href="{{ route('invoices.show', ['invoice' => $invoice->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $invoice->invoice_no ?: ('Invoice #'.$invoice->id) }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $invoice->invoice_no ?: ('Invoice #'.$invoice->id) }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $invoice->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('invoices.edit'))
                            <a href="{{ route('invoices.edit', ['invoice' => $invoice->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        @if(Auth::user()->can('invoices.destroy'))
                            <button type="button"
                                    onclick="invoiceDeleteConfirm({{ $invoice->id }}, @js($invoice->invoice_no ?: ('invoice #'.$invoice->id)))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                    aria-label="Delete">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        @endif
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if(!is_null($invoice->status ?? null))
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Status</dt>
                            <dd>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $isPaid ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700' }}">
                                    {{ $invoice->status }}
                                </span>
                            </dd>
                        </div>
                    @endif
                    @if($invoice->tour)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Tour</dt>
                            <dd class="text-slate-700">{{ $invoice->tour }}</dd>
                        </div>
                    @endif
                    @if($invoice->package)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Service</dt>
                            <dd class="text-slate-700">{{ $invoice->package }}</dd>
                        </div>
                    @endif
                    @if($invoice->officeName)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Office</dt>
                            <dd class="text-slate-700">{{ $invoice->officeName }}</dd>
                        </div>
                    @endif
                    @if(!is_null($invoice->total_amount ?? null))
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Total</dt>
                            <dd class="text-slate-900 font-medium">{{ $invoice->total_amount }}</dd>
                        </div>
                    @endif
                    @if($invoice->dueDate)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Due</dt>
                            <dd class="text-slate-700">{{ $invoice->dueDate }}</dd>
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
<div id="invoiceDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="invoiceDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete invoice?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="invoiceDeleteName" class="text-slate-700"></strong>.
                        This will remove the invoice and its link to the tour. This action cannot be undone.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="invoiceDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="invoiceDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
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
            initializeBootstrapTable('inovices-table');
        }
    });

    // Filter the mobile card list by matching the visible invoice name. Mirrors
    // filterTable() from bootstrap-tables.js so search behaves the same on phones.
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
    window.invoiceDeleteConfirm = function (id, name) {
        var modal = document.getElementById('invoiceDeleteModal');
        var label = document.getElementById('invoiceDeleteName');
        var btn   = document.getElementById('invoiceDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('invoice #' + id);
        btn.href = '{{ url("invoices") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.invoiceDeleteCancel = function () {
        var modal = document.getElementById('invoiceDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.invoiceDeleteCancel();
    });
</script>
@endpush
