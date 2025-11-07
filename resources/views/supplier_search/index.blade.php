@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Supplier Search')

@section('post_styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    /* DataTables responsive styling for Tabler */
    .table-responsive {
        overflow-x: auto;
    }
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
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Global Search
                </div>
                <h2 class="page-title">
                    <i class="ti ti-search me-2"></i>Supplier Search
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button type="button" class="btn btn-ghost-secondary" data-bs-toggle="modal" data-bs-target="#helpModal">
                        <i class="ti ti-help me-1"></i>Help
                    </button>
                    @include('legend.supplier_search')
                </div>
            </div>
        </div>
    </div>

    {{-- Search Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Search Filters</h3>
        </div>
        <div class="card-body">
            <form action="{{route('supplier_show')}}">
                <div class="row g-3">
                    {{-- Name Search --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">Supplier Name</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="searchTextField" 
                                   placeholder="Search by name..." 
                                   value="">
                        </div>
                    </div>

                    {{-- Service Type --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">Service Type</label>
                        <select id="service-select" class="form-select">
                            <option selected>{!!trans('main.All')!!}</option>
                            @foreach($options as $option)
                                <option>@if($option ==='Transfer') Bus Company @else {{$option}} @endif</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Country --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">Country</label>
                        {!! Form::select('country', \App\Helper\Choices::getCountriesSupplierSearchArray(), '', ['class' => 'form-select', 'id' => 'country']) !!}
                    </div>

                    {{-- City --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">City</label>
                        <input id="city" 
                               name="city" 
                               type="text" 
                               class="form-control"
                               value="" 
                               placeholder="Enter city...">
                        <input type="hidden" name="city_code" id="city_code" value="">
                    </div>

                    {{-- Search Button --}}
                    <div class="col-12">
                        <button type="button" 
                                id="supplierSearchButton" 
                                class="btn btn-primary">
                            <i class="ti ti-search me-1"></i>Search Suppliers
                        </button>
                        <button type="button" 
                                class="btn btn-ghost-secondary" 
                                onclick="document.querySelector('form').reset(); $('#searchTextField').focus();">
                            <i class="ti ti-x me-1"></i>Clear
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Alert Messages --}}
    <div class="alert alert-info block-error-driver-transfer" 
         style="display: none;" 
         role="alert">
        <div class="d-flex">
            <div>
                <i class="ti ti-info-circle me-2"></i>
            </div>
            <div class="flex-fill"></div>
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.style.display='none'"></button>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Search Results</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="search-table" class="table card-table table-vcenter table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>{!! trans('main.Name') !!}</th>
                            <th>{!! trans('main.Address') !!}</th>
                            <th>{!! trans('main.Country') !!}</th>
                            <th>{!! trans('main.City') !!}</th>
                            <th>{!! trans('main.Phone') !!}</th>
                            <th>{!! trans('main.ContactName') !!}</th>
                            <th class="text-end">{!! trans('main.Actions') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DataTables will populate this --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add to Tour Modal --}}
<div class="modal modal-blur fade" id="addTourModal" tabindex="-1" aria-labelledby="addTourLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTourLabel">
                    <i class="ti ti-plus me-2"></i>{!!trans('main.AddforTour')!!}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tour-table" class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{!!trans('main.Name')!!}</th>
                                <th class="d-none d-md-table-cell">{!!trans('main.DepDate')!!}</th>
                                <th class="d-none d-md-table-cell">{!!trans('main.Retdate')!!}</th>
                                <th class="d-none d-sm-table-cell">Pax</th>
                                <th class="text-end">{!!trans('main.Choose')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Select Date Modal --}}
<div class="modal modal-blur fade" id="selectDateForTour" tabindex="-1" aria-labelledby="selectDateForTourLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectDateForTourLabel">
                    <i class="ti ti-calendar me-2"></i>{!!trans('main.SelectDate')!!}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info error_date" style="display: none;" role="alert">
                    <i class="ti ti-info-circle me-2"></i>
                    <span></span>
                </div>

                <div class="mb-3">
                    <label class="form-label">{!!trans('main.DateFrom')!!}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        {!! Form::text('date_service', '', [
                            'class' => 'form-control datepickerDisabled',
                            'id' => 'date_service',
                            'placeholder' => 'Select date from'
                        ]) !!}
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{!!trans('main.DateTo')!!}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        {!! Form::text('date_service_retirement', '', [
                            'class' => 'form-control datepickerDisabled',
                            'id' => 'date_service_retirement',
                            'disabled' => true,
                            'placeholder' => 'Select date to'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Cancel
                </button>
                <button class="addTourWithDate btn btn-primary pre-loader-func" type="button">
                    <i class="ti ti-check me-1"></i>{!!trans('main.Add')!!}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Select Driver and Bus Modal --}}
<div class="modal modal-blur fade" id="select-driver-and-bus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="form_transfer_buses_drivers">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-bus me-2"></i>{!!trans('main.Selectdriversandbuses')!!}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info block-error-driver" style="display: none;" role="alert">
                        <i class="ti ti-info-circle me-2"></i>
                        <span></span>
                    </div>
                    
                    <div class="list-driver-and-buses"></div>

                    <div class="overlay" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>Cancel
                    </button>
                    <div class="btn-send-driver">
                        <button type="button" class="btn btn-primary btn-send-transfer_add">
                            <i class="ti ti-check me-1"></i>{!!trans('main.Add')!!}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Transfer Package Date Modal --}}
<div class="modal modal-blur fade" id="selectDateForTransferPackage" tabindex="-1" aria-labelledby="selectDateForTransferPackageLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-calendar me-2"></i>{!!trans('main.SelectDate')!!}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info error_date" style="display: none;" role="alert">
                    <i class="ti ti-info-circle me-2"></i>
                    <span></span>
                </div>

                <div class="mb-3">
                    <label class="form-label">{!!trans('main.DateFrom')!!}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        {!! Form::text('date_service_package', '', [
                            'class' => 'form-control datepickerDisabledTransferPackage',
                            'id' => 'date_service_transfer_package',
                            'placeholder' => 'Select date from'
                        ]) !!}
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{!!trans('main.DateTo')!!}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        {!! Form::text('date_service_retirement_package', '', [
                            'class' => 'form-control datepickerDisabledTransferPackage',
                            'id' => 'date_service_transfer_retirement_package',
                            'placeholder' => 'Select date to'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Cancel
                </button>
                <button class="addTransferPackageWithDate btn btn-primary" type="button">
                    <i class="ti ti-arrow-right me-1"></i>{!!trans('main.Next')!!}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Auto focus on search input
        setTimeout(function() {
            $('#searchTextField').focus();
        }, 1000);
    });
</script>
<script type="text/javascript">
    // Override DataTables initialization to add responsive features
    (function() {
        const originalGenerateTable = globalSearch.generateTable;
        
        globalSearch.generateTable = function(service_select = null) {
            // Store original service for visibility toggles
            if(this.service == "Hotel"){
                $("#hotel_service_create").css("display","block");
                $("#guide_service_create").css("display","none");
                $("#event_service_create").css("display","none");
                $("#res_service_create").css("display","none");
                $("#bus_service_create").css("display","none");
            }
            else if(this.service == "Guide"){
                $("#guide_service_create").css("display","block");
                $("#hotel_service_create").css("display","none");
                $("#event_service_create").css("display","none");
                $("#res_service_create").css("display","none");
                $("#bus_service_create").css("display","none");
            }
            else if(this.service == "Event"){
                $("#event_service_create").css("display","block");
                $("#guide_service_create").css("display","none");
                $("#hotel_service_create").css("display","none");
                $("#res_service_create").css("display","none");
                $("#bus_service_create").css("display","none");
            }
            else if(this.service == "Transfer"){
                $("#bus_service_create").css("display","block");
                $("#guide_service_create").css("display","none");
                $("#hotel_service_create").css("display","none");
                $("#res_service_create").css("display","none");
                $("#event_service_create").css("display","none");
            }
            else{
                $("#res_service_create").css("display","block");
                $("#guide_service_create").css("display","none");
                $("#hotel_service_create").css("display","none");
                $("#event_service_create").css("display","none");
                $("#bus_service_create").css("display","none");
            }
            
            // Initialize DataTables with responsive configuration
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
                    {data: 'nameService', name: 'nameService', responsivePriority: 1},
                    {data: 'address_first', name: 'address_first', responsivePriority: 4},
                    {data: 'country', name: 'country', responsivePriority: 5},
                    {data: 'city', name: 'city', responsivePriority: 6},
                    {data: 'work_phone', name: 'work_phone', responsivePriority: 2},
                    {data: 'contact_name', name: 'contact_name', responsivePriority: 7},
                    {data: this.actionColumn, sortable: false, responsivePriority: 3}
                ],
                initComplete: function(settings, json) {
                    if(service_select){
                        $(service_select).attr('disabled', false);
                    }
                    
                    // Update Bootstrap 5 classes
                    setTimeout(function() {
                        $('.dataTables_wrapper .row').addClass('g-2');
                        $('.dataTables_length').addClass('text-start');
                        $('.dataTables_filter').addClass('text-md-end');
                        $('.dataTables_info').addClass('text-start');
                        $('.dataTables_paginate').addClass('text-md-end');
                    }, 50);
                },
                rowCallback: function(row, data) {
                    var actionCell = $(row).find('td:last');
                    var anchorElement = actionCell.find('a.show-button');
                    var dataLink = anchorElement.attr('data-link');

                    if (dataLink !== undefined) {
                        $(row).on('click', function() {
                            window.location.href = dataLink;
                        });
                    }
                }
            });
            
            $('#search-table_filter').css('display', 'none');
            
            // Add custom search fields
            $('#search-table_filter').after('<label>City:<input type="text" id="city-search" style="margin-right: 10px;"></label>');
            $('#search-table_filter').before('<label>Name:<input type="text" id="hotel-name-search" style="margin-left: 10px;"></label>');

            $('#city-search').on('keyup', function () {
                table.column(3).search(this.value).draw();
            });

            $('#hotel-name-search').on('keyup', function () {
                table.column(0).search(this.value).draw();
            });
        };
    })();
</script>
<script type="text/javascript" src="{{asset('js/supplier-search.js')}}"></script>
<script type="text/javascript">
    // Initialize the search app
    globalSearchApp.run();
</script>
@endpush
