@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Show Tour')

@php
    use Illuminate\Support\Str;
@endphp

@section('post_styles')
{{-- Centralized DataTables CDN --}}
@include('component.datatables_cdn')
<link rel="stylesheet" href="{{ asset('css/tour-shopify.css') }}">
<style>
    .select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #aaa;
    border-radius: 4px;
    height: 100%;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #444;
    line-height: 40px;
    /* height: 100%; */
}
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

    {{-- Page header (Phase-3 redesign — original work). Preserves every
         JS handler the prior page wired up: export_to(...) for the three
         dropdowns, showLandingPageModal() for the public landing button. --}}
    <header class="d-print-none mb-4">
        <nav aria-label="Breadcrumb" class="mb-2">
            <ol class="flex items-center gap-1 text-xs text-slate-500 list-none pl-0 m-0">
                <li><a href="{{ url('/home') }}" class="hover:text-slate-700">Home</a></li>
                <li><x-ui.icon name="chevron-right" size="xs" class="text-slate-300" /></li>
                <li><a href="{{ route('tour.index') }}" class="hover:text-slate-700">Tours</a></li>
                <li><x-ui.icon name="chevron-right" size="xs" class="text-slate-300" /></li>
                <li class="text-slate-700 truncate" aria-current="page">{{ $tour->name }}</li>
            </ol>
        </nav>

        <div class="flex items-start gap-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-semibold text-slate-900 truncate flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded bg-primary-50 text-primary-600">
                        <x-ui.icon name="plane" />
                    </span>
                    {{ $tour->name }}
                </h1>
                @if (!empty($tour->external_name))
                    <p class="mt-1 text-sm text-slate-500">{{ $tour->external_name }}</p>
                @endif
            </div>
        </div>

        {{-- Action bar: Back on the left, primary actions (Edit / Add Task)
             in the middle, secondary export-style actions on the right. --}}
        <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
            <x-ui.button as="a" href="{{ route('tour.index') }}" variant="ghost" icon="arrow-left">
                {!! trans('main.Back') !!}
            </x-ui.button>

            <div class="flex flex-wrap items-center gap-2">
                @if (Auth::user()->can('tour.edit'))
                    <x-ui.button as="a" href="{{ route('tour.edit', ['tour' => $tour->id]) }}" variant="secondary" icon="edit">
                        {!! trans('main.Edit') !!}
                    </x-ui.button>
                @endif

                @if (Auth::user()->can('task.create'))
                    <x-ui.button as="a" href="{{ url('task') }}/create?tour={{ $tour->id }}" variant="secondary" icon="plus">
                        {!! trans('main.AddTask') !!}
                    </x-ui.button>
                @endif

                {{-- Export dropdown — Bootstrap-toggle JS preserved so existing
                     keyboard / outside-click behaviour keeps working. --}}
                <div class="btn-group">
                    <button type="button" class="inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <x-ui.icon name="download" />
                        Export
                    </button>
                    <div class="dropdown-menu min-w-[180px] rounded-md border border-slate-200 shadow-overlay">
                        <h6 class="px-3 pt-2 pb-1 text-xs font-medium uppercase tracking-wide text-slate-500">Export tour</h6>
                        <a class="dropdown-item text-sm" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'tour']) }}"); return false;'>
                            <span class="inline-flex items-center gap-2"><x-ui.icon name="file-spreadsheet" size="xs" />CSV — Tour</span>
                        </a>
                        <a class="dropdown-item text-sm" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'service']) }}"); return false;'>
                            <span class="inline-flex items-center gap-2"><x-ui.icon name="file-spreadsheet" size="xs" />CSV — Service</span>
                        </a>
                        <a class="dropdown-item text-sm" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'xlsx']) }}"); return false;'>
                            <span class="inline-flex items-center gap-2"><x-ui.icon name="file-spreadsheet" size="xs" />Excel</span>
                        </a>
                    </div>
                </div>

                {{-- Voucher dropdown --}}
                <div class="btn-group">
                    <button type="button" class="inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <x-ui.icon name="receipt" />
                        {!! trans('main.Voucher') !!}
                    </button>
                    <div class="dropdown-menu min-w-[160px] rounded-md border border-slate-200 shadow-overlay">
                        <a class="dropdown-item text-sm" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'voucher']) }}"); return false;'>
                            <span class="inline-flex items-center gap-2"><x-ui.icon name="file-text" size="xs" />PDF</span>
                        </a>
                        <a class="dropdown-item text-sm" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'voucher']) }}"); return false;'>
                            <span class="inline-flex items-center gap-2"><x-ui.icon name="file-text" size="xs" />DOC</span>
                        </a>
                    </div>
                </div>

                {{-- Itinerary dropdown --}}
                <div class="btn-group">
                    <button type="button" class="inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <x-ui.icon name="route" />
                        {!! trans('main.Itinerary') !!}
                    </button>
                    <div class="dropdown-menu min-w-[160px] rounded-md border border-slate-200 shadow-overlay">
                        <a class="dropdown-item text-sm" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'short']) }}"); return false;'>
                            <span class="inline-flex items-center gap-2"><x-ui.icon name="file-text" size="xs" />PDF</span>
                        </a>
                        <a class="dropdown-item text-sm" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_html_export', ['id' => $tour->id, 'type' => 'html']) }}"); return false;'>
                            <span class="inline-flex items-center gap-2"><x-ui.icon name="code" size="xs" />HTML</span>
                        </a>
                        <a class="dropdown-item text-sm" href="#" onclick='event.preventDefault(); export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'short']) }}"); return false;'>
                            <span class="inline-flex items-center gap-2"><x-ui.icon name="file-text" size="xs" />DOC</span>
                        </a>
                    </div>
                </div>

                {{-- Landing page modal trigger --}}
                <button type="button" onclick="showLandingPageModal()" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">
                    <x-ui.icon name="globe" />
                    Landing page
                </button>
            </div>
        </div>
    </header>

    {{-- Office selection. Keeps the .selectedOffice + .select-office-btn
         CSS hooks the page's existing JS listens on, and the legend
         modal toggle (data-bs-target=#legendModal) unchanged. --}}
    <div class="mb-4 rounded border border-slate-200 bg-white p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1 min-w-0">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Select office</label>
                <div class="flex flex-wrap items-stretch gap-2">
                    <select class="selectedOffice block h-9 min-w-0 flex-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ (isset($select_office->id) && $office->id == $select_office->id) ? 'selected' : '' }}>
                                {{ $office->office_name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="select-office-btn inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">
                        <x-ui.icon name="check" />
                        Select
                    </button>
                </div>
            </div>
            <button type="button"
                    data-bs-toggle="modal" data-bs-target="#legendModal"
                    class="inline-flex h-9 shrink-0 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <x-ui.icon name="help-circle" />
                Help
            </button>
        </div>
    </div>

    {{-- Convert quotation / tour banner. Same #check1/#check2 IDs +
         handleToggleConversion JS hook the existing scripts rely on. --}}
    @if ($tour->is_quotation)
        <div role="alert" class="mb-4 flex items-center justify-between gap-4 rounded border border-warning-600/30 bg-warning-50 px-4 py-3">
            <div class="flex items-center gap-3 text-warning-700">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-warning-600">
                    <x-ui.icon name="refresh-cw" />
                </span>
                <div>
                    <p class="text-sm font-semibold">Convert quotation to tour</p>
                    <p class="text-xs text-warning-700/80">Toggle to convert this quotation into an active tour.</p>
                </div>
            </div>
            <label class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center">
                <input type="checkbox" id="check1" onclick="handleToggleConversion(this, true)" checked class="peer sr-only" />
                <span class="block h-6 w-11 rounded-full bg-slate-300 transition-colors peer-checked:bg-warning-600"></span>
                <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
            </label>
        </div>
    @else
        <div role="alert" class="mb-4 flex items-center justify-between gap-4 rounded border border-success-600/30 bg-success-50 px-4 py-3">
            <div class="flex items-center gap-3 text-success-700">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-success-600">
                    <x-ui.icon name="refresh-cw" />
                </span>
                <div>
                    <p class="text-sm font-semibold">Convert tour to quotation</p>
                    <p class="text-xs text-success-700/80">Toggle to move this tour back into a quotation state.</p>
                </div>
            </div>
            <label class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center">
                <input type="checkbox" id="check2" onclick="handleToggleConversion(this, false)" class="peer sr-only" />
                <span class="block h-6 w-11 rounded-full bg-slate-300 transition-colors peer-checked:bg-success-600"></span>
                <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
            </label>
        </div>
    @endif

    {{-- Session messages --}}
    @if(session('message_buses'))
        <div class="mb-4 flex items-start gap-3 rounded border border-info-600/30 bg-info-50 px-4 py-3 text-sm text-info-700">
            <x-ui.icon name="info" class="mt-0.5 shrink-0" />
            <div class="flex-1">{{ session('message_buses') }}</div>
            <button type="button" class="rounded p-1 text-info-700/70 hover:bg-white/40" data-bs-dismiss="alert" aria-label="Close">
                <x-ui.icon name="x" size="xs" />
            </button>
        </div>
    @endif

    {{-- Tabs.
         Visual treatment is original (Tailwind underline tabs), but
         every anchor still carries href="#…-tab" + data-bs-toggle="tab"
         so Tabler's bundled Bootstrap tab-toggle JS keeps switching
         the panels below. CSS .nav-link.active hook preserved for
         that JS to write into. --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-1" role="tablist">
            <ul class="nav nav-tabs nav-tabs-underline -mb-px flex flex-nowrap gap-6 overflow-x-auto border-0 px-3 list-none pl-0 m-0 [&_.nav-link]:cursor-pointer" data-bs-toggle="tabs" role="tablist">
                @php
                    $tabBase = 'group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-3 text-sm transition-colors border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300';
                    $tabActive = '[&.active]:border-primary-600 [&.active]:text-primary-700 [&.active]:font-medium';
                    $tabClass = $tabBase . ' ' . $tabActive;
                @endphp
                <li class="nav-item" role="presentation">
                    <a href="#frontsheet-tab" class="nav-link active {{ $tabClass }}" data-bs-toggle="tab" aria-selected="true" role="tab">
                        <x-ui.icon name="file-text" />Front Sheet
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#service-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <x-ui.icon name="list" />{!! trans('main.Services') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#tour-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <x-ui.icon name="plane" />{!! trans('main.Tour') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#quotation-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <x-ui.icon name="calculator" />{!! trans('main.Quotation') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#roomlist-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <x-ui.icon name="bed" />{!! trans('main.GuestList') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#invoices-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <x-ui.icon name="receipt" />Invoices
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#billing-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <x-ui.icon name="banknote" />Billing
                    </a>
                </li>
            </ul>
        </div>
        <div class="p-5">
            <div class="tab-content">
            {{-- Front Sheet Tab --}}
            <div role="tabpanel" class="tab-pane active show" id="frontsheet-tab">
                <div class="mb-4 flex items-center gap-2">
                    <x-ui.icon name="file-text" size="sm" class="text-slate-400" />
                    <h3 class="text-sm font-semibold text-slate-700">Front Sheet — {{ $tour->external_name ?? $tour->name }} <span class="font-mono text-xs text-slate-500">#{{ $tour->id }}</span></h3>
                </div>

                @if(!empty($quotation) && isset($quotation->id))
                    @if(!empty($serviceDays))
                    @php
                        $peopleCount = 0;
                        foreach($listRoomsHotel as $room) {
                            $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code])
                                ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count : 0;
                        }
                    @endphp

                    {{-- Summary --}}
                    <div class="rounded border border-slate-200 bg-white px-4 py-3 mb-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Rooms</dt>
                            <dd class="mt-1 flex flex-wrap gap-1">
                                @foreach($listRoomsHotel as $room)
                                    <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                        {{ $room->room_types->code }} <span class="text-slate-500">×</span> {{ $room->count }}
                                    </span>
                                @endforeach
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Pax</dt>
                            <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $tour->pax }} @if($tour->pax_free)<span class="text-slate-400 text-sm font-normal">+ {{ $tour->pax_free }} free</span>@endif</dd>
                        </div>
                    </div>

                    @if ($peopleCount != $tour->pax + $tour->pax_free)
                        <div class="mb-3 flex items-start gap-2 rounded border border-warning-600/20 bg-warning-50 px-3 py-2 text-sm text-warning-700">
                            <x-ui.icon name="alert-triangle" size="sm" class="mt-0.5 text-warning-600" />
                            <div class="flex-1">
                                Pax Count ({{ $tour->pax + $tour->pax_free }}) is not equal to the number of people in the rooms ({{ $peopleCount }})
                            </div>
                        </div>
                    @endif

                    {{-- Front Sheet Table --}}
                    <div class="rounded border border-slate-200 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 align-middle text-sm">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2" style="min-width: 100px;">Date</th>
                                    <th class="px-3 py-2" style="min-width: 80px;">City</th>
                                    <th class="px-3 py-2" style="min-width: 100px;">Quote Single</th>
                                    <th class="px-3 py-2" style="min-width: 100px;">Quote SS</th>
                                    <th class="px-3 py-2" style="min-width: 100px;">Quote HPP</th>
                                    <th class="px-3 py-2" style="min-width: 200px;">CMFD Hotel</th>
                                    <th class="px-3 py-2" style="min-width: 80px;">Option</th>
                                    <th class="px-3 py-2" style="min-width: 100px;">Offer SS</th>
                                    <th class="px-3 py-2" style="min-width: 100px;">Offer HPP</th>
                                    <th class="px-3 py-2" style="min-width: 80px;">®</th>
                                    <th class="px-3 py-2" style="min-width: 120px;">VC sent to SHA</th>
                                    <th class="px-3 py-2" style="min-width: 120px;">Budget HPP +/-</th>
                                </tr>
                            </thead>
                            <!-- <tbody>
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
                            </tbody> -->
                                                                        <tbody>
                                                @php
                                                    $overallSum = 0;
                                                    $count = 0;
                                                @endphp
                                                @foreach ($tour->getTourDaysSortedByDate() as $tourDay)
                                                    @php
                                                        $tourday_hotels = count($tourDay->hotels()) > 0 ? count($tourDay->hotels()) : 1;

                                                        $offer_hotel_count = 0;
                                                        if (count($tourDay->hotels()) != 0) {
                                                            foreach ($tourDay->hotels() as $hotel) {
                                                                $offer_hotel_count = $offer_hotel_count + count($hotel->hotel_offers);
                                                            }
                                                            $total = count($tourDay->hotels());
                                                        } else {
                                                            $offer_hotel_count = 1;
                                                            $total = 1;
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td rowspan="{{ $total }}">{{ $tourDay->date ?? '' }}
                                                        </td>

                                                        @if (count($tourDay->hotels()) != 0)
                                                            @foreach ($tourDay->hotels() as $hotel)
                                                                @php
																	if (!empty($quotation)){
														$quotehtlpp = (int)$quotation->getValueByDate($tourDay->date?? '', "htlpp");
                                                                    $quotationBudget = (int)$quotation->getValueByDate($tourDay->date?? '', "SIN") + $quotehtlpp;
														}else{
														$quotehtlpp = 0;
														 $quotationBudget =0;
														}
														
														            
                                                                    $realBudget = 0;
                                                                    $first_hotel = $tourDay->firstHotel();
                                                                    $count += 1;
                                                                    $offer_hotel_count = count($hotel->hotel_offers) > 0 ? count($hotel->hotel_offers) : 1;
                                                                    $offer_hotel_count = $offer_hotel_count + 1;

                                                                @endphp


                                                                <td>
                                                                    @if (!is_null($hotel) && method_exists($hotel, 'service') && isset($hotel->service()->cityObject))
                                                                        {{ $hotel->service()->cityObject->name ?? '' }}
                                                                    @endif
                                                                </td>
														@if (!empty($quotation))
														<td>{{(int)$quotation->getValueByDate($tourDay->date?? '', "SIN")+ (int)$quotation->getValueByDate($tourDay->date?? '', "htlpp")}}</td>
														<td>{{$quotation->getValueByDate($tourDay->date?? '', "SIN")?? ''}}</td>
														<td>{{$quotation->getValueByDate($tourDay->date?? '', "htlpp")?? ''}}</td>
														@else
														<td></td>
														<td></td>
														@endif
                                                                


                                                                <td>

                                                                    {{ $hotel->name ?? '' }}


                                                                </td>
                                                                
																@php $total_count = count($listRoomsHotel); $budjet = true; $count_room = 0; $hotelpp = 0;
														$ssp =0; $single =0;@endphp
                                                                @if (count($hotel->hotel_offers) != 0)
                                                                    <td rowspan="1">
                                                                        {{ $hotel->latestHotelOffer->status }}</td>
														
                                                                    
                                                                    @foreach ($listRoomsHotel as $selected_room_type)
																	
																		@php 
														
														
														
														if($selected_room_type->room_types->code == "SIN"){
														$single += $hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types); 
														}else if( $selected_room_type->room_types->code == "TWN" || $selected_room_type->room_types->code == "DOU"){
														$hotelpp += $hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types)/2; 
														
														
														$count_room += 1;
														}
														
														@endphp
														@if ($selected_room_type->room_types->code == 'SIN')
                                                                        <td>{{ $hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types) }}
                                                                        </td>
																	@endif
                                                                    @endforeach
                                                                @else
                                                                    <td></td>
                                                                    @foreach ($listRoomsHotel as $selected_room_type)
																		@if ($selected_room_type->room_types->code == 'SIN')
                                                                        <td></td>
																		@endif
                                                                    @endforeach
                                                                @endif
														@php
														if( $count_room == 0){
														$count_room = 1;
														}
														$hotelpp = $hotelpp/$count_room ;
														$ssp = abs($single - $hotelpp);
														$realBudget = $ssp + $hotelpp ;
														@endphp
																	<td rowspan="1">{{$ssp??""}}</td>
														<td rowspan="1">{{$hotelpp??""}}</td>
                                                                <td>{{ Form::checkbox('rooming_list_reserved[]', $comparison->comparisonRowByDate($tourDay->date)->id ?? '', $comparison->comparisonRowByDate($tourDay->date)->rooming_list_reserved ?? '', ['class' => 'rooming_list_reserved']) }}
                                                                </td>
                                                                <td>{{ Form::checkbox('visa_confirmation[]', $comparison->comparisonRowByDate($tourDay->date)->id ?? '', $comparison->comparisonRowByDate($tourDay->date)->visa_confirmation ?? '', ['class' => 'visa_confirmation']) }}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-block comments-button"
                                                                        data-row-id="{{ $comparison->comparisonRowByDate($tourDay->date)->id ?? '' }}"
                                                                        data-link=" ">
                                                                        <span
                                                                            class="badge bg-yellow">{{ \App\Helper\AdminHelper::getComparisonRowCommentsCount($comparison->comparisonRowByDate($tourDay->date)->id ?? '') }}</span>
                                                                        <i class="ti ti-message-circle"
                                                                            aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
														<td data-toggle="tooltip" data-placement="top"
                                                                    title="({{ $quotehtlpp }} - ({{ $hotelpp }})) ">
                                                                    @php
                                                             
                                                                            $sum = $quotehtlpp - $hotelpp ;
                                                                      
                                                                    @endphp
                                                                    {{ round($sum, 2) }}
                                                                </td>
														{{--
                                                                <td data-toggle="tooltip" data-placement="top"
                                                                    title="({{ $quotationBudget }} - ({{ $realBudget }} + {{ $cityTax }})) / {{ $tour->pax }}">
                                                                    @php
                                                                        if ($tour->pax != 0) {
                                                                            $sum = ($quotationBudget - ($realBudget + $cityTax)) / $tour->pax;
                                                                        } else {
                                                                            $sum = 0;
                                                                        }
                                                                        $overallSum += $sum;
                                                                    @endphp
                                                                    {{ round($sum, 2) }}
                                                                </td>--}}


                                                    </tr>
                                                @endforeach
                                                @php $count = 0; @endphp
                                            @else
                                                @for ($i = 1; $i < 10; $i++)
                                                    <td rowspan="{{ $total }}"></td>
                                                @endfor
                                                @foreach ($listRoomsHotel as $selected_room_type)
                                                    <td></td>
                                                    <td></td>
                                                @endforeach
                                                @endif
                                                @endforeach
                                                <tr rowspan="{{ $offer_hotel_count }}">
                                                    <td colspan="{{ 8 + count($listRoomsHotel) * 2 }}">
                                                        {!! trans('main.ENDOFSERVICE') !!}</td>
                                                    <td>&#931; =</td>
                                                    <td>{{ round($overallSum, 2) }}</td>
                                                </tr>

                                                <!--  Bottom  -->
                                            </tbody>
                        </table>
                    </div>

                    {{-- Column descriptions --}}
                    <div class="mt-4 rounded border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-4 py-3">
                            <h5 class="text-sm font-medium text-slate-700">Column descriptions</h5>
                        </div>
                        <div class="px-4 py-3 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1.5 text-sm text-slate-700">
                            <div><span class="font-medium text-slate-900">Quote Single:</span> Single room quoted price</div>
                            <div><span class="font-medium text-slate-900">Option:</span> Option status</div>
                            <div><span class="font-medium text-slate-900">Quote SS:</span> Single supplement quoted price</div>
                            <div><span class="font-medium text-slate-900">Offer SS/HPP:</span> Offered prices</div>
                            <div><span class="font-medium text-slate-900">Quote HPP:</span> Half-board per person quoted price</div>
                            <div><span class="font-medium text-slate-900">®:</span> Registered / Confirmed</div>
                            <div><span class="font-medium text-slate-900">CMFD Hotel:</span> Confirmed hotel name</div>
                            <div><span class="font-medium text-slate-900">VC sent to SHA:</span> Voucher / Confirmation sent</div>
                            <div></div>
                            <div><span class="font-medium text-slate-900">Budget HPP +/-:</span> Budget variance</div>
                        </div>
                    </div>
                    @else
                        <div class="flex items-start gap-2 rounded border border-info-600/20 bg-info-50 px-3 py-2 text-sm text-info-700">
                            <x-ui.icon name="info" size="sm" class="mt-0.5 text-info-600" />
                            <div class="flex-1">{{ __('No service schedule is available for this tour yet.') }}</div>
                        </div>
                    @endif
                @else
                    <div class="flex items-start gap-2 rounded border border-info-600/20 bg-info-50 px-3 py-2 text-sm text-info-700">
                        <x-ui.icon name="info" size="sm" class="mt-0.5 text-info-600" />
                        <div class="flex-1">No quotation data available for front sheet. Please create a quotation first.</div>
                    </div>
                @endif
            </div>

            {{-- Services Tab --}}
            <div role="tabpanel" class="tab-pane" id="service-tab">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="list" size="sm" class="text-slate-400" />
                        <h3 class="text-sm font-semibold text-slate-700">Services</h3>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="inline-flex rounded-md shadow-subtle">
                            <button type="button" onclick="addDay()"
                                    class="inline-flex h-8 items-center gap-1 rounded-l border border-r-0 border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                <x-ui.icon name="plus" size="xs" />Day
                            </button>
                            <button type="button" onclick="addAllDays()"
                                    class="inline-flex h-8 items-center gap-1 rounded-r border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Add all
                            </button>
                        </div>
                        <div class="inline-flex rounded-md shadow-subtle">
                            @foreach([['City','exportCity()'],['Excel','exportExcel()'],['Number','exportNumber()'],['Itinerary','exportItinerary()'],['Print All','printAll()']] as $i => $btn)
                                <button type="button" onclick="{{ $btn[1] }}"
                                        class="inline-flex h-8 items-center gap-1 border border-slate-300 bg-white px-3 text-xs font-medium text-slate-700 hover:bg-slate-50
                                               {{ $i === 0 ? 'rounded-l' : '' }} {{ $i === 4 ? 'rounded-r border-l-0' : ($i > 0 ? 'border-l-0' : '') }}">
                                    {{ $btn[0] }}
                                </button>
                            @endforeach
                        </div>
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
                    <div class="mb-3 flex items-start gap-2 rounded border border-warning-600/20 bg-warning-50 px-3 py-2 text-sm text-warning-700">
                        <x-ui.icon name="alert-triangle" size="sm" class="mt-0.5 text-warning-600" />
                        <div class="flex-1">
                            Pax Count ({{ $tour->pax + $tour->pax_free }}) is not equal to the number of people in the rooms ({{ $peopleCount }})
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
                        <div class=" mb-4" data-day="{{ $day['day_number'] }}" data-date="{{ $day['date_key'] }}" @if($tourDayId) data-tour-day-id="{{ $tourDayId }}" @endif>



                 <?php $countDay = 0; ?>
	<div class="block-error-driver mb-3 hidden rounded border border-info-600/20 bg-info-50 px-3 py-2 text-sm text-info-700 text-center">
        		</div>
    @foreach($tourDates as $tourDate)
        <?php $countDay++ ?>
		
        <div class="" >
			 
            <div class="rounded border border-slate-200 bg-white mb-3">
                <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2 bg-slate-50">
                    <x-ui.icon name="calendar-days" size="sm" class="text-slate-400" />
                    <h3 class="text-sm font-semibold text-slate-700">{!!trans('main.Day')!!} {{ $countDay }} — {{ (new \Carbon\Carbon($tourDate->date))->formatLocalized('%B %d, %Y (%A)') }}</h3>
                </div>
                <div class="p-2 overflow-x-auto">
					
                    <table class="table table-striped table-bordered table-hover {{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'package-service-table' : '' }}"
                           style='background:#fff'>
                        <colgroup>
                            <col style="width: auto">
                            <col style="width: auto">
                            <col style="width: auto">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <!--<col style="width: 30%;">
                            <col style="width: auto;">-->
                            <col style="width: auto;">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>Itn</th>
                            <th>Vch</th>
                            <th>{!!trans('main.FromTime')!!}</th>
                            <th></th>
                            <th style="width: 15%; min-width: 100px;">{!!trans('main.Name')!!}</th>
                            <th style="min-width: 150px">{!!trans('main.Status')!!}</th>
                            <th>Pax</th>
                            <th>Rooms</th>
                            <th>{!!trans('main.Description')!!}</th>
							<th>Offers</th>
                        {{--<th>Price for one</th>--}}
                        <!-- <th style="width: 120px">Rooms Hotel</th>-->
                            <th style="width: 150px; min-width: 150px">{!!trans('main.Actions')!!}</th>
                        </tr>
                        </thead>
                        
                        <tbody data-default_tour_day_id='{{$tourDate->id}}'>
                            
                            @php
                            $count =  $tourDate->packages->count();
                            
                            @endphp
                            @if($count > 0 )
                        @foreach($tourDate->packages as $package)
							
							<tr >
								<td colspan="14">@if(Auth::user()->can('tour_package.create'))
                    <button class="btn btn-flat btn-success pull-right add-service-quick"
                            data-tourDayId="{{$tourDate->id}}"
                            data-link="{{route('tour_package.store')}}" data-date="{!! $tourDate->date !!}"
                            data-tour_id='{{$tour->id}}'
                            data-departure_date='{{$tour->departure_date}}'
                            data-retirement_date="{{$tour->retirement_date}}">{!!trans('main.AddService')!!}
                    </button>
                    <button class="btn btn-flat btn-success pull-right add-description-package" dayid="{{$tourDate->id}}" onclick=loadDescriptionemplate()>{!!trans('main.Adddescription')!!}</button>
                @endif</td>
							</tr>
                            @if($package->description_package)
								
                                <tr data-package_id='{{$package->id}}' data-type='{{$package->type}}'
                                    data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif' >
                                    <td valign="center" align="center" class="not-click">
                                        @if($package->itn == 1)
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected" checked>
                                        @else
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected" >
                                        @endif
                                    </td>
                                    <td valign="center" align="center" class="not-click">
                                        {{--
                                        {{ $package->id }} is desc
                                        @if ($package->hasChild()) is has child : {{ $package->getChild()['id'] }}  {{ $package->getChild()['time_from'] }}  @else null  @endif
                                        @if ($package->hasParrent()) is has parent: {{  $package->parrent()['id'] }} {{  $package->parrent()['time_from'] }} @else null  @endif --}}
                                        @if($package->vch == 1)
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected_vch"
                                               checked>
                                        @else
                                               <input type="checkbox" value="{{$package->id}}" class="export_selected_vch"
                                               >
                                        @endif
                                    </td>
                                    <td>
                                        <input style="width: 80px; background-color: inherit; border-style: none;"
                                               type="text"
                                               data-package_id="{{$package->id}}"
                                               class="form-control timepicker {{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-time' : '' }}"
                                               name="time_from"
                                               value="{!! $package->time_from !!}">
                                        <span class="package-data" data-type='{{$package->type}}'
                                              data-tour_day_id='{{$tourDate->id}}' data-package_id='{{$package->id}}'
                                              data-package-type="service_description"
                                              data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                                        ></span>
                                    </td>
									<td colspan="9">
                                    <div 
                                        class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-description' : '' }}" id="service-description{{$package->id}}">{!! $package->description !!}</div>
										@if(strlen($package->description )>1000)
										<p style="color:blue;width: 100%;float: right;" id="readmore{{$package->id}}" onclick="toggleReadMore(this,{{$package->id}})">readmore</p>
										@endif
									</td>
									<td></td>
                                    <td style="text-align: center">
                                        @if(Auth::user()->can('tour_package.destroy'))
                                            <a data-toggle="modal" data-target="#myModal"
                                               class="delete btn btn-danger btn-xs"
                                               data-link="/tour_package/{{$package->id}}/deleteMsg"
                                               style="text-align: center"><i class="ti ti-trash"
                                                                             aria-hidden="true"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if(!$package->description_package)
								@php
								$background = "";
								if($package->service()->service_type??"" == "Hotel"){
						
									if($package->status == 23){
										$background = "background: rgb(202, 255, 189);";
										}
								
								}else{
									if($package->status == 9){
										$background = "background: rgb(202, 255, 189);";
										}
									}
								@endphp
                                <tr data-package_id='{{$package->id}}'
                                    data-type='{{@$package->service()->service_type??""}}'
                                    data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                                    class="tour-package-item" style="{{$background}}">
                                    <td valign="center" align="center" class="not-click">
										@if(@$package->fellow_hotel_confirm == 0)
                                        @if($package->itn == 1)
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected" checked>
                                        @else
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected" >
                                        @endif
										@endif
                                    </td>
                                    <td valign="center" align="center" class="not-click">

                                        @php
                                            $menu = '';
                                            $tourid = null ;
                                        @endphp
                                        @if ($package->type == 0 || $package->type == 4)
                                            @php
										if(!empty(@$package->service()->menus)){
                                                if(count(@$package->service()->menus) > 0){
													
                                                    foreach(@$package->service()->menus as $men){
									
                                                     //if ($men['id'] == $package->menu_id){
                                                            $menu = $men['name'];
                                                       // }
                                                    }
                                                }
										}
						
                                            @endphp
                                        @endif
										@if(@$package->fellow_hotel_confirm == 0)
                                        @if($package->vch == 1)
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected_vch"
                                               checked>
                                        @else
                                               <input type="checkbox" value="{{$package->id}}" class="export_selected_vch"
                                               >
                                        @endif
										@endif
                                    </td>
                                    <td class="not-click">
                                        @if(!$package->hasParrent())
                                            @if(\App\Helper\PermissionHelper::checkPermission('tour_package.edit'))
                                                <input style="width: 80px; background-color: inherit; border-style: none;"
                                                       type="text"
                                                       data-package_id="{{$package->id}}"
                                                       class="form-control timepicker service-time"
                                                       name="time_from"
                                                       value="{!! $package->time_from !!}"
                                                       data-type='{{@$package->service()->service_type??""}}'
                                                       data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                                                >
                                            @else
                                                <span>{{ $package->time_from }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    {{-- Parent or child hotel--}}
                                    <td>
                                        @if(@$package->service()->service_type??"" === 'Hotel')
                                            @if($package->parent_id)
                                                <i class="ti ti-star text-warning" title="Child hotel"></i>
                                            @else
                                                <i class="ti ti-star-filled text-warning" title="Parent hotel"></i>
                                            @endif

                                        @endif
                                    </td>
                                    <td><span class="package-data" data-tour_day_id='{{$tourDate->id}}'
                                              data-package_id='{{$package->id}}'
                                              data-package-type="{{@$package->service()->service_type?? '' }}"
                                              data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                                        >{{$package->name  . ' (' .@$package->service()->service_type??"". ')'}}{{  @$package->service()->address_first}} {!! @$package->service()->work_email!!} {!! @$package->service()->work_phone!!}</span><br>
										@if(@$package->service()->service_type??"" == 'Transfer' || @$package->service()->service_type??"" == 'Guide')
										<span>Pickup:{{ $tourDate->date. " " .$package->time_from }}/{{ $package->pickup_des }} at {{ $package->time_to }} Dropoff: {{ $tourDate->date. " " .$package->time_to }}/{{ $package->drop_des }} at {{ $package->time_to }}</span>
										@endif
									
									</td>
                                    @if(!$statusPackage->isEmpty())
                                        @php
                                            $status = false;
                                            $status_name = '';
                                        @endphp
                                        @foreach($statusPackage as $item)
                                            @if($item->id == $package->status)

                                                <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'tour_package_status' : '' }}"
                                                    data-info-package-id="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                                    data-info-status="{{$item->name}}"
                                                    data-info-status_id="{{$item->id}}"
                                                    data-info-package-type="{{(@$package->service()->service_type??"" === 'Hotel') ? 'hotel':  '' }}{{(@$package->service()->service_type??"" === 'Transfer') ? 'transfer':  '' }}">
                                                    {{$item->name}} </td>
                                                @php $status_name = $item->name; @endphp
                                                @php
                                                    $status = true;
                                                @endphp
                                            @else

                                            @endif
                                        @endforeach
                                        @if (!$status)
                                            <td></td>
                                        @endif

                                    @else
                                        <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'tour_package_status' : '' }}"
                                            data-info-package-id="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                            data-info-status="{{$package->status}}"
                                            data-info-status_id="{{$package->status->id}}"
                                            data-info-package-type="{{(@$package->service()->service_type??"" === 'Hotel') ? 'hotel':  '' }}{{(@$package->service()->service_type??"" === 'Transfer') ? 'transfer':  '' }}">
                                            {{$package->status}}
                                        </td>
                                    @endif
                                   
                                    <td>{{ $package->pax }} {{$package->pax_free}}</td>
                                    <td>
                                        @foreach($package->room_types_hotel as $item)
                                            <span>
                                                {{$item->room_types->code}}
                                                {{$item->count}}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-description' : '' }}">{!! $package->description !!}</td>
									@php $offer_hotel_count = count($package->hotel_offers) > 0 ? count($package->hotel_offers) : 0; @endphp
									@if($package->service()->service_type??"" == "Hotel")
									<td><table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>HPP</th>
										
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($package->hotel_offers as $offer)
									@php 
									$hotelpp= 0;
									if(!empty( $offer)){
										foreach ($offer->offer_room_prices as $offer_room_price) {
											if ($offer_room_price->room_type_id == 2 || $offer_room_price->room_type_id == 6) {
												$hotelpp = $offer_room_price->price/2;
											}
										}
														
									}
														
														@endphp
                                        <tr>
                                            <td>{{ $offer->id ?? '' }}</td>
                                            <td>{{ $offer->status ?? '' }}</td>
                                            <td>{{ $hotelpp ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
										<a href='/tour_package/hotel_offers/{{ $package->parent_id != null ? $package->parent_id : $package->id }}' class="btn btn-primary">Show Offers</a>
									</td>
									@else
									<td></td>
									@endif
                                    {{--<td>{!! $package->total_amount !!}</td>--}}
                                    {{--<td>
                                        @foreach($package->room_types_hotel as $item)
                                            <span>
                                                {{ $item->room_types->code }}
                                                {{ $item->count }} - {{ $item->price }}
                                            </span>
                                            <br>
                                        @endforeach
                                    </td>--}}
                                    @if(!$package->hasParrent())
                                        <td style="text-align: center;width:150px;">
                                            {{-- Package service is hotel --}}
											@if(@$package->fellow_hotel_confirm == 0)
                                            @if ($package->type == 0)
                                                @php $tourid = $tour->id; @endphp
                                                <button class="{{ \App\Helper\PermissionHelper::checkPermission('comparison.show') ? 'main-hotel' : 'disabled' }} btn btn-xs {{$package->main_hotel ? 'btn-success' : 'btn-danger'}}"
                                                        type="button" style="margin-bottom: 5px">M
                                                </button>
                                            @endif
											@endif
											@if(@$package->fellow_hotel_confirm == 0)
                                            @if(Auth::user()->can('tour_package.edit'))
                                                <a href="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/edit"
                                                   class="btn btn-primary btn-xs show-button p-0"
                                                   data-link="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/edit"
                                                   style="margin-bottom: 5px"><i
                                                            class="ti ti-edit icon m-0" aria-hidden="true"></i></a>
                                            @endif
											@endif
                                            @if(Auth::user()->can('tour_package.destroy'))
                                                <a data-toggle="modal" data-target="#myModal"
                                                   class="delete btn btn-danger btn-xs p-0" style="margin-bottom: 5px"
                                                   data-link="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/deleteMsg"><i
                                                            class=" ti ti-trash icon m-0" aria-hidden="true"></i></a>
                                            @endif
											@if(@$package->fellow_hotel_confirm == 0)
                                            @if($package->parent_id == null)
                                                <!-- @if(Auth::user()->can('tour_package.create') && Auth::user()->can('tour_package.destroy'))
                                                    <a href="#" class="btn btn-warning btn-xs open-modal-change-service"
                                                       style="display: {{ @$package->getStatusName() !== 'Confirmed' ? 'inline-flex' : 'none' }}; margin-bottom: 5px; padding:0;"
                                                       data-toggle="modal"
                                                       data-target="#list-tour-packages"
                                                       data-serviceTypeId="{{ $package->type }}"
                                                       data-serviceId="{{ @$package->service()->id }}"
                                                       data-packageId="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                                       data-tour-day-id="{{$tourDate->id}}"
                                                       data-tour_id="{{$package->getTour()->id}}"
                                                       data-time-old-service="{{!$package->hasParrent() ? $package->time_from : null }}"
                                                       data-link="{{route('tour_package.store')}}"
                                                    >
                                                        <i class="ti ti-exchange icon m-0" aria-hidden="true"></i>
                                                    </a>
                                                @endif -->
												@endif
                                                @if(Auth::user()->can('tour_package.edit'))
                                                    @if(@$package->fellow_hotel_confirm == 0)
											@php $packageName = addslashes($package->name);$tourName = addslashes($tour->name);
											$desc = addslashes($package->description);
											@endphp
                                                        <button style="margin-bottom: 5px;padding:0px;" href="javascript:void(0);"
                                                           data-info="{{@$package ?: \GuzzleHttp\json_encode(' ')}}"
                                                           onclick="loadTemplate(JSON.parse($(this).attr('data-info')) ? JSON.parse($(this).attr('data-info')).type : '','{!! @$package->service()->work_email !!}','{!! $packageName !!}','{!! $package->pax !!} {!! $package->pax_free !!}','', '{!! @$package->service()->work_email !!}','{!! @$package->service()->work_phone !!}','{!! $desc!!}','{!! $status_name !!}','{!! $package->time_from  !!}','{!! $package->time_to!!}','{!! $package->supplier_url!!}','{!! $package->total_amount !!}','{{ $menu }}','{{ $tour->id }}','{{ $package->reference }}','{{ $tourName }}','{{ $package->id }}');"
                                                           class="btn btn-success btn-xs"
                                                        ><i class="ti ti-mail icon m-0" aria-hidden="true"></i></button>
													
                                                  @endif
													
                                                @endif
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endif



                        @endforeach


       @else
       <tr>

           <td colspan="14">@if(Auth::user()->can('tour_package.create'))
<button class="btn btn-flat btn-success pull-right add-service-quick"
data-tourDayId="{{$tourDate->id}}"
data-link="{{route('tour_package.store')}}" data-date="{!! $tourDate->date !!}"
data-tour_id='{{$tour->id}}'
data-departure_date='{{$tour->departure_date}}'
data-retirement_date="{{$tour->retirement_date}}">{!!trans('main.AddService')!!}
</button>
<button class="btn btn-flat btn-success pull-right add-description-package" dayid="{{$tourDate->id}}" onclick=loadDescriptionemplate()>{!!trans('main.Adddescription')!!}</button>
@endif</td>
       </tr>

       @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

                           

                        </div>
                        @endforeach
                    @else
                        <div class="flex items-start gap-2 rounded border border-info-600/20 bg-info-50 px-3 py-2 text-sm text-info-700">
                            <x-ui.icon name="info" size="sm" class="mt-0.5 text-info-600" />
                            <div class="flex-1">{{ __('No service days are configured for this tour yet.') }}</div>
                        </div>
                    @endif
                </div>

                {{-- Comments Section --}}
                <div class="mt-6 rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="message-circle" size="sm" class="text-slate-400" />
                        <h3 class="text-sm font-medium text-slate-700">{!! trans('main.Comments') !!}</h3>
                    </div>
                    <div class="px-4 py-4">
                        <div id="show_comments" class="max-h-80 overflow-y-auto"></div>
                    </div>
                    <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 rounded-b">
                        <form method="POST" action="{{ route('comment.store') }}" id="form_comment" class="space-y-3">
                            @csrf
                            <textarea class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                      id="content" name="content" rows="3" placeholder="Add a comment — Ctrl + Enter to post"></textarea>
                            <input type="hidden" name="reference_id" value="{{ $tour->id }}">
                            <input type="hidden" name="reference_type" value="{{ \App\Comment::$services['tour'] ?? 'tour' }}">
                            <div class="flex">
                                <button type="submit" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                                    <x-ui.icon name="send" size="sm" />
                                    {!! trans('main.Send') !!}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tour Info Tab --}}
            <div role="tabpanel" class="tab-pane" id="tour-tab">
                <div class="mb-4 flex items-center gap-2">
                    <x-ui.icon name="plane" size="sm" class="text-slate-400" />
                    <h3 class="text-sm font-semibold text-slate-700">Tour information</h3>
                </div>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Name') !!}</dt>
                        <dd class="mt-0.5 text-slate-800">{{ $tour->name ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.DepDate') !!}</dt>
                        <dd class="mt-0.5 text-slate-800">{{ $tour->departure_date ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.ExternalName') !!}</dt>
                        <dd class="mt-0.5 text-slate-800">{{ $tour->external_name ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.RetDate') !!}</dt>
                        <dd class="mt-0.5 text-slate-800">{{ $tour->retirement_date ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Pax') !!}</dt>
                        <dd class="mt-0.5 text-slate-800">{{ $tour->pax ?? '—' }} @if($tour->pax_free)<span class="text-slate-400">+ {{ $tour->pax_free }} free</span>@endif</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Status') !!}</dt>
                        <dd class="mt-0.5">
                            @if($status->name ?? null)
                                <span class="inline-flex items-center rounded bg-info-50 px-2 py-0.5 text-xs font-medium text-info-700">{{ $status->name }}</span>
                            @else — @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.PaxFree') !!}</dt>
                        <dd class="mt-0.5 text-slate-800">{{ $tour->pax_free ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Phone') !!}</dt>
                        <dd class="mt-0.5">
                            @if($tour->phone)<a href="tel:{{ $tour->phone }}" class="text-primary-700 hover:underline">{{ $tour->phone }}</a>
                            @else <span class="text-slate-400">—</span>@endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Quotations Tab --}}
            <div role="tabpanel" class="tab-pane" id="quotation-tab">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="calculator" size="sm" class="text-slate-400" />
                        <h3 class="text-sm font-semibold text-slate-700">Quotations</h3>
                    </div>
                    @if(Auth::user()->can('quotation.add'))
                        <x-ui.button as="a" href="{{ route('quotation.add', ['id' => $tour->id]) }}" icon="plus" size="sm">
                            {!! trans('main.AddQuotation') !!}
                        </x-ui.button>
                    @endif
                </div>

                <div class="overflow-x-auto rounded border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">{!! trans('main.Name') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.Assigned') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.Frontsheet') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.CreatedAt') !!}</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($tour->quotations as $quotation)
                                <tr class="hover:bg-slate-50 {{ $quotation->is_confirm == 0 ? 'bg-danger-50/40' : 'bg-success-50/40' }}">
                                    <td class="px-3 py-2">
                                        @if(Auth::user()->can('quotation.edit'))
                                            <a href="{{ route('quotation.edit', ['quotation' => $quotation->id]) }}" class="font-medium text-primary-700 hover:underline">
                                                {{ $quotation->name ?: '—' }}
                                            </a>
                                        @else
                                            <span class="font-medium text-slate-800">{{ $quotation->name ?: '—' }}</span>
                                        @endif
                                        @if($quotation->is_confirm == 0)
                                            <span class="ml-2 inline-flex items-center rounded bg-danger-50 px-1.5 py-0.5 text-[10px] font-medium text-danger-700 uppercase">Draft</span>
                                        @else
                                            <span class="ml-2 inline-flex items-center rounded bg-success-50 px-1.5 py-0.5 text-[10px] font-medium text-success-700 uppercase">Confirmed</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-slate-700">{{ $quotation->userName() ?: '—' }}</td>
                                    <td class="px-3 py-2">
                                        @if(Auth::user()->can('comparison.show'))
                                            <a href="{{ route('comparison.show', ['comparison' => $quotation->id]) }}" onclick="event.stopPropagation();"
                                               class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:text-primary-800">
                                                <x-ui.icon name="layout-grid" size="xs" />Front Sheet
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-slate-500 text-xs whitespace-nowrap">{{ Carbon\Carbon::parse($quotation->created_at)->format('d-m-Y') }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('quotation.pdf', ['id' => $quotation->id]) }}" target="_blank"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700" title="Print PDF">
                                                <x-ui.icon name="printer" size="sm" />
                                            </a>
                                            <a href="{{ route('quotation.excel', ['id' => $quotation->id]) }}" target="_blank"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-success-700" title="Excel">
                                                <x-ui.icon name="file-spreadsheet" size="sm" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-8 text-center text-sm text-slate-500">No quotations found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Guest List Tab --}}
            <div role="tabpanel" class="tab-pane" id="roomlist-tab">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="bed" size="sm" class="text-slate-400" />
                        <h3 class="text-sm font-semibold text-slate-700">Guest lists</h3>
                    </div>
                    @if(Auth::user()->can('guestList.add'))
                        <x-ui.button as="a" href="{{ route('guestList.add', ['id' => $tour->id]) }}" icon="plus" size="sm">
                            Add guest list
                        </x-ui.button>
                    @endif
                </div>

                <div class="overflow-x-auto rounded border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">Version</th>
                                <th class="px-3 py-2">{!! trans('main.Name') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.Author') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.CreatedAt') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.SentAt') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.Hotels') !!}</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($tour->guestLists as $guestList)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 font-mono text-xs text-slate-500">{{ $guestList->version }}</td>
                                    <td class="px-3 py-2">
                                        @if(Auth::user()->can('guestList.showbyid'))
                                            <a href="{{ route('guestList.showbyid', ['id' => $guestList->id]) }}" class="font-medium text-primary-700 hover:underline">
                                                {{ $guestList->name }}
                                            </a>
                                        @else
                                            <span class="font-medium text-slate-800">{{ $guestList->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-slate-700">{{ $guestList->getAuthor()->name ?? '—' }}</td>
                                    <td class="px-3 py-2 text-slate-500 text-xs whitespace-nowrap">{{ Carbon\Carbon::parse($guestList->created_at)->format('d-m-Y') }}</td>
                                    <td class="px-3 py-2 text-xs whitespace-nowrap">
                                        @if($guestList->sent_at)
                                            <span class="inline-flex items-center gap-1 rounded bg-success-50 px-2 py-0.5 text-xs font-medium text-success-700">
                                                <x-ui.icon name="check" size="xs" />{{ Carbon\Carbon::parse($guestList->sent_at)->format('d-m-Y') }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">Not sent</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-slate-700 max-w-xs">
                                        @foreach($guestList->getSelectedHotelNames() as $index => $hotelName)
                                            {{ $hotelName }}{{ $index < count($guestList->getSelectedHotelNames()) - 1 ? ', ' : '' }}
                                        @endforeach
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-end gap-1">
                                            @if(!$guestList->sent_at)
                                                <button class="send-guest-list inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                                        data-url="{{ route('guestlist.send', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}" title="Send">
                                                    <x-ui.icon name="send" size="sm" />
                                                </button>
                                                <button class="delete-guest-list inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                                        data-url="{{ route('guestlist.delete', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}" title="Delete">
                                                    <x-ui.icon name="trash-2" size="sm" />
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500">No guest lists found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Invoices Tab --}}
            <div role="tabpanel" class="tab-pane" id="invoices-tab">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="receipt" size="sm" class="text-slate-400" />
                        <h3 class="text-sm font-semibold text-slate-700">Invoices</h3>
                    </div>
                    @if(Auth::user()->can('invoices.create'))
                        <x-ui.button as="a" href="{{ route('invoices.create', ['tour_id' => $tour->id]) }}" icon="plus" size="sm">
                            New invoice
                        </x-ui.button>
                    @endif
                </div>

                <div class="overflow-x-auto rounded border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">ID</th>
                                <th class="px-3 py-2">Invoice no</th>
                                <th class="px-3 py-2">Due date</th>
                                <th class="px-3 py-2">Received</th>
                                <th class="px-3 py-2">Service</th>
                                <th class="px-3 py-2">Office</th>
                                <th class="px-3 py-2 text-right">Total</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($invoicesData as $invoice)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $invoice['id'] }}</td>
                                    <td class="px-3 py-2 font-medium text-slate-900">{{ $invoice['invoice_no'] }}</td>
                                    <td class="px-3 py-2 text-slate-700 text-xs whitespace-nowrap">{{ $invoice['due_date'] }}</td>
                                    <td class="px-3 py-2 text-slate-700 text-xs whitespace-nowrap">{{ $invoice['received_date'] }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $invoice['package_name'] }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $invoice['office_name'] }}</td>
                                    <td class="px-3 py-2 text-right font-mono text-slate-700">{{ $invoice['total_amount'] }}</td>
                                    <td class="px-3 py-2">
                                        @php
                                            $st = strtolower($invoice['status'] ?? '');
                                            $stClass = $st === 'paid' ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700';
                                        @endphp
                                        <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium {{ $stClass }}">{{ ucfirst($invoice['status']) }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('invoices.show', ['invoice' => $invoice['id']]) }}"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700" title="View">
                                                <x-ui.icon name="eye" size="sm" />
                                            </a>
                                            <a href="{{ route('invoices.edit', ['invoice' => $invoice['id']]) }}"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700" title="Edit">
                                                <x-ui.icon name="edit" size="sm" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="px-3 py-8 text-center text-sm text-slate-500">No invoices found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Billing Tab --}}
            <div role="tabpanel" class="tab-pane" id="billing-tab">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="banknote" size="sm" class="text-slate-400" />
                        <h3 class="text-sm font-semibold text-slate-700">Billing</h3>
                    </div>
                    @if(Auth::user()->can('accounting.create'))
                        <x-ui.button as="a" href="{{ route('accounting.create', ['tour_id' => $tour->id]) }}" icon="plus" size="sm">
                            New billing
                        </x-ui.button>
                    @endif
                </div>

                <div class="overflow-x-auto rounded border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">ID</th>
                                <th class="px-3 py-2">Date</th>
                                <th class="px-3 py-2">Office</th>
                                <th class="px-3 py-2 text-right">Total amount</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($billingData as $billing)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $billing['id'] }}</td>
                                    <td class="px-3 py-2 text-slate-700 text-xs whitespace-nowrap">{{ \Carbon\Carbon::parse($billing['date'] ?? now())->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $billing['office_name'] }}</td>
                                    <td class="px-3 py-2 text-right font-mono text-slate-700">{{ $billing['total_amount'] }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('accounting.show', ['accounting' => $billing['id']]) }}"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700" title="View">
                                                <x-ui.icon name="eye" size="sm" />
                                            </a>
                                            <a href="{{ route('accounting.edit', ['accounting' => $billing['id']]) }}"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700" title="Edit">
                                                <x-ui.icon name="edit" size="sm" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-8 text-center text-sm text-slate-500">No billing records found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
                <div id="service-loader" class="text-center py-4" style="display:none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">Loading...</p>
</div>

                <div class="row g-3 mb-3">

<div class="col-12">
    <label class="form-label">{!! trans('main.Servicetype') !!}</label>

    <div id="service-type-filters" class="d-flex gap-3 flex-wrap">

        <label class="form-check">
            <input type="radio" name="service_type" value="all" class="form-check-input" checked>
            <span class="form-check-label">All</span>
        </label>

        <label class="form-check">
            <input type="radio" name="service_type" value="event" class="form-check-input">
            <span class="form-check-label">Event</span>
        </label>

        <label class="form-check">
            <input type="radio" name="service_type" value="guide" class="form-check-input">
            <span class="form-check-label">Guide</span>
        </label>

        <label class="form-check">
            <input type="radio" name="service_type" value="hotel" class="form-check-input">
            <span class="form-check-label">Hotel</span>
        </label>

        <label class="form-check">
            <input type="radio" name="service_type" value="restaurant" class="form-check-input">
            <span class="form-check-label">Restaurant</span>
        </label>

        <label class="form-check">
            <input type="radio" name="service_type" value="transfer" class="form-check-input">
            <span class="form-check-label">Transfer</span>
        </label>

    </div>
</div>

                    <div class="col-12">
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
                            @forelse($serviceCatalog ?? [] as $service)
                                @php
                                    $type = $service['type'] ?? '';
                                    $id = $service['id'] ?? '';
                                    $name = $service['name'] ?? '';
                                    $city = $service['city'] ?? '';
                                    $country = $service['country'] ?? '';
                                    $preLoader = ($type == 'hotel' || $type == 'transfer') ? '' : 'pre-loader-func';
                                @endphp
                                <tr data-service-type="{{ $type }}" 
                                    data-service-name="{{ strtolower($name) }}" 
                                    data-service-city="{{ strtolower($city) }}" 
                                    data-service-country="{{ strtolower($country) }}">
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $name }}</span>
                                            @if(isset($service['type_label']) && $service['type_label'])
                                                <small class="text-muted text-uppercase">{{ $service['type_label'] }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $service['address'] ?? '' }}</td>
                                    <td>{{ $country }}</td>
                                    <td>{{ $city }}</td>
                                    <td>{{ $service['phone'] ?? '' }}</td>
                                    <td>{{ $service['contact'] ?? '' }}</td>
                                    <td>
                                        <button class="btn btn-success btn-sm add-service-button {{ $preLoader }}" 
                                                data-link="{{ route('tour_package.store') }}" 
                                                data-service_type="{{ $type }}" 
                                                data-service_id="{{ $id }}" 
                                                data-service_name="{{ htmlspecialchars($name, ENT_QUOTES, 'UTF-8') }}">
                                            <i class="ti ti-plus me-1"></i>{!! trans('main.Add') !!}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="ti ti-inbox fs-1"></i>
                                        <p class="mt-2">{!! __('No services available') !!}</p>
                                        @if(config('app.debug'))
                                            <small class="text-muted">Debug: serviceCatalog is empty or not loaded</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
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

<div class="modal modal-blur fade " id="service-modaltest" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Description</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                
            
                                <form action="{{ route('description_package') }}" method="Post" id="description-service">
						  {{csrf_field()}}
                        <div class="form-group">
                            <label for="description">{!! trans('main.Text') !!}</label>
							<h2>Select Time</h2>
							<label for="appt">Select a time:</label>
                            <input type="time" id="time" name="time" class="form-control">
							<div class="form-group">
                                <span class="input-group-addon"> {!! trans('main.Template') !!}</span>
                        	<div class="input-group">
							<select id="desc_template_selector" name="desc_template_selector" class="form-control">
                            </select>
								</div>
							</div>
                            {{-- <input type="text" name="description" class="form-control" required> --}}
                            <textarea name="description" id="description"  class="form-control" style="width: 100%; resize: vertical;"></textarea>
                        </div>
                        <input type="number" hidden="hidden" id="tour_day_id" name="tourDayId">
                        <button type="submit" class="btn btn-primary pre-loader-func">{!! trans('main.Create') !!}</button>
                    </form>
                
                
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

    // Client-side filtering for service catalog (server-side rendered data)
    // Root fix: Load data directly from controller, filter on client side
    // function filterServices() {
    //     const serviceType = $('#service-type-filter').val();
    //     const searchValue = ($('#service-catalog-search').val() || '').toLowerCase().trim();
    //     const rows = $('#service-catalog-table tbody tr').not('#no-services-message');
    //     let visibleCount = 0;
        
    //     rows.each(function() {
    //         const $row = $(this);
            
    //         // Root fix: Ensure all values are strings before calling includes()
    //         const rowServiceType = String($row.data('service-type') || '').toLowerCase();
    //         const rowServiceName = String($row.data('service-name') || '').toLowerCase();
    //         const rowServiceCity = String($row.data('service-city') || '').toLowerCase();
    //         const rowServiceCountry = String($row.data('service-country') || '').toLowerCase();
            
    //         // Filter by service type
    //         let typeMatch = true;
    //         if (serviceType && serviceType !== 'all') {
    //             const filterType = serviceType.toLowerCase();
    //             // Handle special case: 'transfer' in data might be 'transfer' or 'bus company'
    //             typeMatch = rowServiceType === filterType || 
    //                        (filterType === 'transfer' && rowServiceType === 'transfer');
    //         }
            
    //         // Filter by search term (name, city, or country)
    //         let searchMatch = true;
    //         if (searchValue) {
    //             searchMatch = rowServiceName.includes(searchValue) ||
    //                          rowServiceCity.includes(searchValue) ||
    //                          rowServiceCountry.includes(searchValue);
    //         }
            
    //         // Show or hide row
    //         if (typeMatch && searchMatch) {
    //             $row.show();
    //             visibleCount++;
    //         } else {
    //             $row.hide();
    //         }
    //     });
        
    //     // Show/hide "no results" message
    //     $('#no-services-message').remove();
    //     if (visibleCount === 0 && rows.length > 0) {
    //         $('#service-catalog-table tbody').append(
    //             '<tr id="no-services-message"><td colspan="7" class="text-center text-muted py-4"><i class="ti ti-inbox fs-1"></i><p class="mt-2">{!! __('No services match your search criteria') !!}</p></td></tr>'
    //         );
    //     }
    // }
    
    // // Initialize filters when modal is shown
    // $('#service-modal').on('shown.bs.modal shown.modal', function() {
    //     // Show all rows initially
    //     $('#service-catalog-table tbody tr').not('#no-services-message').show();
        
    //     // Reset filters
    //     $('#service-type-filter').val('all');
    //     $('#service-catalog-search').val('');
        
    //     // Initial filter (shows all)
    //     filterServices();
        
    //     // Connect service type filter
    //     $('#service-type-filter').off('change.serviceFilter').on('change.serviceFilter', function() {
    //         filterServices();
    //     });
        
    //     // Connect search input - live search (no debounce)
    //     $('#service-catalog-search').off('input.serviceFilter keyup.serviceFilter paste.serviceFilter').on('input.serviceFilter keyup.serviceFilter paste.serviceFilter', function() {
    //         filterServices();
    //     });
        
    //     // Ensure Add buttons are visible and clickable
    //     $('.add-service-button').css({
    //         'display': 'inline-block',
    //         'visibility': 'visible',
    //         'opacity': '1'
    //     }).show();
    // });
    
    // // Reset filters when modal is hidden
    // $('#service-modal').on('hidden.bs.modal hidden.modal', function() {
    //     $('#service-type-filter').val('all');
    //     $('#service-catalog-search').val('');
    //     // Show all rows when modal closes
    //     $('#service-catalog-table tbody tr').not('#no-services-message').show();
    //     $('#no-services-message').remove();
    // });

//     function filterServices() {
//     const serviceType = ($('#service-type-filter').val() || '').toLowerCase();
//     const searchValue = ($('#service-catalog-search').val() || '').toLowerCase().trim();
//     const rows = $('#service-catalog-table tbody tr').not('#no-services-message');
//     let visibleCount = 0;

//     rows.each(function () {
//         const $row = $(this);

//         const rowServiceType = String($row.data('service-type') || '').toLowerCase();
//         const rowServiceName = String($row.data('service-name') || '').toLowerCase();
//         const rowServiceCity = String($row.data('service-city') || '').toLowerCase();
//         const rowServiceCountry = String($row.data('service-country') || '').toLowerCase();

//         // --- Filter by type ---
//         let typeMatch = true;

//         if (serviceType !== '' && serviceType !== 'all') {
//             const filterType = serviceType.toLowerCase();

//             typeMatch =
//                 rowServiceType === filterType ||
//                 (filterType === 'transfer' && rowServiceType === 'transfer');
//         }

//         // --- Filter by search ---
//         let searchMatch = true;
//         if (searchValue !== '') {
//             searchMatch =
//                 rowServiceName.includes(searchValue) ||
//                 rowServiceCity.includes(searchValue) ||
//                 rowServiceCountry.includes(searchValue);
//         }

//         // --- Show/hide row ---
//         if (typeMatch && searchMatch) {
//             $row.show();
//             visibleCount++;
//         } else {
//             $row.hide();
//         }
//     });

//     // --- No results message ---
//     $('#no-services-message').remove();

//     if (visibleCount === 0 && rows.length > 0) {
//         $('#service-catalog-table tbody').append(`
//             <tr id="no-services-message">
//                 <td colspan="7" class="text-center text-muted py-4">
//                     <i class="ti ti-inbox fs-1"></i>
//                     <p class="mt-2">{!! __('No services match your search criteria') !!}</p>
//                 </td>
//             </tr>
//         `);
//     }
// }

// // Initialize when modal opens
// $('#service-modal').on('shown.bs.modal', function () {

//     // Reset fields
//     $('#service-type-filter').val('all');
//     $('#service-catalog-search').val('');
//     $('#no-services-message').remove();

//     // Ensure all rows visible
//     $('#service-catalog-table tbody tr').show();

//     // Run initial filter
//     filterServices();

//     // Prevent duplicate event bindings
//     $('#service-type-filter').off('change.serviceFilter');
//     $('#service-catalog-search').off('input.serviceFilter');

//     // Bind events
//     $('#service-type-filter').on('change.serviceFilter', filterServices);
//     $('#service-catalog-search').on('input.serviceFilter', filterServices);

//     // Ensure buttons visible
//     $('.add-service-button').css({
//         display: 'inline-block',
//         visibility: 'visible',
//         opacity: 1
//     });
// });

// // Reset on close
// $('#service-modal').on('hidden.bs.modal', function () {
//     $('#service-type-filter').val('all');
//     $('#service-catalog-search').val('');
//     $('#service-catalog-table tbody tr').show();
//     $('#no-services-message').remove();
// });




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
        
        let overlay = '<div class="overlay"><div class="spinner-border spinner-border-sm text-primary" role="status" aria-label="Loading"></div></div>';
        let container = selectedGuestList.closest('.card-body');
        container.append(overlay);
        selectedGuestList.hide();
        
        // Root fix: Bootstrap Modal compatibility - handle both Bootstrap 4 and 5
        var questionModalEl = document.getElementById('question_modal');
        var questionModal = null;
        if (questionModalEl && typeof bootstrap !== 'undefined' && bootstrap && bootstrap.Modal) {
            try {
                // Try Bootstrap 5.1+ getOrCreateInstance
                if (bootstrap.Modal.getOrCreateInstance && typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                    questionModal = bootstrap.Modal.getOrCreateInstance(questionModalEl);
                }
                // Try Bootstrap 5.0 getInstance
                else if (bootstrap.Modal.getInstance && typeof bootstrap.Modal.getInstance === 'function') {
                    questionModal = bootstrap.Modal.getInstance(questionModalEl);
                    if (!questionModal && typeof bootstrap.Modal === 'function') {
                        questionModal = new bootstrap.Modal(questionModalEl);
                    }
                }
                // Try direct constructor
                else if (typeof bootstrap.Modal === 'function') {
                    questionModal = new bootstrap.Modal(questionModalEl);
                }
            } catch (e) {
                console.warn('Bootstrap Modal API failed, falling back to jQuery:', e);
            }
        }
        
        // Fallback to jQuery/Bootstrap 4
        if (questionModal && typeof questionModal.hide === 'function') {
            questionModal.hide();
        } else if (questionModalEl && typeof $ !== 'undefined') {
            $(questionModalEl).modal('hide');
        }
        
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
// RADIO FILTER
// $(document).on("change", "input[name='service_type']", function () {

//     let selected = $(this).val().toLowerCase();

//     $("#service-catalog-table tbody tr").each(function () {

//         let rowType = String($(this).data("service-type") || "").toLowerCase();

//         if (selected === "all") {
//             $(this).show(); // show all
//         } else {
//             if (rowType === selected) {
//                 $(this).show(); // show matched
//             } else {
//                 $(this).hide(); // hide non-matching
//             }
//         }

//     });

// });
$(document).on('click', '.add-service-button', function () {
    location.reload();
});


// RADIO FILTER WITH LOADER
$(document).on("change", "input[name='service_type']", function () {

    let selected = $(this).val().toLowerCase();

    // Show loader
    $("#service-loader").show();
        $('.add-service-button').css({
             'display': 'inline-block',
             'visibility': 'visible',
             'opacity': '1'
         }).show();

    // Hide table during loading
    $("#service-catalog-table").css("opacity", 0.3);

    setTimeout(() => {

        $("#service-catalog-table tbody tr").each(function () {

            let rowType = String($(this).data("service-type") || "").toLowerCase();

            if (selected === "all") {
                $(this).show();
            } else {
                $(this).toggle(rowType === selected);
            }
        });

        // Hide loader and restore table
        $("#service-loader").hide();
        $("#service-catalog-table").css("opacity", 1);

    }, 200); // simulate processing for smooth UX
});

$(document).on("click", ".add-description-package", function (e) {
    e.preventDefault(); 
    var ab = $(this).attr('dayid');
    console.log('ab',ab);
    $('#tour_day_id').val(ab);
    $("#service-modaltest").modal("show");
$('#desc_template_selector').removeClass('select2-hidden-accessible');
$('.selection').css('display','none');
    let html = "<option value='0' selected>Default</option>";

    $.ajax({
        url: '/templates/api/loadServiceTemplates',
        method: 'GET',
        data: { id: 6 },

        success: function(res) {
            for (let i = 0; i < res.templates.length; i++) {

                let t = res.templates[i];

                if (t.name !== 'Header' && t.name !== 'Footer') {

                    // Add option with content inside data-detail
                    html += "<option value='" + t.id + "' data-detail='" +
                            encodeURIComponent(t.content) + "'>" +
                            t.name + "</option>";
                }
            }

            // Replace old options
            $('#desc_template_selector').html(html);

            // On change load content in CKEditor
            $('#desc_template_selector').on('change', function () {

                let val = $(this).val();

                if (val === '0') {

                    CKEDITOR.instances['description'].setData("");

                } else {

                    let detail = $(this).find("option:selected").data("detail");

                    // Decode & apply to CKEditor
                    CKEDITOR.instances['description'].setData(decodeURIComponent(detail));
                }
            });
        }
    });
});


// $(document).on("click", ".add-description-package", function () {
//     $("#service-modaltest").modal("show");
//      $('#desc_template_selector').removeClass('.select2-hidden-accessible')
//     		var selected = '';
// 		 html = "<option value='0' selected>Default</option>";
// 		$.ajax({
//             url: '/templates/api/loadServiceTemplates',
//             method: 'GET',

//             data: {
//                 id: 6,
//             },
//             success: function(res) {
// console.log('abcsdgffc',res);
//                 for (var i = 0; i < res.templates.length; i++) {

//                     (i == 0) ? selected = "selected": "";

//                     if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
						
//                         html += "<option value='" + res.templates[i]['id'] + "' >" + res
//                             .templates[i]['name'] + "</option>";
//                     }
//                 }

//                 // $('#service-description').find('#desc_template_selector').html(html);
//  $('#desc_template_selector').append(html);
//                 $('#service-description').find('#desc_template_selector').on('change', function() {
// 					if($(this).val() === '0'){
// 						CKEDITOR.instances['description'].setData("");
// 					}else{
//                     guestTemplate($(this).val());
// 					}
//                 });

//             }

//         });
// });



$(document).on("click", "#service-modaltest .btn-close", function () {
    $("#service-modaltest").modal("hide");
    
});

	function loadDescriptionemplate(){
		var selected = '';
		 html = "<option value='0' selected>Default</option>";
		// $.ajax({
        //     url: '/templates/api/loadServiceTemplates',
        //     method: 'GET',

        //     data: {
        //         id: 6,
        //     },
        //     success: function(res) {

        //         for (var i = 0; i < res.templates.length; i++) {

        //             (i == 0) ? selected = "selected": "";

        //             if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
						
        //                 html += "<option value='" + res.templates[i]['id'] + "' >" + res
        //                     .templates[i]['name'] + "</option>";
        //             }
        //         }

        //         $('#service-description').find('#desc_template_selector').html(html);

        //         $('#service-description').find('#desc_template_selector').on('change', function() {
		// 			if($(this).val() === '0'){
		// 				CKEDITOR.instances['description'].setData("");
		// 			}else{
        //             guestTemplate($(this).val());
		// 			}
        //         });

        //     }

        // });
		
		 
	}

    $("tr").on("click", function(e) {
    e.preventDefault();
});





</script>


@endsection