<form id="form_update_bus_table_trip">
    <div class="form-group">
        <div class="col-lg-12">
            <div class="col-lg-6">
                <label class="trip_full">
                    <input type="radio" name="trip_mode"  value="0" checked>
                    <span style="font-size: 1.1em;">{!!trans('main.FullTrip')!!}</span>
                </label>
            </div>
            <div class="col-lg-6">
                <label class="trip_day">
                    <input type="radio" name="trip_mode" value="1">
                    <span style="font-size: 1.1em;">{!!trans('main.ForDayTrip')!!}</span>
                </label>
            </div>
        </div>
        <div class="col-lg-12">
            <div style="overflow: hidden; display: block; margin-bottom: 10px">
                <button type="button" onclick="deleteTrip()" class="btn btn-danger pull-right delete_trip">{!!trans('main.Deletetrip')!!}</button>
            </div>
        </div>
    </div>


    <input type='hidden' name='_token' value='{{Session::token()}}'>
    <input type="hidden" name="bus_day_id" value="{{ $busDay->id }}">
    <input type="hidden" name="trip_id" value="{{ $busDay->trip_id }}">

    <div class="form-group trip_field">
        <label for="name_update_trip">{!!trans('main.NameTrip')!!}</label>
        {!! Form::text('name_update_trip', $busDay->name_trip,
        ['class' => 'form-control clear_input','id' => 'name_update_trip', 'required']) !!}
    </div>

    <div class="form-group">
        <label for="bus_status_update_trip"><b>{!!trans('main.Status')!!}</b></label>
        <select id="bus_status_update_trip" name="bus_status_update_trip" class="form-control" >
            @foreach( $bus_statuses as $status)
                <option {{ $status->id == $busDay->status_id ? 'selected' : '' }}
                        value="{{$status->id}}">{{$status->name}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="country_begin">{!!trans('main.CountryFrom')!!}</label>
        {!! Form::select('country_begin',
            \App\Helper\Choices::getCountriesArray(),
            $busDay->getAttributes()['country_trip'],
            ['class' => 'form-control', 'id' => 'country_from']) !!}
    </div>

    <div class="form-group">
        <label for="city_from">{!!trans('main.Cityfrom')!!}</label>
        <input id="city_from" name="city_begin" type="text" class="form-control"
               value="{{$busDay->city_trip()}}">
        <input type="hidden" name="city_begin_code" id="city_code_from"
               value="{!! \App\Helper\CitiesHelper::getCityById($busDay->getAttributes()['city_trip'])['code']!!}">
    </div>

    <div style="overflow: hidden; display: block">
        <div class="col-lg-6" style="padding-left: 0">
            <div class="form-group">
                <label for="driver_trip_update">{!!trans('main.Drivers')!!}</label>
                @if(count($drivers) > 0)
                    <select name="driver_trip_update[]" id="driver_trip_update" class="select2 js-state form-control" multiple="multiple" required>
                        @foreach($drivers as $driver)
                            <?php $check = false; ?>
                            @if(count($selected_drivers) > 0)
                                @foreach($selected_drivers as $selected_driver)
                                    {{ $selected_driver->driver_id == $driver->id ? $check = true : '' }}
                                @endforeach
                            @endif
                            <option {{ $check ? 'selected' : '' }}
                                    class="transfer_driver"
                                    value="{{ $driver->id }}"
                            >{{ $driver->name }}</option>
                        @endforeach
                    </select>
                    <script>$('#driver_trip_update').select2();</script>
                @else
                    <h5>{!!trans('main.Transferwithoutdriver')!!}</h5>
                @endif
            </div>
        </div>
        <div class="col-lg-6" style="padding-right: 0">
            <div class="form-group">
                <label for="bus_trip_update"><b>{!!trans('main.Bus')!!}</b></label>
                @if(count($buses) > 0)
                    <select id="bus_trip_update" name="bus_trip_update" class="form-control" required>
                        @foreach($buses as $bus)
                            <option {{ $bus->id == $busDay->bus_id ? 'selected' : '' }}
                                    value="{{$bus->id}}">{{$bus->name}}</option>
                        @endforeach
                    </select>
                @else
                    <h5>{!!trans('main.Transferwithoutbus')!!}</h5>
                @endif
            </div>
        </div>
    </div>
</form>

<script>
    $(document).find('#form_update_bus_table_trip').find('#country_from').on('change', function (e) {
        $(document).find('#form_update_bus_table_trip').find('#city_from').val('');
        $(document).find('#form_update_bus_table_trip').find('#city_code_from').val('');

        $(document).find('#form_update_bus_table_trip').find('#city_from').focus();
        initField();
    });


</script>

<script src="{{URL::asset('js/google_places.js')}}"></script>
