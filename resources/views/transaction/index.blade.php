@extends('scaffold-interface.layouts.tabler-app')
@section('title','Transactions')

@section('content')
<x-ui.page-header
    title="Customer transactions"
    description="Payment movements recorded against client invoices."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tours', 'href' => route('tour.index')],
        ['label' => 'Transactions'],
    ]"
>
    <x-slot name="actions">
        {{-- #tour_create preserved for the legacy PermissionHelper anchor wrapper. --}}
        <div id="tour_create" class="inline-flex">
            {!! \App\Helper\PermissionHelper::getCreateButton(route('transaction.create'), \App\Invoices::class) !!}
        </div>
    </x-slot>
</x-ui.page-header>

<div class="space-y-4">

    @if(session('message_buses'))
        <div class="rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700 text-center">
            {{ session('message_buses') }}
        </div>
    @endif

    {{-- ─── Filters card ──────────────────────────────────────────────── --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle">
        <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
            <div>
                <label for="transaction-search" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-ui.icon name="search" size="sm" />
                    </span>
                    <input
                        type="text"
                        id="transaction-search"
                        class="form-control block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                        placeholder="Search transactions…"
                        onkeyup="filterTable('inovices-table', this.value)"
                    >
                </div>
            </div>
            <div class="sm:text-right">
                <button
                    type="button"
                    onclick="exportTableToCSV('inovices-table', 'transactions_export.csv')"
                    class="inline-flex h-9 items-center justify-center gap-1.5 rounded bg-success-600 px-3 text-sm font-medium text-white hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-success-600/30 focus:ring-offset-1"
                >
                    <x-ui.icon name="download" size="sm" />
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Transactions table card ───────────────────────────────────── --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
        <div class="overflow-x-auto">
            {{-- IDs and classes (#inovices-table, .bootstrap-table, .table-*) preserved
                 because bootstrap-tables.js / sortTable / filterTable / exportTableToCSV
                 all key off them. --}}
            <table id="inovices-table" class="table table-striped table-bordered table-hover bootstrap-table w-full text-sm" style="background:#fff;">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th onclick="sortTable(0, 'inovices-table')" class="px-3 py-3 cursor-pointer hover:bg-slate-100" style="width:60px">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(1, 'inovices-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">Date <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(2, 'inovices-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">Payment to <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(3, 'inovices-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">Transaction no <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(4, 'inovices-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">Invoice no <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(5, 'inovices-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100 text-right">
                            <span class="inline-flex items-center gap-1">Amount <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(6, 'inovices-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100 text-right">
                            <span class="inline-flex items-center gap-1">Unallocated <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th class="actions-button px-4 py-3 text-right" style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($transactionsData as $transaction)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-3 text-sm text-slate-700 font-mono">{{ $transaction->id }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $transaction->date }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $transaction->pay_to }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-mono">{{ $transaction->transaction_no }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-mono">{{ $transaction->invoice_no }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900 text-right font-mono">{{ $transaction->amount }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 text-right font-mono">{{ $transaction->unallocated }}</td>
                        <td class="px-4 py-3 text-right">{!! $transaction->action_buttons !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-sm text-slate-500">
                            <span class="inline-flex items-center gap-2 text-slate-500">
                                <x-ui.icon name="receipt" class="text-slate-400" />
                                No transactions found
                            </span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('inovices-table');
    });
</script>
@endpush
