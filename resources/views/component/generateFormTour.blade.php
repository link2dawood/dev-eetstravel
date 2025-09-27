<form id="form_update_bus_table_tour">
    <div class="form-group">
        <div class="col-lg-12">
            <div class="col-lg-6">
                <label class="trip_full">
                    <input type="radio" name="tour_mode"  value="0" checked>
                    <span style="font-size: 1.1em;">{!!trans('main.FullTour')!!}</span>
                </label>
            </div>
            <div class="col-lg-6">
                <label class="trip_day">
                    <input type="radio" name="tour_mode" value="1">
                    <span style="font-size: 1.1em;">{!!trans('main.ForDayTour')!!}</span>
                </label>
            </div>
        </div>
    </div>

    <input type='hidden' name='_token' value='{{Session::token()}}'>
    <input type="hidden" name="bus_day_id" value="{{ $busDay->id }}">
    <input type="hidden" name="tour_package_id_bus_day" value="{{ $busDay->tour_package_id }}">

    <div class="form-group">
        <label for="bus_status_update_tour"><b>{!!trans('main.Status')!!}</b></label>
        <select id="bus_status_update_tour" name="bus_status_update_tour" class="form-control" >
            @foreach( $bus_statuses as $status)
                <option {{ $status->id == $busDay->status_id ? 'selected' : '' }}
                        value="{{$status->id}}">{{$status->name}}
                </option>
            @endforeach
        </select>
    </div>

    <div style="overflow: hidden; display: block">
        <div class="col-lg-6" style="padding-left: 0">
            <div class="form-group">
                <label for="drivers_tour_update">{!!trans('main.Drivers')!!}</label>
                @if(count($drivers) > 0)
                    <select name="drivers_tour_update[]" id="drivers_tour_update" class="select2 js-state form-control" multiple="multiple" required>
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
                    <script>$('#drivers_tour_update').select2();</script>
                @else
                    <h5>{!!trans('main.Transferwithoutdrivers')!!}</h5>
                @endif
            </div>
        </div>
        <div class="col-lg-6" style="padding-right: 0">
            <div class="form-group">
                <label for="buses_tour_update"><b>{!!trans('main.Bus')!!}</b></label>
                @if(count($buses) > 0)
                    <select name="bus_tour_update" id="buses_tour_update" class="form-control" required>
                        @foreach($buses as $bus)
                            <option {{ $busDay->bus_id == $bus->id ? 'selected' : '' }}
                                    class="transfer_driver"
                                    value="{{ $bus->id }}"
                            >{{ $bus->name }}</option>
                        @endforeach
                    </select>
                @else
                    <h5>{!!trans('main.Transferwithoutbus')!!}</h5>
                @endif
            </div>
        </div>
    </div>
</form>
