@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Offer')
@section('content')
    @include('layouts.title', [
        'title' => 'Create Offer',
        'sub_title' => $tour_package->name,
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Offers', 'icon' => 'suitcase', 'route' => route('offers', $tour_package->id)],
            ['title' => 'Create Offer', 'route' => null],
        ],
    ])
    <style>
        .offer-form .card-title { font-size: 1rem; font-weight: 600; }
        .offer-form .form-control, .offer-form .form-select { background-color: #fff; color: #1e293b; }
        /* The layout turns selects into Select2; make them fill the column like the inputs */
        .offer-form .select2-container { width: 100% !important; }
        .offer-form .select2-container--default .select2-selection--single { min-height: 44px; display: flex; align-items: center; }
        .offer-form .select2-container--default .select2-selection--single .select2-selection__arrow { height: 100%; }
        .offer-form .rate-table td, .offer-form .rate-table th { vertical-align: middle; }
        .offer-form .rate-table input[type="number"] { max-width: 140px; }
        .offer-form .policy-list li { display: flex; justify-content: space-between; align-items: center; gap: .75rem; padding: .5rem .75rem; border: 1px solid var(--tblr-border-color, #e6e7e9); border-radius: 6px; margin-bottom: .5rem; background: #fff; }
        .offer-form .policy-list:empty::before { content: 'No cancellation policies added yet.'; color: #6c757d; font-size: .875rem; }
    </style>

    <form method="POST" id="hoteloffers_add_form" class="offer-form"
        action="{{ url('tour_package/' . $tour_package->id . '/offer_update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{ $tour_package->id }}" name="package_id">
        <input type="hidden" value="{{ $tour_package->pax }}" id="pax">

        <div class="mb-3">
            <a href="{{ route('offers', $tour_package->id) }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
            </a>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title">Offer details</h3></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="reference">Your booking reference</label>
                                <input type="text" class="form-control" id="reference" name="reference" placeholder="e.g. HB-12345">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="status">{!! trans('main.Status') !!}</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="Offered No rooms blocked" selected>Offered No rooms blocked</option>
                                    <option value="Offered with Option">Offered with Option</option>
                                    <option value="Waiting List">Waiting List</option>
                                    <option value="Unavailable">Unavailable</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="option_with_date" style="display: none;">
                                <label class="form-label" for="option_date_input">{!! trans('Option Date') !!}</label>
                                <input type="date" class="form-control" id="option_date_input" name="option_with_date">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title">Room rates</h3></div>
                    @php $offerRoomTypes = collect($selected_room_types)->filter()->unique('id'); @endphp
                    @if ($offerRoomTypes->isEmpty())
                        <div class="card-body text-muted">This package has no room types yet. Add rooms to the hotel service to enter rates.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table card-table rate-table mb-0">
                                <thead>
                                    <tr><th>Room type</th><th>Rate</th><th>Breakfast included</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($offerRoomTypes as $selected_room_type)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="room_type_id[]" value="{{ $selected_room_type->id }}">
                                                <label for="rate_{{ $selected_room_type->id }}" class="mb-0">{{ $selected_room_type->name }}</label>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" step="0.01" min="0" id="rate_{{ $selected_room_type->id }}"
                                                    name="room_rate_{{ $selected_room_type->id }}" placeholder="Rate">
                                            </td>
                                            <td>
                                                <label class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="breakfast_{{ $selected_room_type->id }}"
                                                        name="is_breakfast_{{ $selected_room_type->id }}" checked>
                                                    <span class="form-check-label">Included</span>
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title">Extras</h3></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <label class="form-label" for="city_tax">City tax</label>
                                <input class="form-control" type="number" step="0.01" min="0" id="city_tax" name="city_tax">
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label" for="portrage_perperson">Porterage p.p.</label>
                                <input class="form-control" type="number" step="0.01" min="0" id="portrage_perperson" name="portrage_perperson">
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label" for="halfboard">Halfboard supp. p.p.</label>
                                <input class="form-control" type="number" step="0.01" min="0" max="999999" id="halfboard" name="halfboard">
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label" for="children_cost">Children cost</label>
                                <input class="form-control" type="number" step="0.01" min="0" id="children_cost" name="children_cost">
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label" for="foc_after_every_pax">F.O.C.</label>
                                <input class="form-control" type="number" min="0" id="foc_after_every_pax" name="foc_after_every_pax">
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label" for="halfboardMax">Max allowed per group</label>
                                <input class="form-control" type="number" min="0" id="halfboardMax" name="halfboardMax">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="currency">Currency</label>
                                <select name="currency" id="currency" class="form-select">
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ $currency->id == $tour_package->currency ? 'selected' : '' }}>
                                            {{ $currency->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="supplier_file">Hotel file</label>
                                <input class="form-control" type="file" id="supplier_file" name="supplier_file">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="hotel_note">Note</label>
                                <textarea name="hotel_note" id="hotel_note" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title">Other conditions</h3></div>
                    <div class="card-body">
                        <textarea class="form-control" id="otherConditions" name="otherConditions" rows="4"></textarea>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title">Cancellation policies</h3></div>
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-6 col-sm-3">
                                <label class="form-label" for="cancellationDays">Days before arrival</label>
                                <input type="number" class="form-control" id="cancellationDays" min="0">
                            </div>
                            <div class="col-6 col-sm-3">
                                <label class="form-label" for="cancellationPercentage">Free to cancel</label>
                                <input type="number" step="0.01" class="form-control" id="cancellationPercentage" min="0">
                            </div>
                            <div class="col-8 col-sm-4">
                                <label class="form-label" for="cancellationType">Type</label>
                                <select class="form-select" id="cancellationType">
                                    <option value="percentage">Percentage</option>
                                    <option value="amount">Amount</option>
                                </select>
                            </div>
                            <div class="col-4 col-sm-2">
                                <button type="button" class="btn btn-primary w-100" id="addCancellation" title="Add policy">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-hint d-block mt-1">Of the rooms that can be cancelled free of charge.</small>
                        <ul class="list-unstyled policy-list mt-3 mb-0" id="cancellationRequirements"></ul>

                        <label class="form-label mt-3" for="cancellationNote">Additional cancellation policies</label>
                        <textarea class="form-control" id="cancellationNote" name="cancellationNote" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-success" type="submit" id="offerSaveBtn">
                <i class="ti ti-device-floppy me-1"></i>{!! trans('main.Save') !!}
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
(function () {
    var status = document.getElementById('status');
    var optionDate = document.getElementById('option_with_date');
    function toggleOptionDate() {
        optionDate.style.display = status.value === 'Offered with Option' ? '' : 'none';
    }
    // jQuery listener: the select is a Select2 widget, which triggers change through jQuery
    $(status).on('change', toggleOptionDate);
    toggleOptionDate();

    var days = document.getElementById('cancellationDays');
    var amount = document.getElementById('cancellationPercentage');
    var type = document.getElementById('cancellationType');
    var list = document.getElementById('cancellationRequirements');

    function hidden(name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

    // Returns true when a policy was added, false when the inputs are incomplete
    function addPolicy() {
        if (days.value === '' || amount.value === '') {
            return false;
        }
        var item = document.createElement('li');
        var text = document.createElement('span');
        text.textContent = days.value + ' days before arrival: ' + amount.value + ' ' + type.value + ' can be cancelled free of charge';
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-danger btn-sm btn-icon';
        remove.title = 'Remove policy';
        remove.innerHTML = '<i class="ti ti-trash"></i>';
        remove.addEventListener('click', function () { item.remove(); });
        item.appendChild(text);
        item.appendChild(remove);
        item.appendChild(hidden('cancellation_days[]', days.value));
        item.appendChild(hidden('cancellation_percentage[]', amount.value));
        item.appendChild(hidden('cancellation_type[]', type.value));
        list.appendChild(item);
        days.value = '';
        amount.value = '';
        return true;
    }

    document.getElementById('addCancellation').addEventListener('click', function () {
        if (!addPolicy() && typeof window.appToast === 'function') {
            window.appToast('Enter the days before arrival and the free cancellation value.', 'warning');
        }
    });

    var form = document.getElementById('hoteloffers_add_form');
    var saveBtn = document.getElementById('offerSaveBtn');
    form.addEventListener('submit', function (e) {
        if (form.dataset.busy) {
            e.preventDefault();
            return;
        }
        // Keep a policy that was typed in but not added with "+"
        addPolicy();
        form.dataset.busy = '1';
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    });
    window.addEventListener('pageshow', function () {
        delete form.dataset.busy;
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>{!! trans('main.Save') !!}';
    });
})();
</script>
@endpush
