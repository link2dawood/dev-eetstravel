{{--
    TMS-Client tour-packages fragment. Loaded via AJAX into
    `.tour-packages` on the tour show page. Standalone — no @extends.

    This is a deep-binding template: ~30 distinct JS hooks read its
    data-* attributes, classes, IDs, and `onclick` handlers. Migration
    rewrites the chrome (AdminLTE box / Bootstrap 3 table / btn-xs)
    to Tailwind while preserving every hook intact:

    Selectors preserved
    -------------------
      .add-service-quick[data-link,data-tour_id,data-tour_transfer,
        data-service,data-departure_date,data-retirement_date,
        data-tourDayId,data-date]
      .add-description-package
      .tour-package-item[data-package_id,data-type,data-is_main]
      .package-data[data-tour_day_id,data-package_id,data-package-type,
        data-is_main]
      .tour_package_status[data-info-package-id,data-info-status,
        data-info-status_id,data-info-package-type]
      .timepicker.service-time[data-package_id,data-type,data-is_main]
      .service-description, .package-service-table
      .export_selected, .export_selected_vch
      .main-hotel, .show-button, .delete[data-link],
      .open-modal-change-service[data-serviceTypeId,data-serviceId,
        data-packageId,data-tour-day-id,data-tour_id,data-link,
        data-time-old-service,data-info]
      onclick handlers: loadTemplate(...) and (separately) sendTemplate()
      iCheck plugin bindings on .export_selected[_vch]

    Modals preserved (all keep their existing #id and Bootstrap-modal
    data-attrs so the existing JS that opens/closes them still works):
      #list-tour-packages    — change-service search results
      #select-driver-and-bus + #select-driver-and-bus_transfer_package
      #confirmed_hotel + #error_hotel
      #selectDateForHotelPackage + #selectDateForTransferPackage
      #TemplatesModal        — email template composer
      #myModal               — delete confirm (rendered elsewhere)

    URL endpoints called by JS in this file:
      /update_itnid, /update_voucherid
      /tour_package/{id}/deleteMsg, /tour_package/{id}/edit
      /templates/api/{send,load,loadServiceTemplates}
      /tour_package/{id}/change_time  (commented out but kept inline)
--}}

@php
    $can = fn($p) => Auth::user()->can($p);
@endphp

<div class="space-y-6">

    {{-- ============================================================ --}}
    {{-- Bus Company / Transfers                                       --}}
    {{-- ============================================================ --}}
    <section class="rounded-lg border border-slate-200 bg-white shadow-subtle">
        <header class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
            <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i class="ti ti-bus text-primary-600"></i>{!! trans('main.BusCompany') !!}
            </h3>
            @if($can('tour_package.create'))
                <button class="add-service-quick inline-flex items-center gap-1.5 rounded bg-success-600 px-3 h-9 text-sm font-medium text-white hover:bg-success-700"
                        data-link="{{ route('tour_package.store') }}"
                        data-tour_id='{{ $tour->id }}'
                        data-tour_transfer="1"
                        data-service="transfer"
                        data-departure_date='{{ $tour->departure_date }}'
                        data-retirement_date="{{ $tour->retirement_date }}">
                    <i class="ti ti-plus"></i>{!! trans('main.AddBusCompany') !!}
                </button>
            @endif
        </header>

        <div class="alert alert-info block-error-driver hidden rounded border border-info-200 bg-info-50 m-4 px-3 py-2 text-sm text-info-700 text-center"></div>

        @if($tour->transfers)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-2.5">{!! trans('main.Name') !!}</th>
                            <th class="px-4 py-2.5">{!! trans('main.Status') !!}</th>
                            <th class="px-4 py-2.5">{!! trans('main.Paid') !!}</th>
                            <th class="px-4 py-2.5">{!! trans('main.Address') !!}</th>
                            <th class="px-4 py-2.5">{!! trans('main.DriversPhones') !!}</th>
                            <th class="px-4 py-2.5">{!! trans('main.DateFrom') !!}</th>
                            <th class="px-4 py-2.5">{!! trans('main.Dateto') !!}</th>
                            <th class="px-4 py-2.5 text-right" style="width:150px">{!! trans('main.Actions') !!}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tour->transfers as $package)
                            <tr class="tour-package-item hover:bg-slate-50"
                                data-package_id='{{ $package->id }}'
                                data-type='{{ @$package->service()->service_type }}'
                                data-is_main='@if(!$package->hasParrent()){{ $package->main_hotel }}@else{{ false }}@endif'>
                                <td class="px-4 py-3">
                                    <span class="package-data text-slate-800 font-medium"
                                          data-package_id='{{ $package->id }}'
                                          data-package-type="{{ @$package->service()->service_type }}"
                                          data-is_main='@if(!$package->hasParrent()){{ $package->main_hotel }}@else{{ false }}@endif'>
                                        {{ $package->name }}<br><span class="text-xs text-slate-500">(Bus Company)</span>
                                    </span>
                                </td>

                                {{-- Status --}}
                                @if(!$statusesTransfers->isEmpty())
                                    @php $status = false; $status_name = ''; @endphp
                                    @foreach($statusesTransfers as $item)
                                        @if($item->id == $package->status)
                                            <td class="px-4 py-3 text-slate-700 {{ $can('tour_package.edit') ? 'tour_package_status' : '' }}"
                                                data-info-package-id="{{ $package->parent_id != null ? $package->parent_id : $package->id }}"
                                                data-info-status="{{ $item->name }}"
                                                data-info-status_id="{{ $item->id }}"
                                                data-info-package-type="{{ (@$package->service()->service_type === 'Hotel') ? 'hotel' : '' }}{{ (@$package->service()->service_type === 'Transfer') ? 'transfer' : '' }}">
                                                {{ $item->name }}
                                            </td>
                                            @php $status_name = $item->name; $status = true; @endphp
                                        @endif
                                    @endforeach
                                    @if(!$status)<td class="px-4 py-3"></td>@endif
                                @else
                                    <td class="px-4 py-3 text-slate-700 {{ $can('tour_package.edit') ? 'tour_package_status' : '' }}"
                                        data-info-package-id="{{ $package->parent_id != null ? $package->parent_id : $package->id }}"
                                        data-info-status="{{ $package->status }}"
                                        data-info-status_id="{{ $package->status->id }}"
                                        data-info-package-type="{{ (@$package->service()->service_type === 'Hotel') ? 'hotel' : '' }}{{ (@$package->service()->service_type === 'Transfer') ? 'transfer' : '' }}">
                                        {{ $package->status }}
                                    </td>
                                @endif

                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium {{ $package->paid ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">
                                        {{ $package->paid ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ @$package->service()->address_first }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    @forelse($package->getTransferDrivers() as $driver)
                                        <span class="block text-xs">{{ $driver->phone }}</span>
                                    @empty
                                    @endforelse
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ \Carbon\Carbon::parse($package->time_from)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ \Carbon\Carbon::parse($package->time_to)->format('Y-m-d') }}</td>

                                @if(!$package->hasParrent())
                                    <td class="px-4 py-3 text-right" style="width:150px;">
                                        <div class="flex items-center justify-end gap-1 flex-wrap">
                                            @if($package->type == 0)
                                                <button type="button"
                                                        class="{{ $can('comparison.show') ? 'main-hotel' : 'disabled' }} inline-flex h-7 w-7 items-center justify-center rounded text-xs font-bold text-white {{ $package->main_hotel ? 'bg-success-600' : 'bg-danger-600' }}">M</button>
                                            @endif
                                            @if($can('tour_package.edit'))
                                                <a href="/tour_package/{{ $package->parent_id != null ? $package->parent_id : $package->id }}/edit"
                                                   class="show-button inline-flex h-7 w-7 items-center justify-center rounded bg-primary-600 text-white hover:bg-primary-700"
                                                   data-link="/tour_package/{{ $package->parent_id != null ? $package->parent_id : $package->id }}/edit"
                                                   title="Edit"><i class="ti ti-edit"></i></a>
                                            @endif
                                            @if($can('tour_package.destroy'))
                                                <a data-toggle="modal" data-target="#myModal"
                                                   class="delete inline-flex h-7 w-7 items-center justify-center rounded bg-danger-600 text-white hover:bg-danger-700 cursor-pointer"
                                                   data-link="/tour_package/{{ $package->parent_id != null ? $package->parent_id : $package->id }}/deleteMsg"
                                                   title="Delete"><i class="ti ti-trash"></i></a>
                                            @endif
                                            @if($package->parent_id == null)
                                                @if($can('tour_package.create') && $can('tour_package.destroy'))
                                                    <a href="#"
                                                       class="open-modal-change-service inline-flex h-7 w-7 items-center justify-center rounded bg-warning-500 text-white hover:bg-warning-600"
                                                       style="{{ @$package->getStatusName() !== 'Confirmed' ? '' : 'display:none;' }}"
                                                       data-toggle="modal"
                                                       data-target="#list-tour-packages"
                                                       data-serviceTypeId="{{ $package->type }}"
                                                       data-serviceId="{{ @$package->service()->id }}"
                                                       data-packageId="{{ $package->parent_id != null ? $package->parent_id : $package->id }}"
                                                       data-time-old-service="{{ !$package->hasParrent() ? $package->time_from : null }}"
                                                       data-info="page_change_main"
                                                       data-tour_id="{{ $package->getTour()->id }}"
                                                       data-link="{{ route('tour_package.store') }}"
                                                       title="Change service">
                                                        <i class="ti ti-arrows-exchange"></i>
                                                    </a>
                                                @endif
                                                @if($can('tour_package.edit'))
                                                    <a href="javascript:void(0);"
                                                       data-info="{{ @$package ?: \GuzzleHttp\json_encode(' ') }}"
                                                       onclick="loadTemplate(JSON.parse($(this).attr('data-info')) ? JSON.parse($(this).attr('data-info')).type : '','{!! @$package->service()->work_email !!}','{!! $package->name !!}','{!! $package->pax !!} {!! $package->pax_free !!}','{!!  @$package->service()->address_first !!}', '{!! @$package->service()->work_email !!}','{!! @$package->service()->work_phone !!}','{!! $package->description !!}','{!! $status_name !!}','{!! $package->time_from  !!}','','' );"
                                                       class="inline-flex h-7 w-7 items-center justify-center rounded bg-success-600 text-white hover:bg-success-700"
                                                       title="Send email"><i class="ti ti-mail"></i></a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- ============================================================ --}}
    {{-- Per-day sections                                              --}}
    {{-- ============================================================ --}}
    @php $countDay = 0; @endphp
    @foreach($tourDates as $tourDate)
        @php $countDay++; @endphp
        <section class="rounded-lg border border-slate-200 bg-white shadow-subtle">
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200 px-5 py-3">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <i class="ti ti-calendar text-primary-600"></i>
                        {!! trans('main.Day') !!} {{ $countDay }} — {{ (new \Carbon\Carbon($tourDate->date))->formatLocalized('%B %d, %Y (%A)') }}
                    </h3>
                </div>
                @if($can('tour_package.create'))
                    <div class="flex items-center gap-2">
                        <button class="add-service-quick inline-flex items-center gap-1.5 rounded bg-success-600 px-3 h-9 text-sm font-medium text-white hover:bg-success-700"
                                data-tourDayId="{{ $tourDate->id }}"
                                data-link="{{ route('tour_package.store') }}"
                                data-date="{!! $tourDate->date !!}"
                                data-tour_id='{{ $tour->id }}'
                                data-departure_date='{{ $tour->departure_date }}'
                                data-retirement_date="{{ $tour->retirement_date }}">
                            <i class="ti ti-plus"></i>{!! trans('main.AddService') !!}
                        </button>
                        <button class="add-description-package inline-flex items-center gap-1.5 rounded border border-success-600 bg-white px-3 h-9 text-sm font-medium text-success-700 hover:bg-success-50">
                            <i class="ti ti-note"></i>{!! trans('main.Adddescription') !!}
                        </button>
                    </div>
                @endif
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm {{ $can('tour_package.edit') ? 'package-service-table' : '' }}" style="background:#fff">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-2.5">Itn</th>
                            <th class="px-3 py-2.5">Vch</th>
                            <th class="px-3 py-2.5">{!! trans('main.FromTime') !!}</th>
                            <th class="px-3 py-2.5 w-6"></th>
                            <th class="px-3 py-2.5" style="min-width:100px">{!! trans('main.Name') !!}</th>
                            <th class="px-3 py-2.5" style="min-width:150px">{!! trans('main.Status') !!}</th>
                            <th class="px-3 py-2.5">{!! trans('main.Paid') !!}</th>
                            <th class="px-3 py-2.5">Pax</th>
                            <th class="px-3 py-2.5">Rooms</th>
                            <th class="px-3 py-2.5">{!! trans('main.Address') !!}</th>
                            <th class="px-3 py-2.5">{!! trans('main.Email') !!}</th>
                            <th class="px-3 py-2.5">{!! trans('main.Phone') !!}</th>
                            <th class="px-3 py-2.5">{!! trans('main.Description') !!}</th>
                            <th class="px-3 py-2.5 text-right" style="width:150px; min-width:150px">{!! trans('main.Actions') !!}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" data-default_tour_day_id='{{ $tourDate->id }}'>
                        @foreach($tourDate->packages as $package)
                            @if($package->description_package)
                                {{-- ---- description-only row ---- --}}
                                <tr class="hover:bg-slate-50"
                                    data-package_id='{{ $package->id }}'
                                    data-type='{{ $package->type }}'
                                    data-is_main='@if(!$package->hasParrent()){{ $package->main_hotel }}@else{{ false }}@endif'>
                                    <td class="px-3 py-2 text-center not-click">
                                        <input type="checkbox" value="{{ $package->id }}" class="export_selected" @if($package->itn == 1) checked @endif>
                                    </td>
                                    <td class="px-3 py-2 text-center not-click">
                                        <input type="checkbox" value="{{ $package->id }}" class="export_selected_vch" @if($package->vch == 1) checked @endif>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text"
                                               class="form-control timepicker {{ $can('tour_package.edit') ? 'service-time' : '' }} block w-20 rounded border border-slate-300 bg-transparent px-2 h-8 text-xs"
                                               style="background-color: inherit;"
                                               data-package_id="{{ $package->id }}"
                                               name="time_from"
                                               value="{!! $package->time_from !!}">
                                        <span class="package-data"
                                              data-type='{{ $package->type }}'
                                              data-tour_day_id='{{ $tourDate->id }}'
                                              data-package_id='{{ $package->id }}'
                                              data-package-type="service_description"
                                              data-is_main='@if(!$package->hasParrent()){{ $package->main_hotel }}@else{{ false }}@endif'></span>
                                    </td>
                                    <td class="px-3 py-2 italic text-slate-700 {{ $can('tour_package.edit') ? 'service-description' : '' }}" colspan="10">
                                        {!! $package->description !!}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        @if($can('tour_package.destroy'))
                                            <a data-toggle="modal" data-target="#myModal"
                                               class="delete inline-flex h-7 w-7 items-center justify-center rounded bg-danger-600 text-white hover:bg-danger-700 cursor-pointer"
                                               data-link="/tour_package/{{ $package->id }}/deleteMsg"
                                               title="Delete"><i class="ti ti-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @if(!$package->description_package)
                                {{-- ---- normal service row ---- --}}
                                <tr class="tour-package-item hover:bg-slate-50"
                                    data-package_id='{{ $package->id }}'
                                    data-type='{{ @$package->service()->service_type }}'
                                    data-is_main='@if(!$package->hasParrent()){{ $package->main_hotel }}@else{{ false }}@endif'>
                                    <td class="px-3 py-2 text-center not-click">
                                        <input type="checkbox" value="{{ $package->id }}" class="export_selected" @if($package->itn == 1) checked @endif>
                                    </td>
                                    <td class="px-3 py-2 text-center not-click">
                                        @php
                                            $menu = '';
                                            $tourid = null;
                                        @endphp
                                        @if($package->type == 0 || $package->type == 4)
                                            @php
                                                if (count(@$package->service()->menus) > 0) {
                                                    foreach (@$package->service()->menus as $men) {
                                                        if ($men['id'] == $package->menu_id) {
                                                            $menu = $men['name'];
                                                        }
                                                    }
                                                }
                                            @endphp
                                        @endif
                                        <input type="checkbox" value="{{ $package->id }}" class="export_selected_vch" @if($package->vch == 1) checked @endif>
                                    </td>
                                    <td class="px-3 py-2 not-click">
                                        @if(!$package->hasParrent())
                                            @if(\App\Helper\PermissionHelper::checkPermission('tour_package.edit'))
                                                <input type="text"
                                                       class="form-control timepicker service-time block w-20 rounded border border-slate-300 bg-transparent px-2 h-8 text-xs"
                                                       style="background-color: inherit;"
                                                       data-package_id="{{ $package->id }}"
                                                       data-type='{{ @$package->service()->service_type }}'
                                                       data-is_main='@if(!$package->hasParrent()){{ $package->main_hotel }}@else{{ false }}@endif'
                                                       name="time_from"
                                                       value="{!! $package->time_from !!}">
                                            @else
                                                <span class="font-mono text-xs text-slate-700">{{ $package->time_from }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    {{-- Parent / child hotel star --}}
                                    <td class="px-3 py-2 text-center">
                                        @if(@$package->service()->service_type === 'Hotel')
                                            @if($package->parent_id)
                                                <i class="ti ti-star text-warning-500"></i>
                                            @else
                                                <i class="ti ti-star-filled text-warning-500"></i>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-slate-800">
                                        <span class="package-data"
                                              data-tour_day_id='{{ $tourDate->id }}'
                                              data-package_id='{{ $package->id }}'
                                              data-package-type="{{ @$package->service()->service_type }}"
                                              data-is_main='@if(!$package->hasParrent()){{ $package->main_hotel }}@else{{ false }}@endif'>
                                            {{ $package->name . ' (' . @$package->service()->service_type . ')' }}
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    @if(!$statusPackage->isEmpty())
                                        @php $status = false; $status_name = ''; @endphp
                                        @foreach($statusPackage as $item)
                                            @if($item->id == $package->status)
                                                <td class="px-3 py-2 text-slate-700 {{ $can('tour_package.edit') ? 'tour_package_status' : '' }}"
                                                    data-info-package-id="{{ $package->parent_id != null ? $package->parent_id : $package->id }}"
                                                    data-info-status="{{ $item->name }}"
                                                    data-info-status_id="{{ $item->id }}"
                                                    data-info-package-type="{{ (@$package->service()->service_type === 'Hotel') ? 'hotel' : '' }}{{ (@$package->service()->service_type === 'Transfer') ? 'transfer' : '' }}">
                                                    {{ $item->name }}
                                                </td>
                                                @php $status_name = $item->name; $status = true; @endphp
                                            @endif
                                        @endforeach
                                        @if(!$status)<td class="px-3 py-2"></td>@endif
                                    @else
                                        <td class="px-3 py-2 text-slate-700 {{ $can('tour_package.edit') ? 'tour_package_status' : '' }}"
                                            data-info-package-id="{{ $package->parent_id != null ? $package->parent_id : $package->id }}"
                                            data-info-status="{{ $package->status }}"
                                            data-info-status_id="{{ $package->status->id }}"
                                            data-info-package-type="{{ (@$package->service()->service_type === 'Hotel') ? 'hotel' : '' }}{{ (@$package->service()->service_type === 'Transfer') ? 'transfer' : '' }}">
                                            {{ $package->status }}
                                        </td>
                                    @endif

                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium {{ $package->paid ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">
                                            {{ $package->paid ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-slate-700">{{ $package->pax }} {{ $package->pax_free }}</td>
                                    <td class="px-3 py-2 text-xs text-slate-700">
                                        @foreach($package->room_types_hotel as $item)
                                            <span class="inline-block mr-1">{{ $item->room_types->code }} {{ $item->count }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-3 py-2 text-slate-700">{{ @$package->service()->address_first }}</td>
                                    <td class="px-3 py-2 text-xs text-slate-700">{!! @$package->service()->work_email !!}</td>
                                    <td class="px-3 py-2 text-xs text-slate-700">{!! @$package->service()->work_phone !!}</td>
                                    <td class="px-3 py-2 text-slate-700 {{ $can('tour_package.edit') ? 'service-description' : '' }}">{!! $package->description !!}</td>

                                    @if(!$package->hasParrent())
                                        <td class="px-3 py-2 text-right" style="width:150px;">
                                            <div class="flex items-center justify-end gap-1 flex-wrap">
                                                @if($package->type == 0)
                                                    @php $tourid = $tour->id; @endphp
                                                    <button type="button"
                                                            class="{{ $can('comparison.show') ? 'main-hotel' : 'disabled' }} inline-flex h-7 w-7 items-center justify-center rounded text-xs font-bold text-white {{ $package->main_hotel ? 'bg-success-600' : 'bg-danger-600' }}">M</button>
                                                @endif
                                                @if($can('tour_package.edit'))
                                                    <a href="/tour_package/{{ $package->parent_id != null ? $package->parent_id : $package->id }}/edit"
                                                       class="show-button inline-flex h-7 w-7 items-center justify-center rounded bg-primary-600 text-white hover:bg-primary-700"
                                                       data-link="/tour_package/{{ $package->parent_id != null ? $package->parent_id : $package->id }}/edit"
                                                       title="Edit"><i class="ti ti-edit"></i></a>
                                                @endif
                                                @if($can('tour_package.destroy'))
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       class="delete inline-flex h-7 w-7 items-center justify-center rounded bg-danger-600 text-white hover:bg-danger-700 cursor-pointer"
                                                       data-link="/tour_package/{{ $package->parent_id != null ? $package->parent_id : $package->id }}/deleteMsg"
                                                       title="Delete"><i class="ti ti-trash"></i></a>
                                                @endif
                                                @if($package->parent_id == null)
                                                    @if($can('tour_package.create') && $can('tour_package.destroy'))
                                                        <a href="#"
                                                           class="open-modal-change-service inline-flex h-7 w-7 items-center justify-center rounded bg-warning-500 text-white hover:bg-warning-600"
                                                           style="{{ @$package->getStatusName() !== 'Confirmed' ? '' : 'display:none;' }}"
                                                           data-toggle="modal"
                                                           data-target="#list-tour-packages"
                                                           data-serviceTypeId="{{ $package->type }}"
                                                           data-serviceId="{{ @$package->service()->id }}"
                                                           data-packageId="{{ $package->parent_id != null ? $package->parent_id : $package->id }}"
                                                           data-tour-day-id="{{ $tourDate->id }}"
                                                           data-tour_id="{{ $package->getTour()->id }}"
                                                           data-time-old-service="{{ !$package->hasParrent() ? $package->time_from : null }}"
                                                           data-link="{{ route('tour_package.store') }}"
                                                           title="Change service"><i class="ti ti-arrows-exchange"></i></a>
                                                    @endif

                                                    @if($can('tour_package.edit'))
                                                        @if(@$package->service()->work_email)
                                                            <a href="javascript:void(0);"
                                                               data-info="{{ @$package ?: \GuzzleHttp\json_encode(' ') }}"
                                                               onclick="loadTemplate(JSON.parse($(this).attr('data-info')) ? JSON.parse($(this).attr('data-info')).type : '','{!! @$package->service()->work_email !!}','{!! $package->name !!}','{!! $package->pax !!} {!! $package->pax_free !!}','{!!  @$package->service()->address_first !!}', '{!! @$package->service()->work_email !!}','{!! @$package->service()->work_phone !!}','{!! $package->description !!}','{!! $status_name !!}','{!! $package->time_from  !!}','{!! $package->time_to !!}','{!! $package->supplier_url !!}','{!! $package->total_amount !!}','{{ $menu }}','{{ $tourid }}');"
                                                               class="inline-flex h-7 w-7 items-center justify-center rounded bg-success-600 text-white hover:bg-success-700"
                                                               title="Send email"><i class="ti ti-mail"></i><span class="hidden">{{ $package->supplier_url }}</span></a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
</div>

{{-- ============================================================ --}}
{{-- Modals — IDs / data-bs-* / data-toggle preserved verbatim.    --}}
{{-- ============================================================ --}}

{{-- Change service modal --}}
<div class="modal fade" id="list-tour-packages" tabindex="-1" style="padding-left:17px;padding-right:17px;">
    <div class="modal-dialog modal-lg" style="width:90%;">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg overflow-hidden">
            <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-3">
                <h4 class="modal-title text-sm font-medium text-slate-700">{!! trans('main.Changeservice') !!}</h4>
                <button type="button" class="close text-slate-400 hover:text-slate-600" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="px-5 py-4">
                <div class="overflow-x-auto">
                    <table id="search-table-service-list" class="min-w-full divide-y divide-slate-200 text-sm" style="width:100%!important">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">{!! trans('main.Name') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.Address') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.Country') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.City') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.Phone') !!}</th>
                                <th class="px-3 py-2">{!! trans('main.ContactName') !!}</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Select driver + bus modal --}}
<div class="modal fade" id="select-driver-and-bus" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <form id="form_transfer_buses_drivers">
                <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-3">
                    <h4 class="modal-title text-sm font-medium text-slate-700">{!! trans('main.Selectdriversandbuses') !!}</h4>
                    <button type="button" class="close text-slate-400 hover:text-slate-600" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="px-5 py-4">
                    <div class="list-driver-and-buses"></div>
                    <div class="modal-footer flex items-center justify-end pt-3 mt-3 border-t border-slate-200">
                        <div class="btn-send-driver">
                            <button type="button" class="btn-send-transfer_add pre-loader-func inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700">
                                <i class="ti ti-check"></i>{!! trans('main.Add') !!}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Select driver + bus (transfer package) modal --}}
<div class="modal fade" id="select-driver-and-bus_transfer_package" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <form id="form_transfer_buses_drivers_transfer_package">
                <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-3">
                    <h4 class="modal-title text-sm font-medium text-slate-700">Select drivers and buses</h4>
                    <button type="button" class="close text-slate-400 hover:text-slate-600" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="px-5 py-4">
                    <div class="list-driver-and-buses_transfer_package"></div>
                    <div class="modal-footer flex items-center justify-end pt-3 mt-3 border-t border-slate-200">
                        <div class="btn-send-driver">
                            <button type="button" class="btn-send-transfer_add_transfer_package pre-loader-func inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700">
                                <i class="ti ti-check"></i>{!! trans('main.Add') !!}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hotel confirm status modal --}}
<div class="modal fade" id="confirmed_hotel" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <form id="form_confirmed_hotel">
                <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-3">
                    <h4 class="modal-title text-sm font-medium text-slate-700">{!! trans('main.Confirmedhotel') !!}</h4>
                    <button type="button" class="close text-slate-400 hover:text-slate-600" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body px-5 py-4">
                    <div class="confirmed_hotel_block"></div>
                </div>
                <div class="modal-footer flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
                    <div class="btn-send-confirmed_hotel">
                        <button type="button" class="btn-send-confirmed_hotel_add inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700">
                            <i class="ti ti-check"></i>{!! trans('main.Save') !!}
                        </button>
                        <button type="button" class="btn-send-hotel_cancel inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                            <i class="ti ti-x"></i>{!! trans('main.Cancel') !!}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hotel error popup --}}
<div class="modal fade" id="error_hotel" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <form id="form_eror_hotel">
                <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-3">
                    <h4 class="modal-title text-sm font-medium text-warning-700 inline-flex items-center gap-2"><i class="ti ti-alert-triangle"></i>{!! trans('main.Warning') !!}!</h4>
                    <button type="button" class="close text-slate-400 hover:text-slate-600" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body px-5 py-4">
                    <div class="confirmed_hotel_block">
                        <h3 id="message" class="text-sm text-slate-700 m-0"></h3>
                    </div>
                </div>
                <div class="modal-footer flex items-center justify-end border-t border-slate-200 bg-slate-50 px-5 py-3">
                    <div class="btn-error-hotel">
                        <button type="button" id="ok" class="inline-flex items-center gap-1 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700">Ok</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hotel date picker modal --}}
<div class="modal fade" id="selectDateForHotelPackage" tabindex="-1" aria-labelledby="selectDateForHotelPackageLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-3">
                <h4 class="modal-title text-sm font-medium text-slate-700">{!! trans('main.SelectDate') !!}</h4>
                <button type="button" class="close text-slate-400 hover:text-slate-600" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="px-5 py-4 space-y-3">
                <div class="alert alert-info error_date hidden rounded border border-info-200 bg-info-50 px-3 py-2 text-sm text-info-700 text-center"></div>

                <div class="form-group">
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateFrom') !!}</label>
                    <div class="input-group date relative">
                        <span class="input-group-addon absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="ti ti-calendar"></i></span>
                        {!! Form::text('date_service_package', '', ['class' => 'form-control pull-right datepickerDisabledHotelPackage block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'date_service_package']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Dateto') !!}</label>
                    <div class="input-group date relative">
                        <span class="input-group-addon absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="ti ti-calendar"></i></span>
                        {!! Form::text('date_service_retirement_package', '', ['class' => 'form-control pull-right datepickerDisabledHotelPackage block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'date_service_retirement_package']) !!}
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-200">
                    <button type="button" class="addHotelPackageWithDate pre-loader-func inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700">
                        <i class="ti ti-plus"></i>{!! trans('main.Add') !!}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Transfer date picker modal --}}
<div class="modal fade" id="selectDateForTransferPackage" aria-labelledby="selectDateForTransferPackageLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-3">
                <h4 class="modal-title text-sm font-medium text-slate-700">{!! trans('main.SelectDate') !!}</h4>
                <button type="button" class="close text-slate-400 hover:text-slate-600" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body px-5 py-4 space-y-3">
                <div class="alert alert-info error_date hidden rounded border border-info-200 bg-info-50 px-3 py-2 text-sm text-info-700 text-center"></div>

                <div class="form-group">
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateFrom') !!}</label>
                    <div class="input-group date relative">
                        <span class="input-group-addon absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="ti ti-calendar"></i></span>
                        {!! Form::text('date_service_package', '', ['class' => 'form-control pull-right datepickerDisabledTransferPackage block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'date_service_transfer_package']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateTo') !!}</label>
                    <div class="input-group date relative">
                        <span class="input-group-addon absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="ti ti-calendar"></i></span>
                        {!! Form::text('date_service_retirement_package', '', ['class' => 'form-control pull-right datepickerDisabledTransferPackage block w-full h-9 rounded border border-slate-300 bg-white pl-8 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600', 'id' => 'date_service_transfer_retirement_package']) !!}
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-200">
                    <button type="button" class="addTransferPackageWithDate inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700">
                        {!! trans('main.Next') !!}<i class="ti ti-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Email template composer modal --}}
<div class="modal fade" id="TemplatesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="padding-left:17px;padding-right:17px;">
    <div class="modal-dialog modal-lg" style="width:90%;">
        <form class="modal-content rounded border border-slate-200 bg-white shadow-lg" id="templateSendForm" enctype="multipart/form-data" action="/templates/api/send" method="POST">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <input name="id" id="id" type="hidden" value="">

            <div class="modal-header border-b border-slate-200 px-5 py-3">
                <h3 class="text-sm font-medium text-slate-700 m-0">{!! trans('main.SendTemplate') !!}</h3>
            </div>

            <div class="modal-body px-5 py-4 space-y-3">
                <div class="form-group">
                    <div class="input-group flex items-stretch gap-2">
                        <input name="email" id="email" required placeholder="E-mail:" value=""
                               class="form-control flex-1 block h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        <span class="input-group-addon inline-flex items-center px-3 h-9 rounded border border-slate-300 bg-slate-50 text-xs text-slate-500">{!! trans('main.Template') !!}</span>
                        <select id="template_selector" name="template_selector" class="form-control h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle"></select>
                    </div>
                </div>

                <div class="form-group">
                    <input name="subject" id="subject" placeholder="Subject:" value=""
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                </div>

                <div class="form-group">
                    <textarea name="templatesContent" id="templatesContent" placeholder="Non required Field"
                              class="form-control" style="height:400px; visibility:hidden; display:none;"></textarea>
                </div>

                <div class="form-group">
                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                        <i class="ti ti-paperclip"></i>{!! trans('main.Attachment') !!}
                        <input type="file" name="attachment[]" multiple id="file" class="hidden">
                    </label>
                    <div id="file_name" class="mt-2 text-xs text-slate-500"></div>
                    <script>
                        document.getElementById('file').onchange = function () {
                            $('#file_name').html('Selected files: <br/>');
                            $.each(this.files, function (i, file) { $('#file_name').append(file.name + ' <br/>'); });
                        };
                    </script>
                    <p class="mt-1 text-xs text-slate-500">Max. 32 MB</p>
                </div>
            </div>

            <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-between gap-2">
                <button type="reset" class="modal-close inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50" data-dismiss="modal">
                    <i class="ti ti-x"></i>{!! trans('main.Discard') !!}
                </button>
                <button id="send" type="button" onclick="sendTemplate();" class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">
                    <i class="ti ti-mail"></i>{!! trans('main.Send') !!}
                </button>
            </div>
        </form>
    </div>
</div>

<span id="last_package_object" data-info="{{ @$package ? @$package->description_package == null ? @$package->type : \GuzzleHttp\json_encode('') : \GuzzleHttp\json_encode('') }}"></span>

<script type="text/javascript">

    var unchecked_array = [];
    var unchecked_array_vch = [];

    $(document).ready(function () {
        // $('input.timepicker').timepicker();
        finder.init();
        addService.checkTransferIsExist();
        let package_type = JSON.parse($('#last_package_object').attr('data-info'));
        if (package_type) {
            loadGuestTemplate(package_type, "{!! @$package ? @$package->service()->work_email : '' !!}", "{!! @$package->name !!}", "{!! @$package->pax !!} {!! @$package->pax_free !!}", "{!!  @$package ? @$package->service()->address_first : ''!!}", "{!! @$package ? @$package->service()->work_email : ''!!}", "{!! @$package ? @$package->service()->work_phone  : ''!!}", `{!! @$package->description !!}`, "{!! @$status_name !!}", "{!! @$package->time_from  !!}", "{!! @$package->total_amount !!}", "{{ @$menu }}", "{{ @$tourid }}");
        }
        // $('.timepicker').datetimepicker({
        //     format: 'HH:mm', 'sideBySide' : true,
        //     tooltips: {
        //         incrementHour: '', pickHour: '', decrementHour:'',
        //         incrementMinute: '', pickMinute: '', decrementMinute:'',
        //         incrementSecond: '', pickSecond: '', decrementSecond:'',
        //     }
        // }).on("dp.hide", function (e) {
        //      let timeKey = $(this).attr('name');
        //      $.ajax({
        //         url: '/tour_package/' + $(this).data('package_id') + '/change_time',
        //         method: 'GET',
        //         data: { timeKey: timeKey, timeValue: $(this).val() }
        //      }).done( (res) => { addService.addPackages(); })
        // });

        if ($(document).find('#templatesContent').length > 0) {
            if (CKEDITOR.instances['templatesContent']) {
                CKEDITOR.instances['templatesContent'].destroy(true);
            }
            CKEDITOR.replace('templatesContent', {
                extraPlugins: 'confighelper',
                height: '200px',
                title: false
            });
        }

        $('.export_selected').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        }).on('ifClicked', function (e) {

            $(this).on('ifUnchecked', function (event) {
                unchecked_array[event.target.value] = event.target.value;
                $.ajax({
                    type: 'POST',
                    url: '/update_itnid',
                    data: { id: event.target.value, value: 0 },
                });
            });

            $(this).on('ifChecked', function (event) {
                delete unchecked_array[event.target.value];
                $.ajax({
                    type: 'POST',
                    url: '/update_itnid',
                    data: { id: event.target.value, value: 1 },
                });
            });
        });

        $('.export_selected_vch').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        }).on('ifClicked', function (e) {

            $(this).on('ifUnchecked', function (event) {
                unchecked_array_vch[event.target.value] = event.target.value;
                $.ajax({
                    type: 'POST',
                    url: '/update_voucherid',
                    data: { id: event.target.value, value: 0 },
                });
            });

            $(this).on('ifChecked', function (event) {
                delete unchecked_array_vch[event.target.value];
                $.ajax({
                    type: 'POST',
                    url: '/update_voucherid',
                    data: { id: event.target.value, value: 1 },
                });
            });
        });

    });

    export_to = function (url) {
        console.log(url);
        var out = '?';
        for (var i = 0; i < unchecked_array.length; i++) {
            if (unchecked_array[i]) out += "exclude[]=" + unchecked_array[i] + "&";
        }
        for (var i = 0; i < unchecked_array_vch.length; i++) {
            if (unchecked_array_vch[i]) out += "exclude_vch[]=" + unchecked_array_vch[i] + "&";
        }
        out = out.slice(0, -1);
        if (url.indexOf("landingpage") > 0) {
            $('#landingpage_modal').modal('hide');
            window.open(url + "/" + out, '_blank');
        } else {
            window.location.href = url + "/" + out;
        }
    };

    sendTemplate = function () {
        $('form#templateSendForm').submit(function (event) {
            event.preventDefault();
            $('#TemplatesModal').find('#send').prop('disabled', true);
            var data = new FormData($(this)[0]);
            $.ajax({
                url: '/templates/api/send',
                method: 'POST',
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                data: data
            }).done((res) => {
                console.log(res);
                $('#TemplatesModal').find('#email').val('');
                $('#TemplatesModal').find('#subject').val('');
                // CKEDITOR.instances['templatesContent'].setData('This field is optional');
                $('#TemplatesModal').modal('hide');
            });
            return false;
        });
    };

    loadTemplateById = function (service_id, email, name, pax, address, emailto, phone, description, status, time_from, time_to, supplier_url, price_for_one, menu, tour_id) {
        alert(supplier_url);
        var id = $('#TemplatesModal').find('#template_selector').val();
        $.ajax({
            url: '/templates/api/load',
            method: 'GET',
            data: {
                service_id: service_id, id: id, email: email, name: name, pax: pax, address: address,
                emailto: emailto, phone: phone, description: description, status: status,
                time_from: time_from, time_to: time_to, supplier_url: supplier_url,
                price_for_one: price_for_one, menu: menu, tour_id: tour_id
            }
        }).done((res) => {
            $('#TemplatesModal').find('#email').val(res.email);
            $('#TemplatesModal').find('#id').val(id);
            // if(res.content === '' ) res.content = 'This field is optional';
            CKEDITOR.instances['templatesContent'].setData(res.content);
            $('#TemplatesModal').modal('show');
        });
    };

    loadTemplate = function (service_id, email, name, pax, address, emailto, phone, description, status, time_from, time_to, supplier_url, price_for_one, menu, tour_id) {
        alert(supplier_url);
        console.log(`Supplier Url ${supplier_url}`);
        var selected = '';
        var html = '';

        $('#TemplatesModal').find('#send').prop('disabled', false);
        $('#TemplatesModal').find('#email').val('');
        $('#TemplatesModal').find('#subject').val('');
        $('#TemplatesModal').find('#file').val('');
        $('#TemplatesModal').find('#file_name').text('');
        CKEDITOR.instances['templatesContent'].setData('');

        $.ajax({
            url: '/templates/api/loadServiceTemplates',
            method: 'GET',
            data: { id: service_id },
            success: function (res) {
                for (var i = 0; i < res.templates.length; i++) {
                    (i == 0) ? selected = "selected" : "";
                    if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
                        html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
                    }
                }
                $('#TemplatesModal').find('#template_selector').html(html);
                $('#TemplatesModal').find('#template_selector').on('change', function () {
                    loadTemplateById(service_id, email, name, pax, address, emailto, phone, description, status, time_from, time_to, supplier_url, price_for_one, menu, tour_id);
                });
                loadTemplateById(service_id, email, name, pax, address, emailto, phone, description, status, time_from, time_to, supplier_url, price_for_one, menu, tour_id);
            }
        });
    };

    function loadGuestTemplate(service_id, email, name, pax, address, emailto, phone, description, status, time_from, price_for_one, menu, tour_id) {
        var selected = '';
        var html = '';
        $.ajax({
            url: '/templates/api/loadServiceTemplates',
            method: 'GET',
            data: { id: 0 },
            success: function (res) {
                html += "<option value='' selected disabled hidden>Choose here</option>";
                for (var i = 0; i < res.templates.length; i++) {
                    if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
                        html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
                    }
                }
                $('#template_selector_guest').html(html);
                $('#template_selector_guest').on('change', function () {
                    var id = $('#template_selector_guest').val();
                    $.ajax({
                        url: '/templates/api/load',
                        method: 'GET',
                        data: {
                            service_id: service_id, id: id, email: email, name: name, pax: pax,
                            address: address, emailto: emailto, phone: phone, description: description,
                            status: status, time_from: time_from, price_for_one: price_for_one,
                            menu: menu, tour_id: tour_id
                        }
                    }).done((res) => {
                        CKEDITOR.instances['roomlist_textarea'].setData(res.content);
                    });
                });
            }
        });
    }

</script>
