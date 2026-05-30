@extends('scaffold-interface.layouts.tabler-app')
@section('title','New hotel offer')

@section('content')
<x-ui.page-header
    title="New hotel offer"
    :description="$tour_package->name"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tour package'],
        ['label' => 'Hotel offer', 'href' => route('offers', $tour_package->id)],
        ['label' => 'New'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(Session::has('success'))
    <div class="mb-4 flex items-start gap-3 rounded border border-success-600/20 bg-success-50 px-4 py-3 text-sm text-success-700">
        <x-ui.icon name="check-circle-2" class="mt-0.5 text-success-600" />
        <div class="flex-1">{{ Session::get('success') }}</div>
    </div>
@endif

<form method="POST" id="hoteloffers_add_form" class="form-light space-y-4"
      action='{!! url('tour_package') !!}/{!! $tour_package->id !!}/offer_update'
      enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ Session::token() }}">
    <input type="hidden" value="{{ $tour_package->pax }}" id="pax">
    <input type="hidden" value="{{ $tour_package->id }}" name="package_id">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Left column: rates + status + room rates --}}
        <div class="space-y-4">

            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="receipt" size="sm" /></div>
                    <h2 class="text-sm font-medium text-slate-700">Reference & status</h2>
                </div>
                <div class="px-5 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Booking reference</label>
                        <input type="text" name="reference" placeholder="Your booking reference"
                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Status') !!}</label>
                        <select name="status" id="status"
                                class="form-select form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            <option value="Offered No rooms blocked" selected>Offered No rooms blocked</option>
                            <option value="Offered with Option">Offered with Option</option>
                            <option value="Waiting List">Waiting List</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div id="option_with_date">
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('Option Date') !!}</label>
                        <input type="date" name="option_with_date"
                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    </div>
                </div>
            </div>

            {{-- Room type rates --}}
            @php
                $roomCodeLabels = [
                    'SIN' => ['label' => 'Single', 'input' => 'singleRate', 'cbox' => 'singleBreakfastIncluded'],
                    'TWN' => ['label' => 'Twin',   'input' => 'twinRate',   'cbox' => 'twinBreakfastIncluded'],
                    'DOU' => ['label' => 'Double', 'input' => 'doubleRate', 'cbox' => 'doubleBreakfastIncluded'],
                    'TRI' => ['label' => 'Triple', 'input' => 'tripleRate', 'cbox' => 'tripleBreakfastIncluded'],
                    'SIE' => ['label' => 'Suite',  'input' => 'suiteRate',  'cbox' => 'suiteBreakfastIncluded'],
                    'DFS' => ['label' => 'Double for single', 'input' => 'dfsRate', 'cbox' => 'dfsBreakfastIncluded'],
                ];
            @endphp
            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="bed" size="sm" /></div>
                    <h2 class="text-sm font-medium text-slate-700">Room rates</h2>
                </div>
                <div class="px-5 py-5 space-y-3">
                    @foreach($selected_room_types as $selected_room_type)
                        <input type="hidden" name="room_type_id[]" value="{{ $selected_room_type->id }}">
                        @if(isset($roomCodeLabels[$selected_room_type->code]))
                            @php $cfg = $roomCodeLabels[$selected_room_type->code]; @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                <div class="sm:col-span-5">
                                    <label for="{{ $cfg['input'] }}" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ $cfg['label'] }} Room Rate</label>
                                    <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                           type="text" id="{{ $cfg['input'] }}" name="room_rate_{{ $selected_room_type->id }}" placeholder="Rate">
                                </div>
                                <div class="sm:col-span-7 flex items-center gap-2 pb-1">
                                    <input type="checkbox" id="{{ $cfg['cbox'] }}"
                                           name="is_breakfast_{{ $selected_room_type->id }}" checked
                                           class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                    <label for="{{ $cfg['cbox'] }}" class="text-sm text-slate-700">Breakfast included</label>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Extras: city tax, portrage, halfboard, foc, max per group, currency --}}
            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="settings" size="sm" /></div>
                    <h2 class="text-sm font-medium text-slate-700">Extras & currency</h2>
                </div>
                <div class="px-5 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="city_tax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">City Tax</label>
                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                               type="number" id="city_tax" name="city_tax" min="0">
                    </div>
                    <div>
                        <label for="portrage_perperson" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Porterage P.P</label>
                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                               type="number" id="portrage_perperson" name="portrage_perperson" min="0">
                    </div>
                    <div>
                        <label for="halfboard" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Halfboard Supp P.P</label>
                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                               type="number" id="halfboard" name="halfboard" min="0" max="999999">
                    </div>
                    <div>
                        <label for="children_cost" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Children Cost</label>
                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                               type="number" id="children_cost" name="children_cost" min="0">
                    </div>
                    <div>
                        <label for="foc_after_every_pax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">F.O.C</label>
                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                               type="number" id="foc_after_every_pax" name="foc_after_every_pax" min="0">
                    </div>
                    <div>
                        <label for="halfboardMax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Max allowed per group</label>
                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                               type="number" id="halfboardMax" name="halfboardMax" min="0">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="currency" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Currency</label>
                        <select name="currency" id="currency"
                                class="form-control form-select block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ $currency->id == $tour_package->currency ? 'selected' : '' }}>{{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="paperclip" size="sm" /></div>
                    <h2 class="text-sm font-medium text-slate-700">Supplier file & note</h2>
                </div>
                <div class="px-5 py-5 space-y-3">
                    <input class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 shadow-subtle"
                           type="file" id="formFileDisabled" name="supplier_file">
                    <textarea name="hotel_note" id="hotel_note" rows="4" placeholder="Note"
                              class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                </div>
            </div>
        </div>

        {{-- Right column: conditions + cancellation policies --}}
        <div class="space-y-4">

            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="file-text" size="sm" /></div>
                    <h2 class="text-sm font-medium text-slate-700">Other conditions</h2>
                </div>
                <div class="px-5 py-5">
                    <textarea class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                              id="otherConditions" name="otherConditions" rows="4"></textarea>
                </div>
            </div>

            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="alert-octagon" size="sm" /></div>
                    <h2 class="text-sm font-medium text-slate-700">Cancellation policies</h2>
                </div>
                <div class="px-5 py-5 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-3">
                            <label for="cancellationDays" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Days before arrival</label>
                            <input type="number" id="cancellationDays" min="0"
                                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="cancellationPercentage" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Amount / %</label>
                            <input type="number" id="cancellationPercentage" min="0"
                                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        </div>
                        <div class="sm:col-span-4">
                            <label for="cancellationType" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Type</label>
                            <select id="cancellationType"
                                    class="form-control form-select block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                <option value="percentage">Percentage</option>
                                <option value="amount">Amount</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <button type="button" id="addCancellation"
                                    class="inline-flex w-full h-9 items-center justify-center gap-1 rounded bg-primary-600 px-3 text-sm text-white hover:bg-primary-700">
                                <x-ui.icon name="plus" size="sm" /> Add
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500">…of rooms can be cancelled free of charge.</p>

                    <div id="cancellationRequirements" class="text-sm text-slate-700"></div>

                    <div>
                        <label for="cancellationNote" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Additional cancellation policies</label>
                        <textarea class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                  id="cancellationNote" name="cancellationNote" rows="4"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ route('offers', $tour_package->id) }}" variant="secondary">{!! trans('main.Cancel') !!}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{!! trans('main.Save') !!}</x-ui.button>
    </div>
</form>

<script>
    $("#option_with_date").parent().find('label[for=status]'); // keep no-op for parity
    $("#option_with_date").hide();
    $("#status").change(function () {
        if ($(this).val() === "Offered with Option") {
            $("#option_with_date").show();
        } else {
            $("#option_with_date").hide();
        }
    });

    $('#commentForm').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function (response) { $("#comment_list").append(response); },
            error: function (xhr) {}
        });
    });

    document.getElementById('addCancellation').addEventListener('click', function () {
        const daysBeforeArrival = document.getElementById('cancellationDays').value;
        const percentageOrAmount = document.getElementById('cancellationPercentage').value;
        const cancellationType = document.getElementById('cancellationType').value;

        if (daysBeforeArrival && percentageOrAmount) {
            const daysInput = document.createElement('input');
            daysInput.type = 'hidden';
            daysInput.name = `cancellation_days[]`;
            daysInput.value = percentageOrAmount;

            const amountInput = document.createElement('input');
            amountInput.type = 'hidden';
            amountInput.name = `cancellation_percentage[]`;
            amountInput.value = daysBeforeArrival;

            const cancellationTypeInput = document.createElement('input');
            cancellationTypeInput.type = 'hidden';
            cancellationTypeInput.name = `cancellation_type[]`;
            cancellationTypeInput.value = cancellationType;

            const form = document.getElementById('hoteloffers_add_form');
            form.appendChild(daysInput);
            form.appendChild(amountInput);
            form.appendChild(cancellationTypeInput);

            const cancellationRequirements = document.getElementById('cancellationRequirements');
            const newRequirement = document.createElement('div');
            newRequirement.className = 'flex items-start gap-2 rounded border border-slate-200 bg-slate-50 px-3 py-2';
            newRequirement.innerHTML = `<p class="m-0">${daysBeforeArrival} days before arrival: ${percentageOrAmount} ${cancellationType} can be cancelled free of charge</p>`;
            cancellationRequirements.appendChild(newRequirement);
        }
    });
</script>
@endsection
