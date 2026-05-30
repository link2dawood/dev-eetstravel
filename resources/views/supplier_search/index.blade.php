@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Supplier search')

@section('post_styles')
    @include('component.datatables_cdn')
    <style>
        .table-responsive { overflow-x: auto; }
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: left;
                margin-bottom: 1rem;
            }
        }
    </style>
@endsection

@section('content')
<x-ui.page-header
    title="Supplier search"
    description="Find hotels, guides, restaurants, events, and bus companies across countries and cities."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Supplier search'],
    ]"
>
    <x-slot name="actions">
        <button type="button" class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50" data-bs-toggle="modal" data-bs-target="#helpModal">
            <x-ui.icon name="help-circle" size="sm" /> Help
        </button>
        @include('legend.supplier_search')
    </x-slot>
</x-ui.page-header>

{{-- Search filters --}}
<div class="rounded border border-slate-200 bg-white mb-4">
    <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
        <x-ui.icon name="filter" size="sm" class="text-slate-400" />
        <h2 class="text-sm font-medium text-slate-700">Search filters</h2>
    </div>
    <form action="{{ route('supplier_show') }}" class="px-5 py-5">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Supplier name --}}
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Supplier name</label>
                <div class="relative">
                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><x-ui.icon name="search" size="sm" /></span>
                    <input type="text" id="searchTextField" placeholder="Search by name…" value=""
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                </div>
            </div>

            {{-- Service type --}}
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Service type</label>
                <select id="service-select"
                        class="form-select form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <option selected>{!! trans('main.All') !!}</option>
                    @foreach($options as $option)
                        <option>@if($option === 'Transfer') Bus Company @else {{ $option }} @endif</option>
                    @endforeach
                </select>
            </div>

            {{-- Country --}}
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Country</label>
                {!! Form::select('country', \App\Helper\Choices::getCountriesSupplierSearchArray(), '', ['class' => 'form-select form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'country']) !!}
            </div>

            {{-- City --}}
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">City</label>
                <input id="city" name="city" type="text" value="" placeholder="Enter city…"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                <input type="hidden" name="city_code" id="city_code" value="">
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <button type="button" id="supplierSearchButton"
                    class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm font-medium text-white hover:bg-primary-700">
                <x-ui.icon name="search" size="sm" /> Search suppliers
            </button>
            <button type="button" onclick="document.querySelector('form').reset(); $('#searchTextField').focus();"
                    class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                <x-ui.icon name="x" size="sm" /> Clear
            </button>
        </div>
    </form>
</div>

{{-- Inline-driver/transfer error banner — toggled by supplier-search.js --}}
<div class="alert alert-info block-error-driver-transfer mb-4 hidden rounded border border-info-200 bg-info-50 px-4 py-3 text-sm text-info-700" role="alert">
    <div class="flex items-start gap-2">
        <x-ui.icon name="info" class="mt-0.5 text-info-600 shrink-0" />
        <div class="flex-1"></div>
        <button type="button" onclick="this.parentElement.parentElement.style.display='none'"
                class="btn-close text-info-600 hover:text-info-700"><x-ui.icon name="x" size="sm" /></button>
    </div>
</div>

{{-- Results table --}}
<div class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
        <x-ui.icon name="list" size="sm" class="text-slate-400" />
        <h3 class="text-sm font-medium text-slate-700">Search results</h3>
    </div>
    <div class="p-5">
        <div class="table-responsive overflow-x-auto">
            <table id="search-table" class="min-w-full divide-y divide-slate-200 text-sm" style="width:100%">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-3 py-2">{!! trans('main.Name') !!}</th>
                        <th class="px-3 py-2">{!! trans('main.Address') !!}</th>
                        <th class="px-3 py-2">{!! trans('main.Country') !!}</th>
                        <th class="px-3 py-2">{!! trans('main.City') !!}</th>
                        <th class="px-3 py-2">{!! trans('main.Phone') !!}</th>
                        <th class="px-3 py-2">{!! trans('main.ContactName') !!}</th>
                        <th class="px-3 py-2 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- ---------------------------------------------------------------------- --}}
{{-- Modals — Bootstrap 5 data-bs-* selectors preserved. JS in supplier-search.js
     and inline below toggles visibility & populates fields. --}}
{{-- ---------------------------------------------------------------------- --}}

{{-- Add-to-tour modal --}}
<div class="modal modal-blur fade" id="addTourModal" tabindex="-1" aria-labelledby="addTourLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                <h5 class="modal-title text-sm font-medium text-slate-700 inline-flex items-center gap-2" id="addTourLabel">
                    <x-ui.icon name="plus" size="sm" /> {!! trans('main.AddforTour') !!}
                </h5>
                <button type="button" class="btn-close text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close"><x-ui.icon name="x" size="sm" /></button>
            </div>
            <div class="modal-body px-5 py-4">
                <div class="table-responsive overflow-x-auto">
                    <table id="tour-table" class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">ID</th>
                                <th class="px-3 py-2">{!! trans('main.Name') !!}</th>
                                <th class="px-3 py-2 hidden md:table-cell">{!! trans('main.DepDate') !!}</th>
                                <th class="px-3 py-2 hidden md:table-cell">{!! trans('main.Retdate') !!}</th>
                                <th class="px-3 py-2 hidden sm:table-cell">Pax</th>
                                <th class="px-3 py-2 text-right">{!! trans('main.Choose') !!}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Select-date modal (for hotel etc.) --}}
<div class="modal modal-blur fade" id="selectDateForTour" tabindex="-1" aria-labelledby="selectDateForTourLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                <h5 class="modal-title text-sm font-medium text-slate-700 inline-flex items-center gap-2" id="selectDateForTourLabel">
                    <x-ui.icon name="calendar" size="sm" /> {!! trans('main.SelectDate') !!}
                </h5>
                <button type="button" class="btn-close text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close"><x-ui.icon name="x" size="sm" /></button>
            </div>
            <div class="modal-body px-5 py-4 space-y-3">
                <div class="alert alert-info error_date hidden rounded border border-info-200 bg-info-50 px-3 py-2 text-sm text-info-700" role="alert">
                    <div class="flex items-start gap-2">
                        <x-ui.icon name="info" class="mt-0.5 text-info-600 shrink-0" />
                        <span></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateFrom') !!}</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><x-ui.icon name="calendar" size="sm" /></span>
                        {!! Form::text('date_service', '', [
                            'class' => 'form-control datepickerDisabled block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'date_service',
                            'placeholder' => 'Select date from'
                        ]) !!}
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateTo') !!}</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><x-ui.icon name="calendar" size="sm" /></span>
                        {!! Form::text('date_service_retirement', '', [
                            'class' => 'form-control datepickerDisabled block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'date_service_retirement',
                            'disabled' => true,
                            'placeholder' => 'Select date to'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2">
                <button type="button" data-bs-dismiss="modal"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                    <x-ui.icon name="x" size="sm" /> Cancel
                </button>
                <button class="addTourWithDate pre-loader-func inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700" type="button">
                    <x-ui.icon name="check" size="sm" /> {!! trans('main.Add') !!}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Select driver + bus modal --}}
<div class="modal modal-blur fade" id="select-driver-and-bus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <form id="form_transfer_buses_drivers">
                <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h5 class="modal-title text-sm font-medium text-slate-700 inline-flex items-center gap-2">
                        <x-ui.icon name="bus" size="sm" /> {!! trans('main.Selectdriversandbuses') !!}
                    </h5>
                    <button type="button" class="btn-close text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close"><x-ui.icon name="x" size="sm" /></button>
                </div>
                <div class="modal-body px-5 py-4 space-y-3 relative">
                    <div class="alert alert-info block-error-driver hidden rounded border border-info-200 bg-info-50 px-3 py-2 text-sm text-info-700" role="alert">
                        <div class="flex items-start gap-2">
                            <x-ui.icon name="info" class="mt-0.5 text-info-600 shrink-0" />
                            <span></span>
                        </div>
                    </div>
                    <div class="list-driver-and-buses"></div>
                    <div class="overlay hidden absolute inset-0 items-center justify-center bg-white/70">
                        <div class="spinner-border text-primary-600" role="status">
                            <span class="visually-hidden">Loading…</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2">
                    <button type="button" data-bs-dismiss="modal"
                            class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="x" size="sm" /> Cancel
                    </button>
                    <div class="btn-send-driver">
                        <button type="button" class="btn-send-transfer_add inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">
                            <x-ui.icon name="check" size="sm" /> {!! trans('main.Add') !!}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Transfer-package date modal --}}
<div class="modal modal-blur fade" id="selectDateForTransferPackage" tabindex="-1" aria-labelledby="selectDateForTransferPackageLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                <h5 class="modal-title text-sm font-medium text-slate-700 inline-flex items-center gap-2">
                    <x-ui.icon name="calendar" size="sm" /> {!! trans('main.SelectDate') !!}
                </h5>
                <button type="button" class="btn-close text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close"><x-ui.icon name="x" size="sm" /></button>
            </div>
            <div class="modal-body px-5 py-4 space-y-3">
                <div class="alert alert-info error_date hidden rounded border border-info-200 bg-info-50 px-3 py-2 text-sm text-info-700" role="alert">
                    <div class="flex items-start gap-2">
                        <x-ui.icon name="info" class="mt-0.5 text-info-600 shrink-0" />
                        <span></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateFrom') !!}</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><x-ui.icon name="calendar" size="sm" /></span>
                        {!! Form::text('date_service_package', '', [
                            'class' => 'form-control datepickerDisabledTransferPackage block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'date_service_transfer_package',
                            'placeholder' => 'Select date from'
                        ]) !!}
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateTo') !!}</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><x-ui.icon name="calendar" size="sm" /></span>
                        {!! Form::text('date_service_retirement_package', '', [
                            'class' => 'form-control datepickerDisabledTransferPackage block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'date_service_transfer_retirement_package',
                            'placeholder' => 'Select date to'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2">
                <button type="button" data-bs-dismiss="modal"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                    <x-ui.icon name="x" size="sm" /> Cancel
                </button>
                <button class="addTransferPackageWithDate inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700" type="button">
                    <x-ui.icon name="arrow-right" size="sm" /> {!! trans('main.Next') !!}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- DataTables scripts loaded via centralized loader (window.loadDataTables) --}}

<script type="text/javascript">
    $(document).ready(function () {
        setTimeout(function () { $('#searchTextField').focus(); }, 1000);
    });
</script>
<script type="text/javascript">
    // Override DataTables initialization to add responsive features
    (function () {
        const originalGenerateTable = globalSearch.generateTable;
        globalSearch.generateTable = function (service_select = null) {
            // Service-create block visibility toggles preserved exactly as-is.
            if (this.service == "Hotel") {
                $("#hotel_service_create").css("display", "block");
                $("#guide_service_create").css("display", "none");
                $("#event_service_create").css("display", "none");
                $("#res_service_create").css("display", "none");
                $("#bus_service_create").css("display", "none");
            } else if (this.service == "Guide") {
                $("#guide_service_create").css("display", "block");
                $("#hotel_service_create").css("display", "none");
                $("#event_service_create").css("display", "none");
                $("#res_service_create").css("display", "none");
                $("#bus_service_create").css("display", "none");
            } else if (this.service == "Event") {
                $("#event_service_create").css("display", "block");
                $("#guide_service_create").css("display", "none");
                $("#hotel_service_create").css("display", "none");
                $("#res_service_create").css("display", "none");
                $("#bus_service_create").css("display", "none");
            } else if (this.service == "Transfer") {
                $("#bus_service_create").css("display", "block");
                $("#guide_service_create").css("display", "none");
                $("#hotel_service_create").css("display", "none");
                $("#res_service_create").css("display", "none");
                $("#event_service_create").css("display", "none");
            } else {
                $("#res_service_create").css("display", "block");
                $("#guide_service_create").css("display", "none");
                $("#hotel_service_create").css("display", "none");
                $("#event_service_create").css("display", "none");
                $("#bus_service_create").css("display", "none");
            }

            let table = $('#search-table').DataTable({
                responsive: true,
                dom: "<'row'<'col-md-6'l><'col-md-6'f>>" +
                     "<'row'<'col-12'tr>>" +
                     "<'row'<'col-md-6'i><'col-md-6'p>>",
                processing: true,
                serverSide: true,
                pageLength: 50,
                order: [],
                ajax: {
                    url: "/supplier_show",
                    data: {
                        service: this.service,
                        actionColumn: this.actionColumn,
                        criterias: this.criterias,
                        rates: this.rate,
                        city_code: this.city_code,
                        countryalias: this.countryAlias,
                        searchname: this.searchName
                    }
                },
                columns: [
                    { data: 'nameService',  name: 'nameService',  responsivePriority: 1 },
                    { data: 'address_first', name: 'address_first', responsivePriority: 4 },
                    { data: 'country',       name: 'country',       responsivePriority: 5 },
                    { data: 'city',          name: 'city',          responsivePriority: 6 },
                    { data: 'work_phone',    name: 'work_phone',    responsivePriority: 2 },
                    { data: 'contact_name',  name: 'contact_name',  responsivePriority: 7 },
                    { data: this.actionColumn, sortable: false, responsivePriority: 3 }
                ],
                initComplete: function (settings, json) {
                    if (service_select) $(service_select).attr('disabled', false);
                    setTimeout(function () {
                        $('.dataTables_wrapper .row').addClass('g-2');
                        $('.dataTables_length').addClass('text-start');
                        $('.dataTables_filter').addClass('text-md-end');
                        $('.dataTables_info').addClass('text-start');
                        $('.dataTables_paginate').addClass('text-md-end');
                    }, 50);
                },
                rowCallback: function (row, data) {
                    var actionCell = $(row).find('td:last');
                    var anchorElement = actionCell.find('a.show-button');
                    var dataLink = anchorElement.attr('data-link');
                    if (dataLink !== undefined) {
                        $(row).on('click', function () { window.location.href = dataLink; });
                    }
                }
            });

            $('#search-table_filter').css('display', 'none');
            $('#search-table_filter').after('<label>City: <input type="text" id="city-search" class="ml-1 h-7 rounded border border-slate-300 bg-white px-2 text-sm"></label>');
            $('#search-table_filter').before('<label>Name: <input type="text" id="hotel-name-search" class="ml-1 h-7 rounded border border-slate-300 bg-white px-2 text-sm"></label>');

            $('#city-search').on('keyup', function () { table.column(3).search(this.value).draw(); });
            $('#hotel-name-search').on('keyup', function () { table.column(0).search(this.value).draw(); });
        };
    })();
</script>
<script type="text/javascript" src="{{ asset('js/supplier-search.js') }}"></script>
<script type="text/javascript">
    globalSearchApp.run();
</script>
@endpush
