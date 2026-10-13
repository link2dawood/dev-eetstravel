<form id="form_update_bus_table_trip">
    <div class="d-flex flex-wrap align-items-center gap-4 mb-3">
        <label class="form-check trip_full mb-0">
            <input class="form-check-input" type="radio" name="trip_mode" value="0" checked>
            <span class="form-check-label">{!!trans('main.FullTrip')!!}</span>
        </label>
        <label class="form-check trip_day mb-0">
            <input class="form-check-input" type="radio" name="trip_mode" value="1">
            <span class="form-check-label">{!!trans('main.ForDayTrip')!!}</span>
        </label>
        <button type="button" onclick="if (confirm('Delete the selected trip days?')) deleteTrip()" class="btn btn-danger ms-auto delete_trip">
            <i class="fa fa-trash me-1"></i>{!!trans('main.Deletetrip')!!}
        </button>
    </div>

    <input type='hidden' name='_token' value='{{Session::token()}}'>
    <input type="hidden" name="bus_day_id" value="{{ $busDay->id }}">
    <input type="hidden" name="trip_id" value="{{ $busDay->trip_id }}">

    <div class="row g-3">
        <div class="col-md-6 trip_field">
            <label class="form-label" for="name_update_trip">{!!trans('main.NameTrip')!!}</label>
            {!! Form::text('name_update_trip', $busDay->name_trip,
            ['class' => 'form-control clear_input','id' => 'name_update_trip', 'required']) !!}
        </div>

        <div class="col-md-6">
            <label class="form-label" for="bus_status_update_trip">{!!trans('main.Status')!!}</label>
            <select id="bus_status_update_trip" name="bus_status_update_trip" class="form-select bus-edit-select">
                @foreach( $bus_statuses as $status)
                    <option {{ $status->id == $busDay->status_id ? 'selected' : '' }}
                            value="{{$status->id}}">{{$status->name}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="country_from">{!!trans('main.CountryFrom')!!}</label>
            {!! Form::select('country_begin',
                \App\Helper\Choices::getCountriesArray(),
                $busDay->getAttributes()['country_trip'],
                ['class' => 'form-select bus-edit-select', 'id' => 'country_from']) !!}
        </div>

        <div class="col-md-6">
            <label class="form-label" for="city_from">{!!trans('main.Cityfrom')!!}</label>
            <input id="city_from" name="city_begin" type="text" class="form-control"
                   value="{{$busDay->city_trip()}}">
            <input type="hidden" name="city_begin_code" id="city_code_from"
                   value="{!! \App\Helper\CitiesHelper::getCityById($busDay->getAttributes()['city_trip'])['code'] ?? '' !!}">
        </div>

        <div class="col-md-6">
            <label class="form-label" for="driver_trip_update">{!!trans('main.Drivers')!!}</label>
            @if(count($drivers) > 0)
                <select name="driver_trip_update[]" id="driver_trip_update" class="select2 js-state form-select bus-edit-select" multiple="multiple">
                    @foreach($drivers as $driver)
                        <option {{ $selected_drivers->contains('driver_id', $driver->id) ? 'selected' : '' }}
                                class="transfer_driver"
                                value="{{ $driver->id }}"
                        >{{ $driver->name }}</option>
                    @endforeach
                </select>
            @else
                <div class="form-control-plaintext text-muted">{!!trans('main.Transferwithoutdriver')!!}</div>
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label" for="bus_trip_update">{!!trans('main.Bus')!!}</label>
            @if(count($buses) > 0)
                <select id="bus_trip_update" name="bus_trip_update" class="form-select bus-edit-select" required>
                    @foreach($buses as $bus)
                        <option {{ $bus->id == $busDay->bus_id ? 'selected' : '' }}
                                value="{{$bus->id}}">{{$bus->name}} ({{$bus->bus_number}})</option>
                    @endforeach
                </select>
            @else
                <div class="form-control-plaintext text-muted">{!!trans('main.Transferwithoutbus')!!}</div>
            @endif
        </div>
    </div>
</form>

<script>
    $('#form_update_bus_table_trip .bus-edit-select').each(function () {
        if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
        $(this).select2({ width: '100%', dropdownParent: $('#modalUpdateTrip') });
    });

    $(document).find('#form_update_bus_table_trip').find('#country_from').on('change', function (e) {
        $(document).find('#form_update_bus_table_trip').find('#city_from').val('');
        $(document).find('#form_update_bus_table_trip').find('#city_code_from').val('');

        $(document).find('#form_update_bus_table_trip').find('#city_from').focus();
        initField();
    });
</script>

<script src="{{URL::asset('js/google_places.js')}}"></script>
