<div class="form-group">
    <label for="overview"><b>{!!trans('main.Bus')!!}</b></label>
@if(count($buses) > 0)
        <select id="buses" name="buses" class="form-control">
    @foreach($buses as $bus)
            <option {{ $bus_day->bus_id == $bus->id ? 'selected' : '' }} value="{{$bus->id}}">{{$bus->name}}</option>
        @endforeach
    </select>
    @else
        <h5>No Buses</h5>
    @endif
</div>

<div class="form-group">
    <label for="drivers"><b>{!!trans('main.Driver')!!}</b></label>
    @if(count($drivers) > 0)
    <select id="drivers" name="drivers" class="form-control">
        @foreach($drivers as $driver)
            <option {{ $bus_day->driver_id == $driver->id ? 'selected' : '' }} value="{{$driver->id}}">{{$driver->name}}</option>
        @endforeach
    </select>
    @else
        <h5>{!!trans('main.NoDrivers')!!}</h5>
    @endif
</div>

<span id="page" data-info="trip"></span>