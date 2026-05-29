@extends('scaffold-interface.layouts.tabler-app')
@section('title','Currencies')

@section('content')
<x-ui.page-header
    title="Currencies"
    description="Currency codes used throughout invoicing and reporting."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Currencies'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('currencies.create'))
            <x-ui.button as="a" href="{{ route('currencies.create') }}" icon="plus">New currency</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text" id="currencies-search" onkeyup="filterTable('currencies-table', this.value)"
                   placeholder="Search currencies..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <x-ui.button variant="secondary" icon="download" size="sm" onclick="exportTableToCSV('currencies-table', 'currencies_export.csv')">
            Export CSV
        </x-ui.button>
    </div>
</div>

@if(count($currencies) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="circle-dollar-sign" title="No currencies yet" message="Add your first currency to enable multi-currency support.">
            @if(Auth::user()->can('currencies.create'))
                <x-ui.button as="a" href="{{ route('currencies.create') }}" icon="plus">New currency</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="currencies-table" class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(0, 'currencies-table')">ID</th>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(1, 'currencies-table')">{!! trans('main.Name') !!}</th>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(2, 'currencies-table')">{!! trans('main.Code') !!}</th>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(3, 'currencies-table')">{!! trans('main.Symbol') !!}</th>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(4, 'currencies-table')">{!! trans('main.Cent') !!}</th>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(5, 'currencies-table')">{!! trans('main.SymbolCent') !!}</th>
                        <th class="px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($currencies as $currency)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $currency->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $currency->name }}</td>
                            <td class="px-4 py-3 font-mono text-slate-700">{{ $currency->code }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $currency->symbol }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $currency->cent }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $currency->symbol_cent }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('currencies.show'))
                                        <a href="{{ route('currencies.show', ['currency' => $currency->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700" title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('currencies.edit'))
                                        <a href="{{ route('currencies.edit', ['currency' => $currency->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700" title="Edit">
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

    @if(method_exists($currencies, 'links'))
        <div class="mt-4">{{ $currencies->links() }}</div>
    @endif
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function () {
    if (typeof initializeBootstrapTable === 'function') initializeBootstrapTable('currencies-table');
});
</script>
@endpush
