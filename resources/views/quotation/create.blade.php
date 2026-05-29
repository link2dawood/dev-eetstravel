{{--
    /quotation/{tourId}/create — create-a-quotation-for-a-tour form.
    Visual chrome rewritten in Tailwind. The pricing grid behaviour
    (inline-input cells, save handler, name-toggle) is JS-driven via
    resources/js/quotation.js. Every JS hook is preserved verbatim:
      - #quotation_name (input id; .saved JS reads it on submit)
      - #quotation_body / #quotation_table ids
      - .quotation-row + data-row attribute
      - data-column + data-value on each <td>
      - .saved button class — triggers the actual POST in quotation.js
      - .namesToggle, .hideTitle, .hide, .validate-name (toggle + validation JS)
      - tourId global + calculationArray window var
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Quotation')

@section('content')
<x-ui.page-header
    title="Create Quotation"
    :description="$tour->name"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => $tour->name, 'href' => route('tour.show', ['tour' => $tour->id])],
        ['label' => 'Create Quotation'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
        {{-- The .saved class is what quotation.js binds the submit handler
             to — preserve it. Variant/icon styling is purely visual. --}}
        <x-ui.button type="button" variant="primary" icon="save" class="saved">
            {{ trans('main.Save') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="rounded border border-slate-200 bg-white">
    {{-- Card header --}}
    <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-between gap-3">
        <h2 class="text-sm font-medium text-slate-700 flex items-center gap-2">
            <x-ui.icon name="calculator" size="sm" class="text-slate-400" />
            Quotation Details
        </h2>
        <a href="#" class="namesToggle hideTitle inline-flex h-8 items-center gap-1.5 rounded border border-slate-300 bg-white px-3 text-xs font-medium text-slate-700 hover:bg-slate-50">
            <x-ui.icon name="eye" size="xs" />
            {{ trans('main.Showtitles') }}
        </a>
    </div>

    {{-- Card body --}}
    <div class="px-5 py-5 space-y-5" id="quotation_body">

        {{-- Name + inline validation. .hide is preserved as a class on
             .validate-name because the legacy JS toggles it via $.show()/$.hide(). --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="quotation_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    Quotation Name <span class="text-danger-600">*</span>
                </label>
                <input type="text" id="quotation_name" placeholder="Enter quotation name"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <div class="hide validate-name inline-flex items-center gap-1.5 rounded bg-danger-50 border border-danger-600/20 px-3 py-2 text-sm text-danger-700">
                    <x-ui.icon name="alert-circle" size="sm" />
                    {{ trans('main.Nameisrequiredfield') }}
                </div>
            </div>
        </div>

        <script>
            let tourId = {{ $tour->id }};
            $(document).on('blur', 'input', function () {
                // Reserved for future per-cell handlers — currently a no-op
                // mirror of the legacy template's logic. Kept verbatim so
                // anything depending on the event delegation chain still fires.
                let data_row    = $(this).attr('data_row');
                let data_column = $(this).attr('data_column');
            });
        </script>
        {{ csrf_field() }}

        {{-- ============================================================ --}}
        {{-- Pricing grid table. Wrapped in overflow-x-auto so 17 columns  --}}
        {{-- scroll horizontally on small screens without breaking layout. --}}
        {{-- The .quotation.css overrides for input styling live in        --}}
        {{-- the inline <style> block below.                                --}}
        {{-- ============================================================ --}}
        <div class="rounded border border-slate-200 overflow-x-auto">
            <table id="quotation_table" class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-3 py-2 whitespace-nowrap">{{ trans('main.Date') }}</th>
                        <th class="px-3 py-2 whitespace-nowrap">{{ trans('main.City') }}</th>
                        <th class="px-3 py-2 whitespace-nowrap">{{ trans('main.Hotel') }}</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Single Suppl.">SS</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-column="Hotel P.P" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Hotel P.P">HPP</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-column="lunchName" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Lunch Name">L.Name</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Lunch">Lun</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-column="dinnerName" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Dinner Name">D.Name</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Dinner">Din</th>
                        <th class="px-3 py-2 whitespace-nowrap">Entr</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Comments">Com</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Local G\D">LGD</th>
                        <th class="px-3 py-2 whitespace-nowrap">BUS</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Group Cost">GC</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Driver">Dri</th>
                        <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Porterage">Por</th>
                    </tr>
                </thead>

                @if(empty($quotation))
                    <tbody id="quotation_table" class="divide-y divide-slate-100">
                        @php
                            $sortedTourDays = $tour->tour_days->sortBy(function ($tourDay) {
                                return $tourDay->date;
                            });
                        @endphp
                        @foreach($sortedTourDays as $row => $tour_day)
                            <tr class="quotation-row hover:bg-slate-50" data-row="{{ $row }}">
                                <td class="px-3 py-2 text-xs text-slate-600 whitespace-nowrap" data-column="date">{{ $tour_day->date }}</td>
                                <td class="px-3 py-2" data-column="cityName">
                                    @if($tour_day->firstHotel() && $tour_day->firstHotel()->service()->cityObject)
                                        {{ $tour_day->firstHotel()->service()->cityObject->name }}
                                    @endif
                                </td>
                                <td class="px-3 py-2" data-column="hotelName"
                                    data-value="@if($tour_day->firstHotel()){{ $tour_day->firstHotel()->name }}@endif">
                                    @if($tour_day->firstHotel()){{ $tour_day->firstHotel()->name }}@endif
                                    <input type="text" />
                                </td>
                                @foreach($listRoomsHotel as $room)
                                    @if($room->room_types->code == 'SIN')
                                        <td class="hotelRooms px-3 py-2"
                                            data-column="{{ $room->room_types->code }}"
                                            data-value="@if($tour_day->firstHotel()){{ $tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true) }}@endif">
                                            @if($tour_day->firstHotel())
                                                @if($room->room_types->code == 'SIN')
                                                    {{ $tour_day->firstHotel()->getSinglePrice() }}
                                                @else
                                                    {{ $tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true) }}
                                                @endif
                                            @endif
                                            <input type="text" />
                                        </td>
                                    @endif
                                @endforeach
                                <td class="px-3 py-2" data-column="htlpp"><input type="text" /></td>
                                <td class="px-3 py-2" data-column="lunchName">
                                    @if($tour_day->firstRestaurant()){{ $tour_day->firstRestaurant()->name }}@endif
                                    <input type="text" />
                                </td>
                                <td class="px-3 py-2" data-column="lunch">
                                    @if($tour_day->firstRestaurant()){{ $tour_day->firstRestaurant()->total_amount }}@endif
                                    <input type="text" />
                                </td>
                                <td class="px-3 py-2" data-column="dinnerName">
                                    @if($tour_day->secondRestaurant()){{ $tour_day->secondRestaurant()->name }}@endif
                                    <input type="text" />
                                </td>
                                <td class="px-3 py-2" data-column="dinner">
                                    @if($tour_day->secondRestaurant()){{ $tour_day->secondRestaurant()->total_amount }}@endif
                                    <input type="text" />
                                </td>
                                <td class="px-3 py-2" data-column="entrance"><input type="text" /></td>
                                <td class="px-3 py-2" data-column="comments"><input type="text" /></td>
                                <td class="px-3 py-2" data-column="local_g_d"><input type="text" /></td>
                                <td class="px-3 py-2" data-column="bus"><input type="text" /></td>
                                <td class="px-3 py-2" data-column="group_cost"><input type="text" /></td>
                                <td class="px-3 py-2" data-column="driver"><input type="text" /></td>
                                <td class="px-3 py-2" data-column="porterage"><input type="text" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    {{-- Existing quotation rows (re-rendered for "add column" flow). --}}
                    <tbody id="quotation_table" class="divide-y divide-slate-100">
                        @php
                            $sortedTourDays = $tour->tour_days->sortBy(function ($tourDay) { return $tourDay->date; });
                            $sortedQuotationRows = $quotation->rows->sortBy(function ($quotationRow) {
                                return $quotationRow->getValueByKey('date')->value ?? '';
                            });
                            $data_row = -1;
                            $total_days = count($sortedTourDays);
                            $rows_count = count($sortedQuotationRows);
                            $counter = 0;
                            $columns = array("date","cityName","hotelName","SIN","htlpp","lunchName","lunch","dinnerName","dinner","entrance","comments","local_g_d","bus","group_cost","driver","porterage");
                            $insertIndex = array_search("hotelName", $columns);
                        @endphp
                        @foreach($sortedQuotationRows as $key => $row)
                            @php
                                $data_row = $data_row + 1;
                                if ($total_days == $data_row) { break; }
                            @endphp
                            <tr class="quotation-row hover:bg-slate-50" data-row="{{ $data_row }}">
                                @foreach($columns as $column)
                                    @php $found = false; @endphp
                                    @foreach($row->values as $value)
                                        @if($value->key === $column)
                                            @php $found = true; @endphp
                                            @if(!empty($value->value) && in_array($value->key, $columns))
                                                <td class="px-3 py-2" data-column="{{ $value->key }}">{{ $value->value }}</td>
                                            @elseif(in_array($value->key, $columns))
                                                <td class="px-3 py-2" data-column="{{ $value->key }}">
                                                    <input type="text" value="{{ $value->value }}" />
                                                </td>
                                            @endif
                                            @break
                                        @endif
                                    @endforeach
                                    @if(!$found)
                                        <td class="px-3 py-2" data-column="{{ $column }}"></td>
                                    @endif
                                @endforeach
                                @foreach($quotation->additional_columns as $column)
                                    <td class="additional-cell px-3 py-2 @if($column->type == 'all')quotation-cell-general @endif @if($column->type == 'person')quotation-cell-per-person @endif"
                                        data-column="{{ $column->name }}">
                                        {{ @$quotation->getAdditionalColumnValueCell($row->id, $column->name)->value }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        @foreach($sortedTourDays as $row => $tour_day)
                            @php $counter++; @endphp
                            @if($counter > $rows_count)
                                <tr class="quotation-row hover:bg-slate-50" data-row="{{ $row }}">
                                    <td class="px-3 py-2 text-xs text-slate-600 whitespace-nowrap" data-column="date">{{ $tour_day->date }}</td>
                                    <td class="px-3 py-2" data-column="cityName">
                                        @if($tour_day->firstHotel() && $tour_day->firstHotel()->service()->cityObject)
                                            {{ $tour_day->firstHotel()->service()->cityObject->name }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2" data-column="hotelName"
                                        data-value="@if($tour_day->firstHotel()){{ $tour_day->firstHotel()->name }}@endif">
                                        @if($tour_day->firstHotel()){{ $tour_day->firstHotel()->name }}@endif
                                    </td>
                                    @foreach($listRoomsHotel as $room)
                                        <td class="hotelRooms px-3 py-2"
                                            data-column="{{ $room->room_types->code }}"
                                            data-value="@if($tour_day->firstHotel()){{ $tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true) }}@endif">
                                            @if($tour_day->firstHotel())
                                                @if($room->room_types->code == 'SIN')
                                                    {{ $tour_day->firstHotel()->getSinglePrice() }}
                                                @else
                                                    {{ $tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true) }}
                                                @endif
                                            @endif
                                            <input type="text" />
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-2" data-column="lunchName">
                                        @if($tour_day->firstRestaurant()){{ $tour_day->firstRestaurant()->name }}@endif
                                        <input type="text" />
                                    </td>
                                    <td class="px-3 py-2" data-column="lunch">
                                        @if($tour_day->firstRestaurant()){{ $tour_day->firstRestaurant()->total_amount }}@endif
                                        <input type="text" />
                                    </td>
                                    <td class="px-3 py-2" data-column="dinnerName">
                                        @if($tour_day->secondRestaurant()){{ $tour_day->secondRestaurant()->name }}@endif
                                        <input type="text" />
                                    </td>
                                    <td class="px-3 py-2" data-column="dinner">
                                        @if($tour_day->secondRestaurant()){{ $tour_day->secondRestaurant()->total_amount }}@endif
                                        <input type="text" />
                                    </td>
                                    <td class="px-3 py-2" data-column="entrance"></td>
                                    <td class="px-3 py-2" data-column="comments"></td>
                                    <td class="px-3 py-2" data-column="local_g_d"></td>
                                    <td class="px-3 py-2" data-column="bus"></td>
                                    <td class="px-3 py-2" data-column="group_cost"></td>
                                    <td class="px-3 py-2" data-column="driver"></td>
                                    <td class="px-3 py-2" data-column="porterage"></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>

    {{-- Card footer with the bottom Save button --}}
    <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2 rounded-b">
        <x-ui.button type="button" variant="primary" icon="save" class="saved">
            {{ trans('main.Save') }}
        </x-ui.button>
    </div>
</div>

@push('styles')
<style>
    /* The cells render the read-only value AND a sibling <input> for editing.
       Make the inputs blend cleanly without a hard Bootstrap border that
       conflicts with the slate table grid. */
    #quotation_table td input[type="text"] {
        display: block;
        width: 100%;
        min-width: 70px;
        padding: 0.25rem 0.5rem;
        margin-top: 0.25rem;
        font-size: 0.8125rem;
        color: #334155;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    #quotation_table td input[type="text"]:focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.18);
    }
    /* Highlight cells where the user has values already so they read as
       "this is editable data" rather than "this cell is empty". */
    #quotation_table td {
        vertical-align: top;
    }
</style>
@endpush
@stop

@section('post_scripts')
    <script>var calculationArray = {};</script>
    <script type="text/javascript" src='{{ asset('js/quotation.js') }}'></script>
@stop
