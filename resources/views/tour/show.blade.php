@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Show Tour')

@php
    use Illuminate\Support\Str;
@endphp

@section('post_styles')
<link rel="stylesheet" href="{{ asset('css/tour-shopify.css') }}">
<style>
    /* Toggle Switch */
    .toggle {
        position: relative;
        height: 42px;
        display: flex;
        align-items: center;
    }
    
    .toggle input[type="checkbox"] {
        position: absolute;
        left: 0;
        top: 0;
        z-index: 10;
        width: 100%;
        height: 100%;
        cursor: pointer;
        opacity: 0;
    }
    
    .toggle label {
        position: relative;
        display: flex;
        height: 100%;
        align-items: center;
    }
    
    .toggle label:before {
        content: "Quotations";
        background: #fff;
        color: #000;
        height: 42px;
        width: 140px;
        display: inline-flex;
        align-items: center;
        padding-left: 15px;
        border-radius: 30px;
        border: 1px solid #eee;
        box-shadow: inset 140px 0px 0 0px #000;
        font-size: 10px;
        transition: 0.2s ease-in;
    }
    
    .toggle label:after {
        content: "GoAhead";
        position: absolute;
        left: 80px;
        line-height: 42px;
        top: 0;
        color: #FFF;
        font-size: 10px;
        transition: 0.2s ease-in;
    }
    
    .toggle input[type="checkbox"]:checked + label:before {
        color: #000;
        box-shadow: inset 0px 0px 0 0px #000;
    }
    
    .toggle input[type="checkbox"]:checked + label:after {
        color: #FFF;
    }
</style>
@endsection

@section('content')
@php
    $tourDayLookup = collect($tourDayLookup ?? []);
@endphp
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                {{-- Page pre-title --}}
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tour.index') }}">Tours</a></li>
                            <li class="breadcrumb-item active">{{ $tour->name }}</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-plane me-2"></i> {{ $tour->name }}
                </h2>
            </div>
            {{-- Page title actions --}}
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('tour.index') }}" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
                    </a>
                    @if (Auth::user()->can('tour.edit'))
                        <a href="{{ route('tour.edit', ['tour' => $tour->id]) }}" class="btn btn-warning">
                            <i class="ti ti-edit me-1"></i>{!! trans('main.Edit') !!}
                        </a>
                    @endif
                    @if (Auth::user()->can('task.create'))
                        <a href="{{ url('task') }}/create?tour={{ $tour->id }}" class="btn btn-success">
                            <i class="ti ti-plus me-1"></i>{!! trans('main.AddTask') !!}
                        </a>
                    @endif
                    
                    {{-- Export Dropdown --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-file-export me-1"></i>Export
                        </button>
                        <div class="dropdown-menu">
                            <h6 class="dropdown-header">Export Tour</h6>
                            <a class="dropdown-item" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'tour']) }}"); return false;'>
                                <i class="ti ti-file-spreadsheet me-2"></i>CSV - Tour
                            </a>
                            <a class="dropdown-item" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'service']) }}"); return false;'>
                                <i class="ti ti-file-spreadsheet me-2"></i>CSV - Service
                            </a>
                            <a class="dropdown-item" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'xlsx']) }}"); return false;'>
                                <i class="ti ti-file-excel me-2"></i>Excel
                            </a>
                        </div>
                    </div>
                    
                    {{-- Voucher Dropdown --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-file-invoice me-1"></i>{!! trans('main.Voucher') !!}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'voucher']) }}"); return false;'>
                                <i class="ti ti-file-type-pdf me-2"></i>PDF
                            </a>
                            <a class="dropdown-item" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'voucher']) }}"); return false;'>
                                <i class="ti ti-file-type-doc me-2"></i>DOC
                            </a>
                        </div>
                    </div>
                    
                    {{-- Itinerary Dropdown --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-route me-1"></i>{!! trans('main.Itinerary') !!}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'short']) }}"); return false;'>
                                <i class="ti ti-file-type-pdf me-2"></i>PDF
                            </a>
                            <a class="dropdown-item" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_html_export', ['id' => $tour->id, 'type' => 'html']) }}"); return false;'>
                                <i class="ti ti-file-code me-2"></i>HTML
                            </a>
                            <a class="dropdown-item" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'short']) }}"); return false;'>
                                <i class="ti ti-file-type-doc me-2"></i>DOC
                            </a>
                        </div>
                    </div>
                    
                    <button class="btn btn-info" onclick="showLandingPageModal()">
                        <i class="ti ti-world me-1"></i>Landing Page
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Office Selection --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Office:</label>
                            <div class="input-group">
                                <select class="form-select selectedOffice">
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}" {{ (isset($select_office->id) && $office->id == $select_office->id) ? 'selected' : '' }}>
                                            {{ $office->office_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary select-office-btn" type="button">
                                    <i class="ti ti-check me-1"></i>Select
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-info mt-4" data-bs-toggle="modal" data-bs-target="#legendModal">
                                <i class="ti ti-help me-1"></i>Help
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quotation/Tour Toggle --}}
    @if ($tour->is_quotation)
        <div class="alert alert-warning" role="alert">
            <div class="d-flex">
                <div class="flex-fill">
                    <h4 class="alert-title">
                        <i class="ti ti-exchange me-2"></i>Convert Quotation to Tour
                    </h4>
                </div>
                <div>
                    <div class="toggle">
                        <input type="checkbox" id="check1" onclick="handleToggleConversion(this, true)" checked />
                        <label></label>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-success" role="alert">
            <div class="d-flex">
                <div class="flex-fill">
                    <h4 class="alert-title">
                        <i class="ti ti-exchange me-2"></i>Convert Tour to Quotation
                    </h4>
                </div>
                <div>
                    <div class="toggle">
                        <input type="checkbox" id="check2" onclick="handleToggleConversion(this, false)" />
                        <label></label>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Session Messages --}}
    @if(session('message_buses'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-info-circle me-2"></i>
                </div>
                <div class="flex-fill">
                    {{ session('message_buses') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#frontsheet-tab" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                        <i class="ti ti-file-text me-1"></i>Front Sheet
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#service-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-list me-1"></i>{!! trans('main.Services') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#tour-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-plane me-1"></i>{!! trans('main.Tour') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#quotation-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-calculator me-1"></i>{!! trans('main.Quotation') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#roomlist-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-bed me-1"></i>{!! trans('main.GuestList') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#invoices-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-file-invoice me-1"></i>Invoices
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#billing-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-cash me-1"></i>Billing
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
            {{-- Front Sheet Tab --}}
            <div role="tabpanel" class="tab-pane active show" id="frontsheet-tab">
                <h3 class="mb-4">
                    <i class="ti ti-file-text me-2"></i>Front Sheet [- {{ $tour->external_name ?? $tour->name }} #{{ $tour->id }}]
                </h3>
                
                @if(!empty($quotation) && isset($quotation->id))
                    @if(!empty($serviceDays))
                    {{-- Summary Information --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>
                                <strong>Rooms:</strong>
                                @php $peopleCount = 0; @endphp
                                @foreach ($listRoomsHotel as $room)
                                    @php
                                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code]) 
                                            ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count 
                                            : 0;
                                    @endphp
                                    {{ $room->room_types->code }} : {{ $room->count }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <h5>
                                <strong>Pax:</strong> {{ $tour->pax }} +{{ $tour->pax_free }}
                            </h5>
                        </div>
                    </div>

                    @if ($peopleCount != $tour->pax + $tour->pax_free)
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-triangle me-2"></i>
                                </div>
                                <div class="flex-fill">
                                    <strong>Pax Count ({{ $tour->pax + $tour->pax_free }}) is not equal to the number of people in the rooms ({{ $peopleCount }})</strong>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    @endif

                    {{-- Front Sheet Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 100px;">Date</th>
                                    <th style="min-width: 80px;">City</th>
                                    <th style="min-width: 100px;">Quote Single</th>
                                    <th style="min-width: 100px;">Quote SS</th>
                                    <th style="min-width: 100px;">Quote HPP</th>
                                    <th style="min-width: 200px;">CMFD HOTEL</th>
                                    <th style="min-width: 80px;">Option</th>
                                    <th style="min-width: 100px;">Offer SS</th>
                                    <th style="min-width: 100px;">Offer HPP</th>
                                    <th style="min-width: 80px;">®</th>
                                    <th style="min-width: 120px;">VC sent to SHA</th>
                                    <th style="min-width: 120px;">Budget HPP +/-</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceDays as $day)
                                    <tr>
                                        <td>{{ $day['date_key'] }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center"></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Additional Information --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Column Descriptions</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><strong>Quote Single:</strong> Single room quoted price</li>
                                                <li><strong>Quote SS:</strong> Single supplement quoted price</li>
                                                <li><strong>Quote HPP:</strong> Half-board per person quoted price</li>
                                                <li><strong>CMFD HOTEL:</strong> Confirmed hotel name</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><strong>Option:</strong> Option status</li>
                                                <li><strong>Offer SS/HPP:</strong> Offered prices</li>
                                                <li><strong>®:</strong> Registered/Confirmed</li>
                                                <li><strong>VC sent to SHA:</strong> Voucher/Confirmation sent</li>
                                                <li><strong>Budget HPP +/-:</strong> Budget variance</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> {{ __('No service schedule is available for this tour yet.') }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i> No quotation data available for front sheet. Please create a quotation first.
                    </div>
                @endif
            </div>

            {{-- Services Tab --}}
            <div role="tabpanel" class="tab-pane" id="service-tab">
                <h3 class="mb-4"><i class="ti ti-list me-2"></i>Services</h3>
                
                {{-- Add Package Buttons --}}
                <div class="btn-toolbar mb-3" role="toolbar">
                    <div class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-success btn-sm" onclick="addDay()">
                            <i class="ti ti-plus me-1"></i>Day
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="addAllDays()">Add All</button>
                    </div>
                    <div class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportCity()">City</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportExcel()">Excel</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportNumber()">Number</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportItinerary()">Itinerary</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printAll()">Print All</button>
                    </div>
                </div>

                {{-- Pax Count Warning --}}
                @php $peopleCount = 0; @endphp
                @foreach ($listRoomsHotel as $room)
                    @php
                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code]) 
                            ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count 
                            : 0;
                    @endphp
                @endforeach
                
                @if ($peopleCount != $tour->pax + $tour->pax_free)
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-alert-triangle me-2"></i>
                            </div>
                            <div class="flex-fill">
                                <strong>Pax Count ({{ $tour->pax + $tour->pax_free }}) is not equal to the number of people in the rooms ({{ $peopleCount }})</strong>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                @endif

                {{-- Service Days --}}
                <div id="service-days-container">
                    @if(!empty($serviceDays))
                        @foreach($serviceDays as $day)
                            @php
                                $tourDayId = $day['tour_day_id'];
                                $dayPackages = $day['packages'];
                    @endphp
                        <div class="card mb-4" data-day="{{ $day['day_number'] }}" data-date="{{ $day['date_key'] }}" @if($tourDayId) data-tour-day-id="{{ $tourDayId }}" @endif>
                            <div class="card-header bg-light">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title mb-0">
                                            Day {{ $day['day_number'] }} - {{ $day['date']->format('F d, Y') }} ({{ $day['day_name'] }})
                                        </h4>
                                    </div>
                                    <div class="col-auto">
                                        @if($tourDayId)
                                            <button type="button" class="btn btn-success btn-sm me-1 add-description-package">
                                            <i class="ti ti-plus"></i> Add description
                                        </button>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm add-service-quick"
                                                    data-tourdayid="{{ $tourDayId }}"
                                                    data-link="{{ route('tour_package.store') }}"
                                                    data-tour_id="{{ $tour->id }}"
                                                    data-date="{{ $day['date_key'] }}"
                                                    data-departure_date="{{ $tour->departure_date }}"
                                                    data-retirement_date="{{ $tour->retirement_date }}">
                                            <i class="ti ti-download"></i> Add Service
                                        </button>
                                        @else
                                            <button type="button" class="btn btn-secondary btn-sm" disabled>
                                                <i class="ti ti-alert-triangle"></i> {{ __('No tour day for this date') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                @if($dayPackages->count() > 0)
                                    @foreach($dayPackages as $package)
                                        <div class="service-item mb-4 pb-4 border-bottom">
                                            {{-- Status Icons --}}
                                            <div class="mb-3">
                                                @if(isset($package->is_confirmed) && $package->is_confirmed)
                                                    <i class="ti ti-check text-success me-2" title="Confirmed"></i>
                                                @endif
                                                @if(isset($package->is_voucher_sent) && $package->is_voucher_sent)
                                                    <i class="ti ti-check text-success" title="Voucher Sent"></i>
                                                @endif
                                            </div>

                                            {{-- Service Time --}}
                                            @if(isset($package->time) && $package->time)
                                                <div class="mb-2">
                                                    <strong>{{ \Carbon\Carbon::parse($package->time)->format('H:i') }}</strong>
                                                </div>
                                            @endif

                                            {{-- Service Details --}}
                                            <div class="service-details">
                                                <h5 class="mb-2">
                                                    {{ $package->supplier->name ?? 'Service' }}
                                                    @if(isset($package->supplier->city))
                                                        - {{ $package->supplier->city }}
                                                    @endif
                                                </h5>
                                                
                                                @if(isset($package->pax) && $package->pax)
                                                    <div class="mb-2">
                                                        <strong>PAX:</strong> {{ $package->pax }}
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->room_configuration) && $package->room_configuration)
                                                    <div class="mb-2">
                                                        <strong>ROOM NO:</strong> {{ $package->room_configuration }}
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->total_price) || isset($package->price_per_person))
                                                    <div class="mb-2">
                                                        @if(isset($package->total_price))
                                                            <strong>TTL:</strong> {{ $package->total_price }}<br>
                                                        @endif
                                                        @if(isset($package->price_per_person))
                                                            <strong>PSN/TTL:</strong> {{ $package->price_per_person }}
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->driver_included))
                                                    <div class="mb-2">
                                                        <strong>DRIVER:</strong> {{ $package->driver_included ? 'Yes' : 'No' }}
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->rooms_info) && $package->rooms_info)
                                                    <div class="mb-2">
                                                        <strong>ROOMS:</strong><br>
                                                        {{ $package->rooms_info }}
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->breakfast_info) && $package->breakfast_info)
                                                    <div class="mb-2">
                                                        <strong>BREAKFAST:</strong> {{ $package->breakfast_info }}
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->meals) && $package->meals)
                                                    <div class="mb-2">
                                                        <strong>MEALS:</strong> {{ $package->meals }}
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->special_meal) && $package->special_meal)
                                                    <div class="mb-2">
                                                        <strong>SPECIAL MEAL Req:</strong> {{ $package->special_meal }}
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->notes) && $package->notes)
                                                    <div class="alert alert-info mb-2">
                                                        {!! nl2br(e($package->notes)) !!}
                                                    </div>
                                                @endif
                                                
                                                @if(isset($package->description) && $package->description)
                                                    <div class="mt-3">
                                                        <div class="description-content" id="desc-{{ $package->id }}" data-full-description="{{ htmlspecialchars($package->description, ENT_QUOTES, 'UTF-8') }}">
                                                            {!! nl2br(e(Str::limit($package->description, 500))) !!}
                                                        </div>
                                                        @if(strlen($package->description) > 500)
                                                            <a href="javascript:void(0)" class="text-primary" onclick="toggleDescription({{ $package->id }})">
                                                                <small>readmore</small>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="mt-3">
                                                <button class="btn btn-warning btn-sm me-1" onclick="editService({{ $package->id }})">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteService({{ $package->id }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="ti ti-inbox fs-1"></i>
                                        <p class="mt-2">No services added for this day</p>
                                        @if($tourDayId)
                                            <button type="button"
                                                    class="btn btn-sm btn-primary add-service-quick"
                                                    data-tourdayid="{{ $tourDayId }}"
                                                    data-link="{{ route('tour_package.store') }}"
                                                    data-tour_id="{{ $tour->id }}"
                                                    data-date="{{ $day['date_key'] }}"
                                                    data-departure_date="{{ $tour->departure_date }}"
                                                    data-retirement_date="{{ $tour->retirement_date }}">
                                            <i class="ti ti-plus me-1"></i>Add First Service
                                        </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> {{ __('No service days are configured for this tour yet.') }}
                        </div>
                    @endif
                </div>

                {{-- Comments Section --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-message me-2"></i>{!! trans('main.Comments') !!}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="show_comments"></div>
                    </div>
                    <div class="card-footer">
                        <form method="POST" action="{{ route('comment.store') }}" id="form_comment">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
                            </div>
                            <input type="hidden" name="reference_id" value="{{ $tour->id }}">
                            <input type="hidden" name="reference_type" value="{{ \App\Comment::$services['tour'] ?? 'tour' }}">
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-send me-1"></i>{!! trans('main.Send') !!}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tour Info Tab --}}
            <div role="tabpanel" class="tab-pane" id="tour-tab">
                <h3 class="mb-4"><i class="ti ti-plane me-2"></i>Tour Information</h3>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table card-table table-vcenter">
                            <tbody>
                                <tr>
                                    <td><strong>{!! trans('main.Name') !!}</strong></td>
                                    <td>{{ $tour->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.ExternalName') !!}</strong></td>
                                    <td>{{ $tour->external_name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.Pax') !!}</strong></td>
                                    <td>{{ $tour->pax ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.PaxFree') !!}</strong></td>
                                    <td>{{ $tour->pax_free ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table card-table table-vcenter">
                            <tbody>
                                <tr>
                                    <td><strong>{!! trans('main.DepDate') !!}</strong></td>
                                    <td>{{ $tour->departure_date ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.RetDate') !!}</strong></td>
                                    <td>{{ $tour->retirement_date ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.Status') !!}</strong></td>
                                    <td>{{ $status->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.Phone') !!}</strong></td>
                                    <td>{{ $tour->phone ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quotations Tab --}}
            <div role="tabpanel" class="tab-pane" id="quotation-tab">
                <h3 class="mb-4"><i class="ti ti-calculator me-2"></i>Quotations</h3>
                @if (Auth::user()->can('quotation.add'))
                    <a href="{{ route('quotation.add', ['id' => $tour->id]) }}" class="btn btn-success mb-3">
                        <i class="ti ti-plus"></i> {!! trans('main.AddQuotation') !!}
                    </a>
                @endif
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>{!! trans('main.Name') !!}</th>
                            <th>{!! trans('main.Assigned') !!}</th>
                            <th>{!! trans('main.Frontsheet') !!}</th>
                            <th>{!! trans('main.Print') !!}</th>
                            <th>Excel</th>
                            <th>{!! trans('main.CreatedAt') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tour->quotations as $quotation)
                            <tr style="background-color: {{ $quotation->is_confirm == 0 ? '#ff00008f' : '#caffbd' }}">
                                <td>
                                    @if (Auth::user()->can('quotation.edit'))
                                        <a href="{{ route('quotation.edit', ['quotation' => $quotation->id]) }}">
                                            {{ $quotation->name ?? '—' }}
                                        </a>
                                    @else
                                        {{ $quotation->name ?? '—' }}
                                    @endif
                                </td>
                                <td>{{ $quotation->userName() ?? '—' }}</td>
                                <td>
                                    @if (Auth::user()->can('comparison.show'))
                                        <a href="{{ route('comparison.show', ['comparison' => $quotation->id]) }}">
                                            View Front Sheet
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('quotation.pdf', ['id' => $quotation->id]) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('quotation.excel', ['id' => $quotation->id]) }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="ti ti-file-excel"></i>
                                    </a>
                                </td>
                                <td>{{ Carbon\Carbon::parse($quotation->created_at)->format('d-m-Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="ti ti-inbox"></i> No quotations found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Guest List Tab --}}
            <div role="tabpanel" class="tab-pane" id="roomlist-tab">
                <h3 class="mb-4"><i class="ti ti-bed me-2"></i>Guest Lists</h3>
                @if (Auth::user()->can('guestList.add'))
                    <a href="{{ route('guestList.add', ['id' => $tour->id]) }}" class="btn btn-success mb-3">
                        <i class="ti ti-plus"></i> Add Guest List
                    </a>
                @endif
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th>{!! trans('main.Name') !!}</th>
                            <th>{!! trans('main.Author') !!}</th>
                            <th>{!! trans('main.CreatedAt') !!}</th>
                            <th>{!! trans('main.SentAt') !!}</th>
                            <th>{!! trans('main.Hotels') !!}</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tour->guestLists as $guestList)
                            <tr>
                                <td>{{ $guestList->version }}</td>
                                <td>
                                    @if (Auth::user()->can('guestList.showbyid'))
                                        <a href="{{ route('guestList.showbyid', ['id' => $guestList->id]) }}">
                                            {{ $guestList->name }}
                                        </a>
                                    @else
                                        {{ $guestList->name }}
                                    @endif
                                </td>
                                <td>{{ $guestList->getAuthor()->name ?? '—' }}</td>
                                <td>{{ Carbon\Carbon::parse($guestList->created_at)->format('d-m-Y') }}</td>
                                <td>
                                    @if($guestList->sent_at)
                                        {{ Carbon\Carbon::parse($guestList->sent_at)->format('d-m-Y') }}
                                    @else
                                        <span class="text-muted">Not sent</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($guestList->getSelectedHotelNames() as $index => $hotelName)
                                        {{ $hotelName }}{{ $index < count($guestList->getSelectedHotelNames()) - 1 ? ', ' : '' }}
                                    @endforeach
                                </td>
                                <td>
                                    @if(!$guestList->sent_at)
                                        <button class="btn btn-sm btn-primary send-guest-list" 
                                                data-url="{{ route('guestlist.send', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}">
                                            <i class="ti ti-send"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-guest-list"
                                                data-url="{{ route('guestlist.delete', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <i class="ti ti-inbox"></i> No guest lists found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Invoices Tab --}}
            <div role="tabpanel" class="tab-pane" id="invoices-tab">
                <h3 class="mb-4"><i class="ti ti-file-invoice me-2"></i>Invoices</h3>
                @if(Auth::user()->can('invoices.create'))
                    <a href="{{ route('invoices.create', ['tour_id' => $tour->id]) }}" class="btn btn-success mb-3">
                        <i class="ti ti-plus me-1"></i>New Invoice
                    </a>
                @endif
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice No</th>
                            <th>Due Date</th>
                            <th>Received Date</th>
                            <th>Service</th>
                            <th>Office</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoicesData as $invoice)
                            <tr>
                                <td>{{ $invoice['id'] }}</td>
                                <td>{{ $invoice['invoice_no'] }}</td>
                                <td>{{ $invoice['due_date'] }}</td>
                                <td>{{ $invoice['received_date'] }}</td>
                                <td>{{ $invoice['package_name'] }}</td>
                                <td>{{ $invoice['office_name'] }}</td>
                                <td>{{ $invoice['total_amount'] }}</td>
                                <td>{{ $invoice['status'] }}</td>
                                <td>
                                    <a href="{{ route('invoices.show', ['invoice' => $invoice['id']]) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', ['invoice' => $invoice['id']]) }}" class="btn btn-sm btn-warning">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <i class="ti ti-inbox"></i> No invoices found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Billing Tab --}}
            <div role="tabpanel" class="tab-pane" id="billing-tab">
                <h3 class="mb-4"><i class="ti ti-cash me-2"></i>Billing</h3>
                @if(Auth::user()->can('accounting.create'))
                    <a href="{{ route('accounting.create', ['tour_id' => $tour->id]) }}" class="btn btn-success mb-3">
                        <i class="ti ti-plus me-1"></i>New Billing
                    </a>
                @endif
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Office</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billingData as $billing)
                            <tr>
                                <td>{{ $billing['id'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($billing['date'] ?? now())->format('Y-m-d') }}</td>
                                <td>{{ $billing['office_name'] }}</td>
                                <td>{{ $billing['total_amount'] }}</td>
                                <td>
                                    <a href="{{ route('accounting.show', ['accounting' => $billing['id']]) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('accounting.edit', ['accounting' => $billing['id']]) }}" class="btn btn-sm btn-warning">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <i class="ti ti-inbox"></i> No billing records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Data --}}
<span id="tour_date_id" data-tour-id="{{ $tour->id }}" hidden></span>
<span id="tour_dates" data-departure_date="{{ $tour->departure_date }}" data-retirement_date="{{ $tour->retirement_date }}" hidden></span>
<input type="hidden" id="default_reference_id" value="{{ $tour->id }}">

{{-- Service Modal --}}
<div class="modal modal-blur fade" id="service-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{!! trans('main.Addservice') !!}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="service-type-filter" class="form-label">{!! trans('main.Servicetype') !!}</label>
                        <select id="service-type-filter" class="form-select">
                            <option value="all" selected>{!! trans('main.All') !!}</option>
                            @foreach($options as $option)
                                <option value="{{ strtolower($option) }}">{{ $option }}</option>
                            @endforeach
                            <option value="transfer">{!! trans('main.Transfer') !!}</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label for="service-catalog-search" class="form-label">{!! trans('main.Search') !!}</label>
                        <input type="text" id="service-catalog-search" class="form-control" placeholder="{!! __('Search by name, city or country') !!}">
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table id="service-catalog-table" class="table card-table table-vcenter table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>{!! trans('main.Name') !!}</th>
                                <th>{!! trans('main.Address') !!}</th>
                                <th>{!! trans('main.Country') !!}</th>
                                <th>{!! trans('main.City') !!}</th>
                                <th>{!! trans('main.Phone') !!}</th>
                                <th>{!! trans('main.ContactName') !!}</th>
                                <th>{!! trans('main.Actions') !!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables will populate this via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Landing Page Modal --}}
<div class="modal fade" id="landingpage_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>There is no image for landing page. Are you sure you want to generate the page?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="open-landing" onclick='export_to("{{ route('landing_page', ['id' => $tour->id]) }}");'>
                    Agree
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Question Modal --}}
<div class="modal fade" id="question_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Would you like to send Guest List to selected tour hotels?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="send_agree">Agree</button>
            </div>
        </div>
    </div>
</div>

{{-- Error Modal --}}
<div class="modal fade" id="error_send" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_modal_error">Warning!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h3 class="error_send_message"></h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('post_scripts')
<script src="{{ asset('js/tour-interactions.js') }}"></script>
<script src="{{ asset('js/tour.js') }}"></script>
<script src="{{ asset('js/comment.js') }}"></script>
<script src="{{ asset('js/attachments.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabStorageKey = 'tourActiveTab';
    const storedTab = localStorage.getItem(tabStorageKey);
    if (storedTab && typeof bootstrap !== 'undefined' && bootstrap && bootstrap.Tab) {
        const trigger = document.querySelector('.nav-tabs a[href="#' + storedTab + '"]');
        if (trigger) {
            // Root cause fix: Bootstrap Tab compatibility - handle both Bootstrap 4 and 5
            try {
                if (bootstrap.Tab.getOrCreateInstance && typeof bootstrap.Tab.getOrCreateInstance === 'function') {
                    // Bootstrap 5.1+
                    const tabInstance = bootstrap.Tab.getOrCreateInstance(trigger);
                    if (tabInstance && typeof tabInstance.show === 'function') {
                        tabInstance.show();
                    }
                } else if (bootstrap.Tab.getInstance && typeof bootstrap.Tab.getInstance === 'function') {
                    // Bootstrap 5.0
                    let tabInstance = bootstrap.Tab.getInstance(trigger);
                    if (!tabInstance && typeof bootstrap.Tab === 'function') {
                        tabInstance = new bootstrap.Tab(trigger);
                    }
                    if (tabInstance && typeof tabInstance.show === 'function') {
                        tabInstance.show();
                    }
                } else {
                    // Fallback to jQuery/Bootstrap 4
                    $(trigger).tab('show');
                }
            } catch (e) {
                console.warn('Bootstrap Tab API failed, falling back to jQuery:', e);
                $(trigger).tab('show');
            }
        }
    }

    document.querySelectorAll('.nav-tabs a[data-bs-toggle="tab"]').forEach(function (triggerEl) {
        triggerEl.addEventListener('shown.bs.tab', function (event) {
            const href = event.target.getAttribute('href') || '';
            if (href.startsWith('#')) {
                localStorage.setItem(tabStorageKey, href.substring(1));
            }
        });
    });

    // Initialize DataTables for service catalog modal
    let serviceCatalogTable = null;
    
    // Initialize DataTables when modal is shown
    // Support both Bootstrap 4 and Bootstrap 5 modal events
    $('#service-modal').on('shown.bs.modal shown.modal', function() {
        if ($.fn.DataTable.isDataTable('#service-catalog-table')) {
            serviceCatalogTable = $('#service-catalog-table').DataTable();
            serviceCatalogTable.draw();
            return;
        }

        serviceCatalogTable = $('#service-catalog-table').DataTable({
            responsive: true,
            dom: "<'row'<'col-md-6'l><'col-md-6'f>>" +
                 "<'row'<'col-12'tr>>" +
                 "<'row'<'col-md-6'i><'col-md-6'p>>",
            processing: true,
            serverSide: true,
            pageLength: 25,
            order: [],
            ajax: {
                url: "/supplier_show",
                type: "GET",
                data: function(d) {
                    d.actionColumn = 'add-service-column';
                    let serviceType = $('#service-type-filter').val();
                    if (serviceType === 'all' || !serviceType) {
                        d.service = 'Service'; // 'Service' means all services in the endpoint
                    } else {
                        // Capitalize first letter and handle special cases
                        serviceType = serviceType.charAt(0).toUpperCase() + serviceType.slice(1);
                        if (serviceType === 'Cruise') serviceType = 'Cruises';
                        d.service = serviceType;
                    }
                    d.searchname = $('#service-catalog-search').val() || '';
                    // DataTables server-side parameters
                    d.draw = d.draw;
                    d.start = d.start;
                    d.length = d.length;
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX Error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    alert('Error loading services. Please try again or refresh the page.');
                }
            },
            columns: [
                {
                    data: 'nameService',
                    name: 'nameService',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const parts = data ? data.split(' (') : ['', ''];
                            const name = parts[0] || '';
                            const typeLabel = parts[1] ? parts[1].replace(')', '') : '';
                            return '<div class="d-flex flex-column">' +
                                   '<span class="fw-bold">' + name + '</span>' +
                                   (typeLabel ? '<small class="text-muted text-uppercase">' + typeLabel + '</small>' : '') +
                                   '</div>';
                        }
                        return data;
                    }
                },
                {data: 'address_first', name: 'address_first'},
                {data: 'country', name: 'country'},
                {data: 'city', name: 'city'},
                {data: 'work_phone', name: 'work_phone'},
                {data: 'contact_name', name: 'contact_name'},
                {data: 'add-service-column', name: 'add-service-column', sortable: false, orderable: false}
            ],
            initComplete: function(settings, json) {
                // Update Bootstrap 5 classes
                setTimeout(function() {
                    $('.dataTables_wrapper .row').addClass('g-2');
                    $('.dataTables_length').addClass('text-start');
                    $('.dataTables_filter').addClass('text-md-end');
                    $('.dataTables_info').addClass('text-start');
                    $('.dataTables_paginate').addClass('text-md-end');
                }, 50);
                
                // Log initialization for debugging
                console.log('Service catalog DataTable initialized', json);
            },
            drawCallback: function(settings) {
                // Log if no data is found
                const api = this.api();
                const data = api.rows({page: 'current'}).data();
                if (data.length === 0) {
                    console.warn('No services found. Check filters and search criteria.');
                }
            }
        });

        // Hide default DataTables search
        $('#service-catalog-table_filter').css('display', 'none');

        // Connect service type filter to DataTables
        $('#service-type-filter').on('change', function() {
            if (serviceCatalogTable) {
                serviceCatalogTable.ajax.reload();
            }
        });

        // Connect search input to DataTables with debounce
        let searchTimeout;
        $('#service-catalog-search').on('keyup', function() {
            clearTimeout(searchTimeout);
            const searchValue = $(this).val();
            searchTimeout = setTimeout(function() {
                if (serviceCatalogTable) {
                    serviceCatalogTable.ajax.reload();
                }
            }, 500); // 500ms debounce
        });
    });

    // Destroy DataTable when modal is hidden to free memory
    // Support both Bootstrap 4 and Bootstrap 5 modal events
    $('#service-modal').on('hidden.bs.modal hidden.modal', function() {
        if (serviceCatalogTable && $.fn.DataTable.isDataTable('#service-catalog-table')) {
            serviceCatalogTable.destroy();
            serviceCatalogTable = null;
        }
    });
});

$(function() {
    // Office Selection
    $('.select-office-btn').on('click', function() {
        let officeId = $('.selectedOffice').val();
        if (officeId) {
            $.ajax({
                url: '/update-status/' + officeId,
                type: 'GET',
                success: function() {
                    location.reload(true);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating office:', error);
                    alert('Error updating office. Please try again.');
                }
            });
        }
    });

    // Send Guest List
    var selectedGuestList;
    $('.send-guest-list').on('click', function() {
        selectedGuestList = $(this);
        var bootstrapModal = new bootstrap.Modal(document.getElementById('question_modal'));
        bootstrapModal.show();
    });

    $('#send_agree').on('click', function() {
        if (!selectedGuestList) return;
        
        let overlay = '<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>';
        let container = selectedGuestList.closest('.card-body');
        container.append(overlay);
        selectedGuestList.hide();
        
        var questionModal = bootstrap.Modal.getInstance(document.getElementById('question_modal'));
        questionModal.hide();
        
        $.ajax({
            method: 'GET',
            url: selectedGuestList.data('url'),
            success: function(res) {
                $('#error_send').find('#title_modal_error').html(res.error === 'error' ? 'Warning!' : 'Success!');
                $('#error_send').find('.error_send_message').html(res.message);
                if (res.broke) {
                    $('#error_send').find('.error_send_message').append('<br><br>' + res.broke);
                }
                $('.overlay').remove();
                
                var errorModal = new bootstrap.Modal(document.getElementById('error_send'));
                errorModal.show();
                
                setTimeout(function() {
                    errorModal.hide();
                    if (res.error !== 'error') {
                        location.reload();
                    } else {
                        selectedGuestList.show();
                    }
                }, 3000);
            },
            error: function(xhr, status, error) {
                $('.overlay').remove();
                console.error('Error sending guest list:', error);
                alert('Error sending guest list. Please try again.');
                selectedGuestList.show();
            }
        });
    });

    // Delete Guest List
    $('.delete-guest-list').on('click', function() {
        if (confirm('Are you sure you want to delete this guest list?')) {
            let url = $(this).data('url');
            $.ajax({
                method: 'GET',
                url: url,
                success: function() {
                    location.reload(true);
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting guest list:', error);
                    alert('Error deleting guest list. Please try again.');
                }
            });
        }
    });

    // Comment form submission with Ctrl+Enter
    $('#content').on('keydown', function(e) {
        if (e.ctrlKey && e.keyCode === 13) {
            $('#form_comment').submit();
        }
    });
});

// Handle Toggle Conversion - FIXED VERSION
function handleToggleConversion(checkbox, isCurrentlyQuotation) {
    var url;
    var confirmMessage;
    
    if (isCurrentlyQuotation) {
        // Currently a quotation
        if (checkbox.checked) {
            // Convert to Tour (Go Ahead)
            url = "{{ route('tour.convert_to_tour', ['id' => $tour->id]) }}";
            confirmMessage = "Are you sure you want to convert this Quotation to Tour (Go Ahead)?";
        } else {
            // Stay as Quotation
            checkbox.checked = true;
            return;
        }
    } else {
        // Currently a tour
        if (checkbox.checked) {
            // Convert to Quotation
            url = "{{ route('tour.convertToQuotation', ['id' => $tour->id]) }}";
            confirmMessage = "Are you sure you want to convert this Tour to Quotation?";
        } else {
            // Stay as Tour
            checkbox.checked = false;
            return;
        }
    }
    
    if (confirm(confirmMessage)) {
        // Show loading indicator
        var loadingHtml = '<div class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999;">' +
                         '<div class="spinner-border text-primary" role="status">' +
                         '<span class="visually-hidden">Loading...</span>' +
                         '</div></div>';
        $('body').append(loadingHtml);
        
        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                console.log('Conversion successful:', response);
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Conversion error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                // Remove loading indicator
                $('.spinner-border').parent().remove();
                
                // Revert checkbox state
                checkbox.checked = !checkbox.checked;
                
                // Show error message
                var errorMessage = 'Error converting tour status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage += ' ' + xhr.responseText;
                }
                
                alert(errorMessage + ' Please check the console for more details.');
            }
        });
    } else {
        // User cancelled, revert checkbox
        checkbox.checked = !checkbox.checked;
    }
}

// Show Landing Page Modal
function showLandingPageModal() {
    var img = "{{ $tour->attachments()->first() ? $tour->attachments()->first()->url : '' }}";
    if (!img) {
        var modal = new bootstrap.Modal(document.getElementById('landingpage_modal'));
        modal.show();
    } else {
        window.open("{{ route('landing_page', ['id' => $tour->id]) }}", '_blank');
    }
}

function exportCity() {
    const url = "{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'service']) }}";
    export_to(url);
}

function exportExcel() {
    const url = "{{ route('tour_export', ['id' => $tour->id, 'export' => 'xlsx']) }}";
    export_to(url);
}

function exportNumber() {
    const url = "{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'tour']) }}";
    export_to(url);
}

function exportItinerary() {
    const url = "{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'short']) }}";
    export_to(url);
}

function printAll() {
    window.print();
}

// Export function
function export_to(url) {
    if (!url) {
        console.error('Export URL is empty');
        return;
    }
    try {
        window.open(url, '_blank');
    } catch (e) {
        console.error('Error opening export URL:', e);
        alert('Error opening export. Please check the console for details.');
    }
}

// Scroll position persistence
$(window).on('scroll', function() {
    localStorage.setItem('tourScrollPosition', $(window).scrollTop());
});

$(document).ready(function() {
    var scrollPos = localStorage.getItem('tourScrollPosition');
    if (scrollPos) {
        $(window).scrollTop(parseInt(scrollPos));
    }
    
    // Root cause fix: Initialize CKEditor for all textareas that need it
    // This ensures CKEditor works throughout the project
    if (typeof CKEDITOR !== 'undefined') {
        // Initialize CKEditor for all textareas with class 'ckeditor' or id containing 'editor'
        $('textarea.ckeditor, textarea[id*="editor"], textarea[id*="description"], textarea[id*="content"]').each(function() {
            const textareaId = $(this).attr('id');
            if (textareaId && !CKEDITOR.instances[textareaId]) {
                try {
                    CKEDITOR.replace(textareaId, {
                        height: 200,
                        toolbar: [
                            ['Bold', 'Italic', 'Underline', 'Strike'],
                            ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
                            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                            ['Link', 'Unlink'],
                            ['Image', 'Table'],
                            ['Source'],
                            ['Maximize']
                        ]
                    });
                } catch (e) {
                    console.warn('Failed to initialize CKEditor for:', textareaId, e);
                }
            }
        });
        
        // Also check for dynamically added textareas
        $(document).on('DOMNodeInserted', function(e) {
            if ($(e.target).is('textarea') || $(e.target).find('textarea').length > 0) {
                $(e.target).find('textarea, textarea').each(function() {
                    const textareaId = $(this).attr('id');
                    if (textareaId && !CKEDITOR.instances[textareaId] && 
                        ($(this).hasClass('ckeditor') || textareaId.includes('editor') || 
                         textareaId.includes('description') || textareaId.includes('content'))) {
                        try {
                            CKEDITOR.replace(textareaId);
                        } catch (err) {
                            console.warn('Failed to initialize CKEditor for dynamic textarea:', textareaId, err);
                        }
                    }
                });
            }
        });
    } else {
        console.warn('CKEditor is not loaded. Please ensure ckeditor.js is included in the page.');
    }
});

// Root cause fix: Add missing JavaScript functions for service actions
// These functions were being called via onclick but were not defined

/**
 * Toggle description visibility (show full/truncated)
 * Root cause fix: Store full description in data attribute when rendering
 */
function toggleDescription(packageId) {
    const descElement = document.getElementById('desc-' + packageId);
    if (!descElement) {
        console.warn('Description element not found for package:', packageId);
        return;
    }
    
    // Get the full description from data attribute (set during server-side rendering)
    let fullDescription = descElement.getAttribute('data-full-description');
    const readmoreLink = descElement.nextElementSibling;
    
    if (!fullDescription) {
        // First time - fetch full description via AJAX
        fetch('/tour_package/' + packageId + '/api')
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch description');
                return response.json();
            })
            .then(data => {
                if (data && data.description) {
                    fullDescription = data.description;
                    descElement.setAttribute('data-full-description', fullDescription);
                    // Format and display full description
                    const formatted = fullDescription.replace(/\n/g, '<br>');
                    descElement.innerHTML = formatted;
                    if (readmoreLink) {
                        readmoreLink.innerHTML = '<small>readless</small>';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching description:', error);
            });
        return;
    }
    
    // Decode HTML entities from data attribute
    const tempDiv = document.createElement('div');
    tempDiv.textContent = fullDescription;
    const decodedFullDescription = tempDiv.innerHTML;
    
    // Check if currently showing full or truncated
    const isShowingFull = readmoreLink && readmoreLink.innerHTML.includes('readless');
    
    if (isShowingFull) {
        // Switch to truncated view
        const truncatedText = decodedFullDescription.substring(0, 500);
        descElement.innerHTML = truncatedText + (decodedFullDescription.length > 500 ? '...' : '');
        if (readmoreLink) {
            readmoreLink.innerHTML = '<small>readmore</small>';
        }
    } else {
        // Switch to full view - format with nl2br
        const formattedDescription = decodedFullDescription.replace(/\n/g, '<br>');
        descElement.innerHTML = formattedDescription;
        if (readmoreLink) {
            readmoreLink.innerHTML = '<small>readless</small>';
        }
    }
}

/**
 * Edit service/package
 */
function editService(packageId) {
    if (!packageId) {
        console.error('Package ID is required');
        return;
    }
    
    // Navigate to edit page
    const editUrl = '/tour_package/' + packageId + '/edit';
    window.location.href = editUrl;
}

/**
 * Delete service/package with confirmation
 */
function deleteService(packageId) {
    if (!packageId) {
        console.error('Package ID is required');
        return;
    }
    
    // Show confirmation dialog
    if (confirm('Are you sure you want to delete this service?')) {
        // Use AJAX to get delete confirmation message
        fetch('/tour_package/' + packageId + '/deleteMsg')
            .then(response => response.text())
            .then(html => {
                // Create a temporary div to parse the HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // Extract the delete URL from the HTML
                const deleteLink = tempDiv.querySelector('a[href*="/delete"]');
                if (deleteLink) {
                    const deleteUrl = deleteLink.getAttribute('href');
                    // Perform the delete
                    fetch(deleteUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // Reload the page to show updated services
                            window.location.reload();
                        } else {
                            alert('Error deleting service. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting service:', error);
                        alert('Error deleting service. Please try again.');
                    });
                } else {
                    // Fallback: direct delete
                    const deleteUrl = '/tour_package/' + packageId + '/delete';
                    fetch(deleteUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Error deleting service. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting service:', error);
                        alert('Error deleting service. Please try again.');
                    });
                }
            })
            .catch(error => {
                console.error('Error getting delete confirmation:', error);
                // Fallback: direct delete with confirmation
                if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
                    const deleteUrl = '/tour_package/' + packageId + '/delete';
                    window.location.href = deleteUrl;
                }
            });
    }
}
</script>
@endsection