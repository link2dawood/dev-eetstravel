<div style="overflow: hidden; display: block">
    <div class="col-lg-6" style="padding-left: 0">
        <div class="form-group">
            <label for="driver_transfer">{!!trans('main.Drivers')!!}</label>
            @if(count($drivers) > 0)
                <select name="driver_transfer" id="driver_transfer" class="select2 js-state form-control" multiple="multiple" >
                    @foreach($drivers as $driver)
                        <option 
                                class="transfer_driver"
                                value="{{ $driver->id }}"
                        >{{ $driver->name }}</option>
                    @endforeach
                </select>
                <script>$('#driver_transfer').select2();</script>
            @else
                <h5>{!!trans('main.Transferwithoutdriver')!!}</h5>
            @endif
        </div>
    </div>
    <div class="col-lg-6" style="padding-right: 0">
        <div class="form-group">
            <label for="bus_transfer">{!!trans('main.Buses')!!}</label>
            @if(count($buses) > 0)
                <select name="bus_transfer" id="bus_transfer" class="form-control" >
					<option value="" selected></option>
                    @foreach($buses as $bus)
                        <option
                                class="transfer_bus"
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

