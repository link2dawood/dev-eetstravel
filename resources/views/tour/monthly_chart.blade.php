@extends('scaffold-interface.layouts.tabler-app')
@section('title','Monthly chart')

@section('content')
<x-ui.page-header
    title="Monthly chart"
    description="Tours grouped by month — filter by year or month and search by name."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tours', 'href' => route('tour.index')],
        ['label' => 'Monthly chart'],
    ]"
>
    <x-slot name="actions">
        {{-- The legacy create-button helper renders its own anchor; wrap it
             so the row still aligns with the rest of the page-header actions.
             Identifier #tour_create is preserved for the existing JS hook. --}}
        <div id="tour_create" class="inline-flex">
            {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class) !!}
        </div>
    </x-slot>
</x-ui.page-header>

<div class="space-y-4">

    {{-- ─── Filters card ──────────────────────────────────────────────── --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-between gap-3">
            <h2 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                <x-ui.icon name="filter" class="text-slate-500" />
                Filters
            </h2>
            <span id="help" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-700 cursor-pointer relative">
                <x-ui.icon name="help-circle" size="sm" />
                <span class="hidden sm:inline">Legend</span>
                @include('legend.tour_legend')
            </span>
        </div>

        <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-3">
                <label for="year-filter" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Year</label>
                <select id="year-filter" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <option value="">All years</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label for="month-filter" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Month</label>
                <select id="month-filter" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <option value="">All months</option>
                    @foreach ($months as $key => $month)
                        <option value="{{ $key }}">{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-4">
                <label for="tour-search" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-ui.icon name="search" size="sm" />
                    </span>
                    <input
                        type="text"
                        id="tour-search"
                        class="form-control block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                        placeholder="Search tours…"
                        onkeyup="filterTable('monthly-chart-table', this.value)"
                    >
                </div>
            </div>
            <div class="lg:col-span-2">
                <button
                    type="button"
                    onclick="exportTableToCSV('monthly-chart-table', 'tours_export.csv')"
                    class="inline-flex w-full h-9 items-center justify-center gap-1.5 rounded bg-success-600 px-3 text-sm font-medium text-white hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-success-600/30 focus:ring-offset-1"
                >
                    <x-ui.icon name="download" size="sm" />
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Tours table card ──────────────────────────────────────────── --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
        <div class="overflow-x-auto">
            <table id="monthly-chart-table" class="table table-striped table-bordered table-hover bootstrap-table w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th onclick="sortTable(0, 'monthly-chart-table')" class="px-3 py-3 cursor-pointer hover:bg-slate-100" style="width: 60px">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(1, 'monthly-chart-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Name') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(2, 'monthly-chart-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.DepDate') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(3, 'monthly-chart-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.CountryBegin') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(4, 'monthly-chart-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.CityBegin') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(5, 'monthly-chart-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.Status') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th onclick="sortTable(6, 'monthly-chart-table')" class="px-4 py-3 cursor-pointer hover:bg-slate-100">
                            <span class="inline-flex items-center gap-1">{!! trans('main.ExternalName') !!} <x-ui.icon name="arrows-up-down" size="xs" /></span>
                        </th>
                        <th class="px-4 py-3 text-right" style="width: 140px;">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-sm text-slate-500">
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

{{-- ─── Clone-tour modal (Bootstrap modal API — required by legacy JS) ─── --}}
<div class="modal fade" id="tour-clone-modal" tabindex="-1" role="dialog" aria-labelledby="tour-clone-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                <h4 id="tour-clone-label" class="text-sm font-semibold text-slate-900">{!! trans('main.CloneTour') ?? 'Clone tour' !!}</h4>
                <button type="button" class="close text-slate-400 hover:text-slate-700" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="p-5">
                {{-- block-error retains its legacy class hook; the JS toggles display style. --}}
                <div class="alert alert-info block-error hidden mb-4 rounded border border-info-600/20 bg-info-50 px-4 py-2 text-sm text-info-700 text-center"></div>

                <form id="tour-clone-modal-form" class="space-y-4">
                    <div>
                        <label for="departure_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                            {!! trans('main.DepartureDate') !!}
                        </label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="calendar" size="sm" />
                            </span>
                            {!! Form::text('departure_date', '', [
                                'class' => 'form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                                'id' => 'departure_date',
                                'autocomplete' => 'off',
                            ]) !!}
                        </div>
                    </div>
                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" id="clone_tour_send" class="btn btn-success pre-loader-func inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm font-medium text-white hover:bg-primary-700">
                            <x-ui.icon name="copy" size="sm" />
                            {!! trans('main.Submit') !!}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ─── Tour status error modal (Bootstrap modal API — required by JS) ─── --}}
<div class="modal fade" tabindex="-1" id="error_tour">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_confirmed_hotel">
                <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h4 class="modal-title text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <x-ui.icon name="alert-triangle" class="text-warning-600" />
                        {!! trans('main.Warning') !!}
                    </h4>
                    <button type="button" class="close text-slate-400 hover:text-slate-700" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-5">
                    <h3 class="error_tour_message text-sm text-slate-700 leading-relaxed"></h3>
                </div>
                <div class="modal-footer border-t border-slate-200 px-5 py-3 flex items-center justify-end gap-2 bg-slate-50">
                    <div class="btn-send-confirmed_hotel">
                        <button type="reset" class="btn btn-success modal-close inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm font-medium text-white hover:bg-primary-700" data-dismiss="modal">
                            OK
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<span id="permission" data-permission="{{ \App\Helper\PermissionHelper::checkPermission('tour.edit') }}"></span>

@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    let permission = $('#permission').attr('data-permission');
    let classNameStatus = permission ? 'touredit-status' : '';

    initializeBootstrapTable('monthly-chart-table');

    function loadTourData() {
        const year = $('#year-filter').val();
        const month = $('#month-filter').val();

        $.get("{{route('monthly_chart_data')}}", { year: year, month: month })
        .done(function(data) {
            const tbody = $('#monthly-chart-table tbody');
            tbody.empty();

            if(data.data && data.data.length > 0) {
                data.data.forEach(function(row) {
                    let rowClass = '';
                    switch(row.status_name) {
                        case 'Pending':
                            rowClass = 'style="background: rgb(255 249 176)"';
                            break;
                        case 'Cancelled':
                            rowClass = 'style="background: #ffbbb2"';
                            break;
                        case 'Confirmed':
                            rowClass = 'style="background: rgb(159 255 135)"';
                            break;
                        default:
                            rowClass = 'style="background: rgb(202 255 189)"';
                            break;
                    }

                    let statusCellClass = permission ? 'touredit-status' : '';
                    let statusCellAttr = permission ? `data-status-link="{{ route('tour.update', ['tour' => '__ID__']) }}".replace('__ID__', '${row.id}')` : '';

                    tbody.append(`
                        <tr ${rowClass}>
                            <td class="px-3 py-3 text-sm text-slate-700">${row.id}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900 touredit-name">${row.name}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 touredit-departure_date">${row.departure_date}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 touredit-country_begin">${row.country_begin}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 touredit-city_begin">${row.city_begin}</td>
                            <td class="px-4 py-3 text-sm ${statusCellClass}" ${statusCellAttr}>${row.status_name}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 touredit-external_name">${row.external_name}</td>
                            <td class="px-4 py-3 text-right">${row.action}</td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="8" class="px-4 py-12 text-center text-sm text-slate-500">No tours found</td></tr>');
            }
        })
        .fail(function() {
            $('#monthly-chart-table tbody').html('<tr><td colspan="8" class="px-4 py-12 text-center text-sm text-danger-700">Error loading data</td></tr>');
        });
    }

    // Event delegation for delete buttons - placed outside loadTourData
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const button = $(this);
        const deleteUrl = button.data('url');

        if (!deleteUrl) {
            console.error('Delete URL not found');
            return false;
        }

        if (confirm('Are you sure you want to delete this tour?')) {
            button.prop('disabled', true);

            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Tour deleted successfully!');
                        loadTourData();
                    } else {
                        alert('Error deleting tour');
                        button.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    alert('Error deleting tour: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    button.prop('disabled', false);
                }
            });
        }

        return false;
    });

    // Load initial data
    loadTourData();

    // Reload data when filters change
    $('#year-filter, #month-filter').on('change', function() {
        loadTourData();
    });
});
</script>
@endpush
