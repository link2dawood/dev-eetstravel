@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit')
@include('component.datatables_cdn')
@section('content')

{{-- Page header --}}
<x-ui.page-header
    title="Tour Package"
    description="Edit tour package"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tours', 'href' => route('tour.index')],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ \App\Helper\AdminHelper::getBackButton(route('tour.show', ['tour' => $tourPackage->getTour()->id])) }}" variant="ghost" icon="arrow-left">{{ trans('main.Cancel') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="space-y-4">

    {{-- Validation + flash banners --}}
    @if (isset($errors) && $errors->any())
        <div class="rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
            <div class="flex items-center gap-2 font-medium mb-1"><x-ui.icon name="alert-octagon" class="text-danger-600" />Please correct the following:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- block-error-driver hook preserved for JS that toggles its visibility --}}
    <div class="block-error-driver hidden rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700 text-center"></div>

    @if(session('message_buses'))
        <div class="rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700 text-center">{{ session('message_buses') }}</div>
    @endif
    @if(session('retirement_package'))
        <div class="rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">{{ session('retirement_package') }}</div>
    @endif
    @if(session('transfer_add_date'))
        <div class="rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700 text-center">{{ session('transfer_add_date') }}</div>
    @endif

    {{-- Server-rendered tour-package-services container — populated by tour.js --}}
    <div class="tour-package-services"></div>

    <form method='POST' action='{!! url("tour_package") !!}/{!! $tourPackage->id !!}/update' id="tour_package_add_form" class="space-y-4">
        <input type="hidden" name="tour_package_id" value="{{ $tourPackage->id }}">
        <input type='hidden' name='_token' value='{{ Session::token() }}'>

        {{-- Main info card --}}
        <div class="rounded border border-slate-200 bg-white shadow-subtle">

            {{-- Card header — service name + change-service trigger --}}
            <div class="border-b border-slate-200 px-5 py-3 flex flex-wrap items-start justify-between gap-3">
                <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <x-ui.icon name="package" class="text-primary-600" />
                    <span>{{ trans('main.Service') }}:</span>
                    <a href="{{ $tourPackage->service_link }}" target="_blank" class="text-primary-600 hover:text-primary-700">
                        <span id="service_name" class="capitalize">{!! isset($serviceName) ? str_replace('transfer', 'Bus Company', $serviceName) : $tourPackage->name !!}</span>
                    </a>
                </h3>
                <a href="#" class="open-modal-change-service inline-flex items-center gap-1.5 rounded bg-primary-600 px-3 h-9 text-sm font-medium text-white hover:bg-primary-700"
                   id="change_service_with_edit"
                   style="display: {{ $tourPackage->getStatusName() !== 'Confirmed' ? 'inline-flex' : 'none' }}"
                   data-toggle="modal"
                   data-target="#list-tour-packages"
                   data-serviceTypeId="{{ $tourPackage->type }}"
                   data-serviceId="{{ $tourPackage->service()->id ?? '' }}"
                   data-packageId="{{ $tourPackage->id }}"
                   @if ($tourPackage->tourDays->isNotEmpty())
                       data-tour-day-id="{{ $tourPackage->tourDays[0]->id }}"
                   @endif
                   data-time-old-service="{{ !$tourPackage->hasParrent() ? $tourPackage->time_from : null }}"
                   data-link="{{ route('tour_package.store') }}"
                   data-departure_date='{{ $tourPackage->getTour()->departure_date }}'
                   data-retirement_date="{{ $tourPackage->getTour()->retirement_date }}"
                   data-tour_id='{{ $tourPackage->getTour()->id }}'
                   data-info="change_edit_service">
                    <x-ui.icon name="refresh-cw" size="sm" />{{ trans('main.ChangeService') }}
                </a>
            </div>

            @if(session('driver_busy'))
                <div class="mx-5 mt-4 rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700 text-center">
                    {{ session('driver_busy') }}
                </div>
            @endif

            {{-- Card body — all editable fields --}}
            <div class="px-5 py-5 space-y-4">

                {{-- Name + description --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Name</label>
                        {!! Form::text('name', $tourPackage->name, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'name']) !!}
                    </div>
                    <div>
                        <label for="description" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Description</label>
                        {!! Form::text('description', $tourPackage->description, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'description']) !!}
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Status') }}</label>
                    <select name="status" id="status" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}"
                                {{
                                    (isset($errors) && $errors->any())
                                        ? (old('status') == $status->id ? 'selected' : '')
                                        : ($tourPackage->status == $status->id ? 'selected' : '')
                                }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Driver + Bus (transfer packages only) --}}
                @if($drivers != null && $buses != null)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="driver_transfer_edit" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Drivers</label>
                            @if(count($drivers) > 0)
                                <select name="driver_transfer[]" id="driver_transfer_edit" class="select2 js-state form-control block w-full rounded border border-slate-300 bg-white text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" multiple="multiple">
                                    @foreach($drivers as $driver)
                                        <?php $check = false; ?>
                                        @foreach($selected_drivers as $item)
                                            @if($item->driver_id == $driver->id)
                                                <?php $check = true; ?>
                                            @endif
                                        @endforeach
                                        <option {{ $check ? 'selected' : '' }} class="transfer_driver" value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                <script>
                                    if ($('#driver_transfer_edit').length > 0) {
                                        $('#driver_transfer_edit').select2();
                                    }
                                </script>
                            @else
                                <p class="text-sm text-slate-500">{{ trans('main.Transferwithoutdriver') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="bus_transfer_edit" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Bus</label>
                            @if(count($buses) > 0)
                                <select name="bus_transfer" id="bus_transfer_edit" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                    @foreach($buses as $bus)
                                        <option {{ $selected_bus ? ($selected_bus->bus_id == $bus->id ? 'selected' : '') : '' }} class="transfer_bus" value="{{ $bus->id }}">{{ $bus->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <p class="text-sm text-slate-500">{{ trans('main.Transferwithoutbus') }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Pax + pricing --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pax</label>
                        {!! Form::text('pax', $tourPackage->pax, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'pax']) !!}
                    </div>
                    <div>
                        <label for="pax_free" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pax Free</label>
                        {!! Form::text('pax_free', $tourPackage->pax_free, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'pax_free']) !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="total_amount" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Price per Person</label>
                        {!! Form::text('total_amount', round($tourPackage->total_amount, 2), [
                            'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id'    => 'total_amount',
                        ]) !!}
                    </div>
                    <div>
                        <label for="total_amount_manually" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Total Price</label>
                        {!! Form::text('total_amount_manually', $tourPackage->total_amount_manually, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'total_amount_manually']) !!}
                    </div>
                    <div>
                        <label for="total_amount_auto" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Total Price Auto</label>
                        {!! Form::text('total_amount_auto', $tourPackage->getTotalAmountAuto(), ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-slate-50 px-3 text-sm text-slate-500 shadow-subtle cursor-not-allowed', 'id' => 'total_amount_auto', 'disabled']) !!}
                    </div>
                </div>

                <div>
                    <label for="currency" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Currency</label>
                    <select name="currency" id="currency" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if(session('date-error'))
                    <div class="rounded border border-danger-600/20 bg-danger-50 px-4 py-2 text-sm text-danger-700">{{ session('date-error') }}</div>
                @endif

                {{-- Date / time from-to. The .input-group + .datepickerTourPackageDate
                     + .timepicker class hooks drive the legacy picker JS. --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="from_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.DateFrom') }}</label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="calendar" size="sm" />
                            </span>
                            {!! Form::text('from_date', $tourPackage->from_date, ['class' => 'form-control datepickerTourPackageDate block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'from_date', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                    <div>
                        <label for="from_time" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.TimeFrom') }}</label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="clock" size="sm" />
                            </span>
                            {!! Form::text('from_time', $tourPackage->from_time, ['class' => 'form-control timepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'from_time', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                    <div>
                        <label for="to_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.DateTo') }}</label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="calendar" size="sm" />
                            </span>
                            {!! Form::text('to_date', $tourPackage->to_date, ['class' => 'form-control datepickerTourPackageDate block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'to_date', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                    <div>
                        <label for="to_time" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.TimeTo') }}</label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="clock" size="sm" />
                            </span>
                            {!! Form::text('to_time', $tourPackage->to_time, ['class' => 'form-control timepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'to_time', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                </div>

                {{-- Pickup / drop destinations (transfer + guide only) --}}
                @if((isset($packageServiceType) && $packageServiceType == 'transfer') || (isset($packageServiceType) && $packageServiceType == 'guide'))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="pickup_des" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pickup Description</label>
                            {!! Form::text('pickup_des', $tourPackage->pickup_des, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'pickup_des']) !!}
                        </div>
                        <div>
                            <label for="drop_des" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Drop Destination</label>
                            {!! Form::text('drop_des', $tourPackage->drop_des, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'drop_des']) !!}
                        </div>
                    </div>
                @endif

                {{-- Hotel-only: city tax + room types --}}
                @if($tourPackage->type == 0)
                    <div>
                        <label for="city_tax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">City Tax</label>
                        {!! Form::text('city_tax', $tourPackage->city_tax, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'city_tax']) !!}
                    </div>
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                            {{ trans('main.RoomTypes') }}
                            (<a href="{{ route('hotel.show', ['hotel' => $tourPackage->reference, 'tab' => 'season_tab']) }}" class="text-primary-600 hover:text-primary-700">{{ trans('main.HotelSeasons') }}</a>)
                        </label>

                        <div id="list_selected_room_types" class="space-y-2">
                            @php $peopleCount = 0; @endphp
                            @foreach($selected_room_types as $item)
                                @php
                                    $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$item->code])
                                        ? App\TourPackage::$roomsPeopleCount[$item->code] * $item->count_room : 0;
                                @endphp
                                @include('component.item_hotel_room_type', ['room_type' => $item])
                            @endforeach
                            @if($peopleCount != $tourPackage->pax + $tourPackage->pax_free)
                                <div class="rounded border border-warning-600/20 bg-warning-50 px-4 py-2 text-sm text-warning-700 flex items-start gap-2">
                                    <x-ui.icon name="alert-triangle" class="text-warning-600 mt-0.5 shrink-0" />
                                    <div class="flex-1">{{ trans('main.PaxCount') }} ({{ $tourPackage->pax + $tourPackage->pax_free }}) {{ trans('main.isnotequaltothenumberof') }} ({{ $peopleCount }})</div>
                                    <button type="button" class="close text-slate-400 hover:text-slate-700" data-dismiss="alert" aria-hidden="true">&times;</button>
                                </div>
                            @endif
                        </div>

                        <button class="btn btn-success btn_for_select_room_type mt-3 inline-flex items-center gap-1.5 rounded bg-success-600 px-3 h-9 text-sm font-medium text-white hover:bg-success-700" type="button">{{ trans('main.SelectRooms') }}</button>

                        <div class="error_room_types hidden mt-2 rounded border border-danger-600/20 bg-danger-50 px-4 py-2 text-sm text-danger-700"></div>

                        <ul class="list_room_types list-none p-0 m-0 mt-2 hidden border border-slate-200 rounded bg-white divide-y divide-slate-100">
                            @foreach($room_types as $room_type)
                                <li class="select_room_type px-3 py-2 hover:bg-slate-50 cursor-pointer">
                                    <label class="text-sm text-slate-700">{{ $room_type->name }}</label>
                                    <input type="text" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Menu repeater (hotel + restaurant) --}}
                @if($tourPackage->type == 0 || $tourPackage->type == 4)
                    <div class="repeater border border-slate-200 rounded p-4 bg-slate-50/50">
                        <h5 class="text-sm font-semibold text-slate-900 mb-3">Menus</h5>
                        <div data-repeater-list="package_menu" class="space-y-3">
                            {{-- EMPTY ITEM template for "Add" --}}
                            <div data-repeater-item class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                <div class="md:col-span-3 package-menu-item">
                                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Quantity</label>
                                    {!! Form::input('string', 'count', 0, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                                </div>
                                @if(isset($tourPackage->service()->menus))
                                    <div class="md:col-span-3 package-menu-item">
                                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Menu</label>
                                        {!! Form::select('menu', ['' => '-- Choose Menu --'] + $tourPackage->service()->menus->pluck('name', 'id')->toArray(), 0, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                                    </div>
                                @endif
                                <div class="md:col-span-1">
                                    <a href="#" data-repeater-delete class="inline-flex h-9 w-9 items-center justify-center rounded bg-danger-600 text-white hover:bg-danger-700" title="Remove">
                                        <x-ui.icon name="trash-2" size="sm" />
                                    </a>
                                </div>
                            </div>

                            @foreach($tourPackage->menus as $packageMenu)
                                <div data-repeater-item class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                    <div class="md:col-span-3 package-menu-item">
                                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Quantity</label>
                                        {!! Form::input('text', 'count', $packageMenu->count, ['class' => 'form-control new_qty block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                                    </div>
                                    <div class="md:col-span-3 package-menu-item">
                                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Menu</label>
                                        <select class="form-control new_menu select2-hidden-accessible block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" id="menu" name="package_menu[1][menu]" tabindex="-1" aria-hidden="true">
                                            <option value="">-- Choose Menu --</option>
                                            @foreach($tourPackage->service()->menus as $menu)
                                                @if(@$packageMenu->menu->id == $menu->id)
                                                    <option value="{{ $menu->id }}" selected="selected" title="{!! 'Price : '.$menu->price !!}{!! '         Desc : '.strip_tags($menu->description) !!}">{{ $menu->name }}</option>
                                                @else
                                                    <option value="{{ $menu->id }}" title="{!! 'Price : '.$menu->price !!}">{{ $menu->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-1">
                                        <a href="#" data-repeater-delete class="inline-flex h-9 w-9 items-center justify-center rounded bg-danger-600 text-white hover:bg-danger-700" title="Remove">
                                            <x-ui.icon name="trash-2" size="sm" />
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button data-repeater-create class="mt-3 inline-flex items-center gap-1.5 rounded bg-success-600 px-3 h-9 text-sm font-medium text-white hover:bg-success-700" type="button">
                            <x-ui.icon name="plus" size="sm" />Add
                        </button>

                        <div class="error_menu hidden mt-2 rounded border border-danger-600/20 bg-danger-50 px-4 py-2 text-sm text-danger-700"></div>
                    </div>
                @endif

                {{-- Note --}}
                <div>
                    <label for="note" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Note</label>
                    {!! Form::textarea('note', $tourPackage->note, ['class' => 'form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'note', 'rows' => 3]) !!}
                </div>

                {{-- Hidden fields for the JS submit pipeline --}}
                {!! Form::hidden('serviceType', isset($packageServiceType) ? $packageServiceType : '', ['id' => 'tour_package_service_type_value']) !!}
                {!! Form::hidden('serviceId', $tourPackage->reference, ['id' => 'tour_package_service_type_id']) !!}
                @if($tourPackage->tourDays()->first() !== null)
                    {!! Form::hidden('tourDayId', $tourPackage->tourDays()->first()->id, ['id' => 'tour_package_tour_day_id']) !!}
                @endif
            </div>

            {{-- Card footer — Save / Cancel --}}
            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2 rounded-b">
                <x-ui.button as="a" href="{{ \App\Helper\AdminHelper::getBackButton(route('tour.show', ['tour' => $tourPackage->getTour()->id])) }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
                <button class='btn btn-success inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm font-medium text-white hover:bg-primary-700' type='submit' id="send-tour-package-form">
                    <x-ui.icon name="save" size="sm" />{{ trans('main.Save') }}
                </button>
            </div>
        </div>

        {{-- Hidden helpers read by the JS bottom of the file --}}
        <span id="tour_dep" data-departure_date='{{ $tourPackage->getTour()->departure_date }}' class="hidden"></span>
        <span id="tour_package_id" data-id='{{ $tourPackage->id }}' class="hidden"></span>
        <span id="tour_ret" data-retirement_date="{{ $tourPackage->getTour()->retirement_date }}" class="hidden"></span>
    </form>
</div>

{{-- Modals are kept Bootstrap-compatible because the JS uses jQuery's
     $('#confirmed_hotel').modal() to open / close them. Don't strip the
     `modal fade` / `modal-dialog` / `modal-content` / `data-dismiss=modal`
     hooks. --}}

<div class="modal fade" tabindex="-1" id="list-tour-packages" style="padding-left: 17px; padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content" style="overflow: hidden;">
            <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                <h4 class="modal-title text-sm font-semibold text-slate-900">{{ trans('main.Changeservice') }}</h4>
                <button type="button" class="close text-slate-400 hover:text-slate-700" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <table id="search-table-service-list" class="table table-striped table-bordered table-hover w-full text-sm">
                    <thead>
                        <tr>
                            <th>{{ trans('main.Name') }}</th>
                            <th>{{ trans('main.Address') }}</th>
                            <th>{{ trans('main.Country') }}</th>
                            <th>{{ trans('main.City') }}</th>
                            <th>{{ trans('main.Phone') }}</th>
                            <th>{{ trans('main.ContactName') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="confirmed_hotel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_confirmed_hotel">
                <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h4 class="modal-title text-sm font-semibold text-slate-900">{{ trans('main.Confirmedhotel') }}</h4>
                    <button type="button" class="close text-slate-400 hover:text-slate-700" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                </div>
                <div class="modal-body p-4">
                    <div class="confirmed_hotel_block"></div>
                </div>
                <div class="modal-footer border-t border-slate-200 px-5 py-3 flex items-center justify-end gap-2 bg-slate-50">
                    <button type="button" class="btn btn-default btn-send-hotel_cancel inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ trans('main.Cancel') }}</button>
                    <button type="button" class="btn btn-success btn-send-confirmed_hotel_add inline-flex items-center gap-1.5 rounded bg-primary-600 px-3 h-9 text-sm font-medium text-white hover:bg-primary-700">{{ trans('main.Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="select-driver-and-bus_transfer_package">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_transfer_buses_drivers_transfer_package">
                <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h4 class="modal-title text-sm font-semibold text-slate-900">{{ trans('main.Selectdriversandbuses') }}</h4>
                    <button type="button" class="close text-slate-400 hover:text-slate-700" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                </div>
                <div class="p-4">
                    <div class="list-driver-and-buses_transfer_package"></div>
                    <div class="overlay hidden flex items-center justify-center py-4">
                        <x-ui.icon name="loader-2" class="animate-spin text-slate-400" />
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 px-5 py-3 flex items-center justify-end gap-2 bg-slate-50">
                    <button type="button" class="btn btn-success btn-send-transfer_add_transfer_package inline-flex items-center gap-1.5 rounded bg-primary-600 px-3 h-9 text-sm font-medium text-white hover:bg-primary-700">{{ trans('main.Add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ asset('js/tour.js') }}"></script>
@endsection

@section('tour_package_script')
    <script type="text/javascript">
        let tour_dep_date = $('#tour_dep').attr('data-departure_date');
        let tour_ret_date = $('#tour_ret').attr('data-retirement_date');

        $('.datepickerTourPackageDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose : true,
            startDate: tour_dep_date,
            endDate: tour_ret_date
        });

        $(document).on('keydown', '.count_room_type', function(e){
            if (e.keyCode === 13) {
                e.preventDefault();
                $('.count_room_type').blur();
            }
        });

        $(document).on('focus', '.count_room_type', function (e) {
            $(this).select();
        });

        $('.select_room_type').click(function(){
            var data = $(this).find('input');
            var list_selected_rooms = $('.count_room_type');

            for(var i = 0; i < list_selected_rooms.length; i++){
                var item = list_selected_rooms[i];

                if($(item).attr('data-info') === data.attr('data-info')){
                    return false;
                }
            }

            $.ajax({
                method: 'POST',
                url: '/hotel_room_types',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    room_type: data.val()
                }
            }).done((res) => {
                $('#list_selected_room_types').append(res);
                $('.list_room_types').slideUp(200);
            })
        });

        $('.btn_for_select_room_type').click(function(){
            if($('.list_room_types').css('display') === 'none'){
                $('.list_room_types').slideDown(200);
            }else{
                $('.list_room_types').slideUp(200);
            }
        });

        $(document).on('click', '.icon_delete_room_type', function(){
            $(this).closest('.item_selected_room_type').remove();
        });
    </script>

    <script>
        var list_drivers = $('#driver-list-edit').find('option');
        var tour_package_id = $('#tour_package_id').attr('data-id');
        var driver_id = null;

        if(list_drivers.length !== 0){
            for (var i = 0; i < list_drivers.length; i++){
                if($(list_drivers[i]).is(':selected')){
                    driver_id = $(list_drivers[i]).val();
                }
            }
            getDriverBuses();
        }


        $('body').on('change', '#driver-list-edit', function () {
            driver_id = $(this).val();
            getDriverBuses();
        });



        function getDriverBuses() {
            $.ajax({
                method: 'GET',
                url: `/bus_driver_edit/api/${driver_id}`,
                data: {
                    tourPackageId : tour_package_id
                }
            }).done((res) => {
                $('.list-buses-edit').html(res);
            });
        }
    </script>

    <script type="text/javascript">

        function validateRoomTypes() {
            let list_count_rooms = $('.block-qty-room input');
            let list_price_rooms = $('.block-price-room input');
            $('.error_room_types').css({'display': 'none'});

            for(let i = 0; i < list_count_rooms.length; i++){
                // count validate
                if($(list_count_rooms[i]).val() === ''){
                    $('.error_room_types').css({'display': 'block'});
                    $('.error_room_types').html('Fill in the quantity fields please');
                    return false;
                }

                // price validate
                if($(list_price_rooms[i]).val() === ''){
                    $('.error_room_types').css({'display': 'block'});
                    $('.error_room_types').html('Fill in the price fields please');
                    return false;
                }
            }

            return true;
        }

        function validateMenu() {
            let list_menu = $('.new_qty');
            let select = $('.new_menu');

            $('.error_menu').css({'display': 'none'});

            if(list_menu.length === 0){
                return true;
            }


            for(let i = 0; i < list_menu.length; i++){
                if($(list_menu[i]).val() === ''){
                    $('.error_menu').css({'display': 'block'});
                    $('.error_menu').html('Fill in the quantity fields please');
                    return false;
                }
            }

            for (let j = 0; j < select.length; j++){
                if($(select[j]).select2('data')[0].element.value === ''){
                    $('.error_menu').css({'display': 'block'});
                    $('.error_menu').html('Select one of the list of items');
                    return false;
                }
            }

            return true;
        }


        $('#send-tour-package-form').click(function (e) {
            e.preventDefault();
            let oldForm = document.forms.tour_package_add_form;
            let form = new FormData(oldForm);
            var selected_status_id = form.get('status');
            var tour_package_id = form.get('tour_package_id');
            var serviceType = form.get('serviceType');


            if(serviceType === 'hotel'){
                if(!validateRoomTypes()){
                    return false;
                }
            }

            if(serviceType === 'hotel' || serviceType === 'restaurant'){
                if(!validateMenu()){
                    return false;
                }
            }

            $.ajax({
                method: 'GET',
                url: `/api/get_status/${selected_status_id}`,
                data: {}
            }).done((res) => {
                if(res){
                    $('#confirmed_hotel').modal();

                    $.ajax({
                        url: `/get_packages_for_delete/${tour_package_id}`,
                    }).done( (res) => {
                        $('.confirmed_hotel_block').html(res);

                        $('.btn-send-confirmed_hotel_add').click(function (e) {
                            tourServiceChanger.deleteTourPackagesHotel(tour_package_id);
                            // tourServiceChanger.updateStatusTourPackage($(that), _this, statusName, packageType, package_id, packageId);
                            $('#confirmed_hotel').modal('hide');
                            $('#tour_package_add_form').submit();
                        });

                        $('.btn-send-hotel_cancel').click(function (e) {
                            $('#confirmed_hotel').modal('hide');
                            return false;
                        });

                    });
                }else{
                    $('#tour_package_add_form').submit();
                }

            });
        });
    </script>

    <script>
        var $repeater = $('.repeater').repeater( {
            // (Required)
            // Specify the jQuery selector for this nested repeater
            selector: '.package-menu-item',
            show: function () {
                $(this).slideDown();
                $(this).find('.select2').remove();
                $(this).find('select').addClass('new_menu');
                $(this).find('input').addClass('new_qty');
//                $(this).find('select').data('select2').destroy();
                $(this).find('select').select2();
            },
            hide: function (deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            },
        });
    </script>
@endsection
