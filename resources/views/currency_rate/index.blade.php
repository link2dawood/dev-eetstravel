@extends('scaffold-interface.layouts.tabler-app')
@section('title','Currency rates')

@section('content')
<x-ui.page-header
    title="Currency rates"
    description="Reference exchange rates used by quotations and invoices."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Currency rates'],
    ]"
>
    <x-slot name="actions">
        <div class="inline-flex">
            {!! \App\Helper\PermissionHelper::getCreateButton(route('currency_rate.create'), \App\CurrencyRate::class) !!}
        </div>
    </x-slot>
</x-ui.page-header>

<div class="space-y-4">

    {{-- ─── Filters card ──────────────────────────────────────────────── --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle">
        <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
            <div>
                <label for="currency-search" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-ui.icon name="search" size="sm" />
                    </span>
                    <input type="text"
                           id="currency-search"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                           placeholder="Search currency rates…"
                           onkeyup="filterTable('currency-table', this.value)">
                </div>
            </div>
            <div class="sm:text-right">
                <button type="button"
                        onclick="exportTableToCSV('currency-table', 'currency_rates_export.csv')"
                        class="inline-flex h-9 items-center justify-center gap-1.5 rounded bg-success-600 px-3 text-sm font-medium text-white hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-success-600/30 focus:ring-offset-1">
                    <x-ui.icon name="download" size="sm" />
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Currency rates table card ─────────────────────────────────── --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
        <div class="overflow-x-auto">
            <table id="currency-table" class="table table-striped table-bordered table-hover bootstrap-table w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th onclick="sortTable(0, 'currency-table')" class="px-3 py-3 cursor-pointer hover:bg-slate-100" style="width:60px">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(1, 'currency-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Currency') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(2, 'currency-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100 text-right">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Rate') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(3, 'currency-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Date') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th class="px-4 py-3 text-right" style="width:140px">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($currency_rates as $currency_rate)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-3 text-sm text-slate-700 font-mono">{{ $currency_rate->id }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $currency_rate->currency ?? '' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-900 text-right font-mono">{{ $currency_rate->rate ?? '' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $currency_rate->date ?? '' }}</td>
                            <td class="px-4 py-3 text-right">
                                {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                    'show' => route('currency_rate.show', ['currency_rate' => $currency_rate->id]),
                                    'edit' => route('currency_rate.edit', ['currency_rate' => $currency_rate->id]),
                                    'delete_msg' => "/currency_rate/{$currency_rate->id}/deleteMsg",
                                ], false, $currency_rate) !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">
                                <span class="inline-flex items-center gap-2">
                                    <x-ui.icon name="trending-up" class="text-slate-400" />
                                    No currency rates found
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($currency_rates, 'hasPages') && $currency_rates->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 bg-slate-50">
                {{ $currency_rates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('currency-table');
});
</script>
@endpush
