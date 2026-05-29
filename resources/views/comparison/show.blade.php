{{--
    /comparison/{id} — Front Sheet (budget comparison between quoted and
    actual hotel rates per tour day). Chrome migrated to Tailwind; the
    underlying calculations + JS handlers preserved verbatim:
      - Form posts PUT to comparison.update
      - Field names: city_tax[], rooming_list_reserved[],
        visa_confirmation[], hotel_list_sent, rooming_list_received,
        visa_confirmation_sent, final_documents_sent, comments
      - .datepicker class (bootstrap-datepicker binding)
      - .comments-button + data-row-id + data-link (AJAX modal trigger)
      - .city_tax_select / .cityTax (focus = select-all)
      - .rooming_list_reserved + .visa_confirmation (auto-fill date logic)
      - #help (legend tooltip)
      - .finder-disable (excluded from supplier search highlights)
      - utils.js still loaded in post_scripts
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Front Sheet')

@section('content')
@php $tour = $quotation->tour; @endphp

<x-ui.page-header
    title="Front Sheet"
    :description="$quotation->name . ' — ' . $tour->name"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Quotations', 'href' => route('quotation.index')],
        ['label' => $tour->name, 'href' => route('tour.show', ['tour' => $tour->id])],
        ['label' => 'Front Sheet'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
        <span id="help" class="inline-flex h-9 w-9 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700 cursor-pointer" title="Legend">
            <x-ui.icon name="help-circle" size="sm" />
            @include('legend.frontsheet_legend')
        </span>
        <x-ui.button type="submit" form="frontsheet-form" variant="primary" icon="save">
            {{ trans('main.Save') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

@php
    $peopleCount = 0;
    foreach($listRoomsHotel as $room) {
        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code])
            ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count : 0;
    }
@endphp

{{-- Pax + Rooms summary card --}}
<div class="rounded border border-slate-200 bg-white px-5 py-4 mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">Rooms</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($listRoomsHotel as $room)
                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                    {{ $room->room_types->code }} <span class="text-slate-500">×</span> {{ $room->count }}
                </span>
            @endforeach
        </div>
        @if($peopleCount != $tour->pax + $tour->pax_free)
            <div class="mt-3 flex items-start gap-2 rounded border border-warning-600/20 bg-warning-50 px-3 py-2 text-sm text-warning-700">
                <x-ui.icon name="alert-triangle" size="sm" class="mt-0.5 text-warning-600" />
                <div class="flex-1">
                    {{ trans('main.PaxCount') }} ({{ $tour->pax + $tour->pax_free }}) is not equal to the number of people in the rooms ({{ $peopleCount }})
                </div>
            </div>
        @endif
    </div>
    <div>
        <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">Pax</h3>
        <p class="text-2xl font-semibold text-slate-900">
            {{ $tour->pax }}
            @if($tour->pax_free)
                <span class="text-slate-400 text-base font-normal">+ {{ $tour->pax_free }} free</span>
            @endif
        </p>
    </div>
</div>

<form id="frontsheet-form" action="{{ route('comparison.update', ['comparison' => $quotation->id]) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    {{-- ============================================================ --}}
    {{-- Budget comparison table                                       --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="calculator" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Budget comparison</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 finder-disable text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <td class="px-3 py-2 whitespace-nowrap">{{ trans('main.Date') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ trans('main.City') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ trans('main.QuoteHotel') }}</td>
                        @foreach($listRoomsHotel as $room)
                            <td class="px-3 py-2 whitespace-nowrap"
                                @if($room->room_types->code == 'SIN') data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Single suppl." data-bs-title="Single suppl." @endif>
                                Quote {{ $room->room_types->code }}
                            </td>
                        @endforeach
                        <td class="px-3 py-2 whitespace-nowrap">CMFD HOTEL</td>
                        @foreach($listRoomsHotel as $room)
                            <td class="px-3 py-2 whitespace-nowrap"
                                @if($room->room_types->code == 'SIN') data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Single suppl." data-bs-title="Single suppl." @endif>
                                CMFD {{ $room->room_types->code }}
                            </td>
                        @endforeach
                        <td class="px-3 py-2 whitespace-nowrap">{{ trans('main.CityTax') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ trans('main.Option') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">&reg;</td>
                        <td class="px-3 py-2 whitespace-nowrap">VC sent<br>to SHA</td>
                        <td class="px-3 py-2 whitespace-nowrap"></td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ trans('main.Budget') }}</td>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $overallSum = 0; @endphp
                    @foreach($tour->getTourDaysSortedByDate() as $tourDay)
                        @php
                            $quotationBudget = 0;
                            $realBudget = 0;
                            $first_hotel = $tourDay->firstHotel();
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2 text-xs text-slate-600 whitespace-nowrap">{{ $tourDay->date }}</td>
                            <td class="px-3 py-2">
                                @if(!is_null($first_hotel) && method_exists($first_hotel, 'service') && isset($first_hotel->service()->cityObject))
                                    {{ $first_hotel->service()->cityObject->name }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-slate-700">{{ $quotation->getValueByDate($tourDay->date, 'hotelName') }}</td>

                            @foreach($listRoomsHotel as $room)
                                @if($tourDay->firstHotel())
                                    @php
                                        $roomPrice = 0;
                                        if ($room->room_types->code == 'SIN') {
                                            $roomPrice = (int)$quotation->getValueByDate($tourDay->date, $room->room_types->code) + (int)$quotation->getValueByDate($tourDay->date, 'htlpp');
                                        } else {
                                            $roomPrice = (int)$quotation->getValueByDate($tourDay->date, "htlpp") ?? 0;
                                        }
                                        $roomCount = (int)$tourDay->firstHotel()->getRoomTypeCount($room->room_types->id) ?? 0;
                                        $peopleCount = 1;
                                        if ($room->room_types->code == 'DOU') $peopleCount = 2;
                                        if ($room->room_types->code == 'TRI') $peopleCount = 3;
                                        if ($room->room_types->code == 'TWN') $peopleCount = 2;
                                        $quotationBudget += $roomPrice * $roomCount * $peopleCount;
                                    @endphp
                                    <td class="px-3 py-2 text-slate-700"
                                        data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom"
                                        data-original-title="({{ $roomPrice }} * {{ $roomCount }} * {{ $peopleCount }}) = {{ $roomPrice * $roomCount * $peopleCount }}"
                                        data-bs-title="({{ $roomPrice }} * {{ $roomCount }} * {{ $peopleCount }}) = {{ $roomPrice * $roomCount * $peopleCount }}">
                                        {{ $roomPrice }}
                                    </td>
                                @else
                                    <td class="px-3 py-2"></td>
                                @endif
                            @endforeach

                            <td class="px-3 py-2 text-slate-700">
                                @if($tourDay->firstHotel()){{ $tourDay->firstHotel()->name }}@endif
                            </td>

                            @foreach($listRoomsHotel as $room)
                                @if($tourDay->firstHotel())
                                    @php
                                        $roomPrice = $tourDay->firstHotel()->getRoomTypePrice($room->room_type_id);
                                        $roomCount = $tourDay->firstHotel()->getRoomTypeCount($room->room_types->id);
                                        $peopleCount = 1;
                                        if ($room->room_types->code == 'DOU') $peopleCount = 2;
                                        if ($room->room_types->code == 'TRI') $peopleCount = 3;
                                        if ($room->room_types->code == 'TWN') $peopleCount = 2;
                                        $realBudget += $roomPrice * $roomCount * $peopleCount;
                                    @endphp
                                    <td class="px-3 py-2 text-slate-700"
                                        data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom"
                                        data-original-title="({{ $roomPrice }} * {{ $roomCount }} * {{ $peopleCount }}) = {{ $roomPrice * $roomCount * $peopleCount }}"
                                        data-bs-title="({{ $roomPrice }} * {{ $roomCount }} * {{ $peopleCount }}) = {{ $roomPrice * $roomCount * $peopleCount }}">
                                        {{ $tourDay->firstHotel()->getRoomTypePrice($room->room_type_id) }}
                                    </td>
                                @else
                                    <td class="px-3 py-2"></td>
                                @endif
                            @endforeach

                            {{-- City tax (editable input) --}}
                            <td class="px-3 py-2">
                                @php
                                    if ($comparison->comparisonRowByDate($tourDay->date)->city_tax != 0) {
                                        $cityTax = $comparison->comparisonRowByDate($tourDay->date)->city_tax;
                                    } else {
                                        $cityTax = $tourDay->firstHotel() ? $tourDay->firstHotel()->city_tax : 0;
                                    }
                                @endphp
                                {{ Form::input('text', 'city_tax[' . $comparison->comparisonRowByDate($tourDay->date)->id . ']', $cityTax, ['class' => 'form-control cityTax city_tax_select block w-20 h-8 rounded border border-slate-300 bg-white px-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) }}
                            </td>

                            {{-- Option / status --}}
                            <td class="px-3 py-2 text-slate-700">
                                @if($tourDay->firstHotel()){{ $tourDay->firstHotel()->getStatusName() }}@endif
                            </td>

                            {{-- ® rooming list checkbox --}}
                            <td class="px-3 py-2 text-center">
                                {{ Form::checkbox('rooming_list_reserved[]', $comparison->comparisonRowByDate($tourDay->date)->id, $comparison->comparisonRowByDate($tourDay->date)->rooming_list_reserved, ['class' => 'rooming_list_reserved h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30']) }}
                            </td>

                            {{-- VC sent to SHA checkbox --}}
                            <td class="px-3 py-2 text-center">
                                {{ Form::checkbox('visa_confirmation[]', $comparison->comparisonRowByDate($tourDay->date)->id, $comparison->comparisonRowByDate($tourDay->date)->visa_confirmation, ['class' => 'visa_confirmation h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30']) }}
                            </td>

                            {{-- Comments button (opens AJAX modal) --}}
                            <td class="px-3 py-2 text-center">
                                <a class="comments-button inline-flex items-center gap-1 rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-700 hover:bg-slate-50 cursor-pointer"
                                   data-row-id="{{ $comparison->comparisonRowByDate($tourDay->date)->id }}"
                                   data-link="{{ route('comparison.comments', ['id' => $comparison->comparisonRowByDate($tourDay->date)->id]) }}/">
                                    @php $cmtCount = \App\Helper\AdminHelper::getComparisonRowCommentsCount($comparison->comparisonRowByDate($tourDay->date)->id); @endphp
                                    @if($cmtCount > 0)
                                        <span class="inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-warning-50 px-1 text-[10px] font-medium text-warning-700">{{ $cmtCount }}</span>
                                    @endif
                                    <x-ui.icon name="message-circle" size="xs" />
                                </a>
                            </td>

                            {{-- Budget per pax --}}
                            <td class="px-3 py-2 text-right font-mono text-slate-800"
                                data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="top"
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
                            </td>
                        </tr>
                    @endforeach

                    {{-- Footer row --}}
                    <tr class="bg-slate-50">
                        <td colspan="{{ 8 + count($listRoomsHotel) * 2 }}" class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.ENDOFSERVICE') }}</td>
                        <td class="px-3 py-2 text-right font-medium text-slate-700">&Sigma; =</td>
                        <td class="px-3 py-2 text-right font-mono font-semibold text-slate-900">{{ round($overallSum, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Status dates + important comments                            --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="calendar-check" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Status &amp; communications</h2>
        </div>
        <dl class="px-5 py-5 divide-y divide-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3 first:pt-0">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 sm:col-span-1 self-center">{{ trans('main.GoAheadDate') }}</dt>
                <dd class="text-sm text-slate-700 sm:col-span-2">{{ $tour->ga }}</dd>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 sm:col-span-1 self-center">{{ trans('main.HotelListSent') }}</dt>
                <dd class="sm:col-span-2">
                    <div class="relative max-w-[180px]">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-ui.icon name="calendar" size="sm" />
                        </span>
                        <input type="text" name="hotel_list_sent" value="{{ $comparison->hotel_list_sent }}"
                               class="form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                </dd>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 sm:col-span-1 self-center">{{ trans('main.Roominglistreceived') }}</dt>
                <dd class="sm:col-span-2">
                    <div class="relative max-w-[180px]">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-ui.icon name="calendar" size="sm" />
                        </span>
                        <input type="text" name="rooming_list_received" value="{{ $comparison->rooming_list_received }}"
                               class="form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                </dd>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 sm:col-span-1 self-center">{{ trans('main.Visaconfirmationsent') }}</dt>
                <dd class="sm:col-span-2">
                    <div class="relative max-w-[180px]">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-ui.icon name="calendar" size="sm" />
                        </span>
                        <input type="text" name="visa_confirmation_sent" value="{{ $comparison->visa_confirmation_sent }}"
                               class="form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                </dd>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                <dt class="text-sm font-semibold text-slate-700 sm:col-span-1 self-center">{{ trans('main.Finaldocumentssent') }}</dt>
                <dd class="sm:col-span-2">
                    <div class="relative max-w-[180px]">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-ui.icon name="calendar" size="sm" />
                        </span>
                        <input type="text" name="final_documents_sent" value="{{ $comparison->final_documents_sent }}"
                               class="form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                </dd>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3 last:pb-0">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 sm:col-span-1 self-start">{{ trans('main.IMPORTANTCOMMENTSEMAILS') }}</dt>
                <dd class="sm:col-span-2">
                    <textarea name="comments" rows="6"
                              class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">{{ $comparison->comments }}</textarea>
                </dd>
            </div>
        </dl>
    </div>

    {{-- Form footer --}}
    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ route('tour.show', ['tour' => $tour->id]) }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>
@stop

@section('post_scripts')
<script>
    let comments = {
        init: function () { comments.bind(); },
        bind: function () {
            $('.comments-button').click(function () { /* handled below */ });
        }
    };

    let comparison = {
        init: function () { comparison.bind(); },
        bind: function () {
            $('.visa_confirmation').on('click', function () {
                if (comparison.isAllVisasConfirmed()) {
                    if ($('input[name=visa_confirmation_sent]').val() == '') {
                        $('input[name=visa_confirmation_sent]').val(comparison.today());
                    }
                }
            });
            $('.rooming_list_reserved').on('click', function () {
                if (comparison.isAllRoomingListReserved()) {
                    if ($('input[name=rooming_list_received]').val() == '') {
                        $('input[name=rooming_list_received]').val(comparison.today());
                    }
                }
            });

            $('.city_tax_select').on('focus', function () { $(this).select(); });

            $('input[name=rooming_list_received]').change(function () {
                if ($(this).val() != '') comparison.checkAllRoomingListReserved();
            });
            $('input[name=visa_confirmation_sent]').change(function () {
                if ($(this).val() != '') comparison.checkAllVisasConfirmed();
            });

            // AJAX-load comments modal: response is injected into the global
            // #myModal (Ajaxis modal in the layout) and shown.
            $(document).on('click', '.comments-button', function () {
                $.ajax({
                    async: true,
                    type: 'get',
                    url: $(this).data('link'),
                    data: {
                        reply_message: $(this).data('reply-message'),
                        reply_folder: $(this).data('reply-folder'),
                        reply_to: $(this).data('to')
                    },
                    success: function (response) {
                        $('#myModal').html(response);
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            bootstrap.Modal.getOrCreateInstance(document.getElementById('myModal')).show();
                        } else {
                            $('#myModal').modal();
                        }
                    }
                });
            });
        },
        today: function () {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1;
            var yyyy = today.getFullYear();
            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;
            return yyyy + '-' + mm + '-' + dd;
        },
        isAllRoomingListReserved: function () { return $('.rooming_list_reserved:not(:checked)').length == 0; },
        isAllVisasConfirmed:      function () { return $('.visa_confirmation:not(:checked)').length == 0; },
        checkAllRoomingListReserved: function () { return $('.rooming_list_reserved:not(:checked)').prop('checked', true); },
        checkAllVisasConfirmed:      function () { return $('.visa_confirmation:not(:checked)').prop('checked', true); },
    };
    $(document).ready(function () { comparison.init(); });
</script>
<script src="/js/utils.js"></script>
@stop
