<form id="form_update_bus_table_tour">
    <div class="d-flex flex-wrap align-items-center gap-4 mb-3">
        <label class="form-check trip_full mb-0">
            <input class="form-check-input" type="radio" name="tour_mode" value="0" checked>
            <span class="form-check-label">{!!trans('main.FullTour')!!}</span>
        </label>
        <label class="form-check trip_day mb-0">
            <input class="form-check-input" type="radio" name="tour_mode" value="1">
            <span class="form-check-label">{!!trans('main.ForDayTour')!!}</span>
        </label>
    </div>

    <input type='hidden' name='_token' value='{{Session::token()}}'>
    <input type="hidden" name="bus_day_id" value="{{ $busDay->id }}">
    <input type="hidden" name="tour_package_id_bus_day" value="{{ $busDay->tour_package_id }}">

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label" for="bus_status_update_tour">{!!trans('main.Status')!!}</label>
            <select id="bus_status_update_tour" name="bus_status_update_tour" class="form-select bus-edit-select">
                @foreach( $bus_statuses as $status)
                    <option {{ $status->id == $busDay->status_id ? 'selected' : '' }}
                            value="{{$status->id}}">{{$status->name}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="drivers_tour_update">{!!trans('main.Drivers')!!}</label>
            @if(count($drivers) > 0)
                <select name="drivers_tour_update[]" id="drivers_tour_update" class="select2 js-state form-select bus-edit-select" multiple="multiple">
                    @foreach($drivers as $driver)
                        <option {{ $selected_drivers->contains('driver_id', $driver->id) ? 'selected' : '' }}
                                class="transfer_driver"
                                value="{{ $driver->id }}"
                        >{{ $driver->name }}</option>
                    @endforeach
                </select>
            @else
                <div class="form-control-plaintext text-muted">{!!trans('main.Transferwithoutdrivers')!!}</div>
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label" for="buses_tour_update">{!!trans('main.Bus')!!}</label>
            @if(count($buses) > 0)
                <select name="bus_tour_update" id="buses_tour_update" class="form-select bus-edit-select" required>
                    @foreach($buses as $bus)
                        <option {{ $busDay->bus_id == $bus->id ? 'selected' : '' }}
                                class="transfer_driver"
                                value="{{ $bus->id }}"
                        >{{ $bus->name }} ({{ $bus->bus_number }})</option>
                    @endforeach
                </select>
            @else
                <div class="form-control-plaintext text-muted">{!!trans('main.Transferwithoutbus')!!}</div>
            @endif
        </div>
    </div>
</form>

<script>
    $('#form_update_bus_table_tour .bus-edit-select').each(function () {
        if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
        $(this).select2({ width: '100%', dropdownParent: $('#modalUpdateTrip') });
    });
</script>
