@extends('scaffold-interface.layouts.tabler-app')
@section('title','Quotations')

@section('content')
{{-- This view is filed under resources/views/guest_list/ for historical
     reasons but actually renders the quotation list (it Ajax-loads
     route('quotation.data')). Kept here for route compatibility. --}}

<x-ui.page-header
    title="Quotations"
    description="All quotations across tours."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Quotations'],
    ]"
/>

<div class="space-y-4">

    {{-- ─── Filters card ──────────────────────────────────────────────── --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle">
        <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
            <div>
                <label for="quotation-search" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-ui.icon name="search" size="sm" />
                    </span>
                    <input type="text"
                           id="quotation-search"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                           placeholder="Search quotations…"
                           onkeyup="filterTable('quotation-table', this.value)">
                </div>
            </div>
            <div class="sm:text-right">
                <button type="button"
                        onclick="exportTableToCSV('quotation-table', 'quotations_export.csv')"
                        class="inline-flex h-9 items-center justify-center gap-1.5 rounded bg-success-600 px-3 text-sm font-medium text-white hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-success-600/30 focus:ring-offset-1">
                    <x-ui.icon name="download" size="sm" />
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Quotations table card ─────────────────────────────────────── --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
        <div class="overflow-x-auto">
            <table id="quotation-table" class="table table-striped table-bordered table-hover bootstrap-table w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th onclick="sortTable(0, 'quotation-table')" class="px-3 py-3 cursor-pointer hover:bg-slate-100" style="width:60px">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(1, 'quotation-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Name') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(2, 'quotation-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Tour') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(3, 'quotation-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Assigned') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(4, 'quotation-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.CreatedAt') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th class="px-4 py-3" style="width:120px">{!! trans('main.Frontsheet') !!}</th>
                        <th class="px-4 py-3 text-right" style="width:140px">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">
                            <span class="inline-flex items-center gap-2">
                                <x-ui.icon name="loader-2" class="animate-spin text-slate-400" />
                                Loading…
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('quotation-table');

    // Load data via AJAX
    $.get("{{route('quotation.data')}}", function(data) {
        const tbody = $('#quotation-table tbody');
        tbody.empty();

        if(data.data && data.data.length > 0) {
            data.data.forEach(function(row) {
                tbody.append(`
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-3 text-sm text-slate-700 font-mono">${row.id}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900">${row.name}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">${row.tour_name}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">${row.user_name}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">${row.created_at}</td>
                        <td class="px-4 py-3 text-sm">${row.comparison}</td>
                        <td class="px-4 py-3 text-right">${row.action}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">No quotations found</td></tr>');
        }
    }).fail(function() {
        $('#quotation-table tbody').html('<tr><td colspan="7" class="px-4 py-12 text-center text-sm text-danger-700">Error loading data</td></tr>');
    });
});
</script>
@endpush
