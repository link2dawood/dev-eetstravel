<div class="form-group">
    <label for="driver_transfer">{!!trans('main.Drivers')!!}</label>
    @if(count($drivers) > 0)
        <select name="driver_transfer" id="driver_transfer" class="js-state form-control select2" multiple="multiple" required>
            @foreach($drivers as $driver)
                <?php $check = false; ?>
                @foreach($selected_drivers as $item)
                    @if($item->driver_id == $driver->id)
                        <?php $check = true; ?>
                    @endif
                @endforeach
                <option {{ $check ? 'selected' : '' }}
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


<div class="form-group">
    <label for="bus_transfer">{!!trans('main.Buses')!!}</label>
    @if(count($buses) > 0)
        <select name="bus_transfer" id="bus_transfer" class="js-state form-control select2" multiple="multiple" required>
            @foreach($buses as $bus)
                <?php $check = false; ?>
                @foreach($selected_buses as $item)
                    @if($item->bus_id == $bus->id)
                        <?php $check = true; ?>
                    @endif
                @endforeach
                <option {{ $check ? 'selected' : '' }}
                        class="transfer_bus"
                        value="{{ $bus->id }}"
                >{{ $bus->name }}</option>
            @endforeach
        </select>
        <script>$('#bus_transfer').select2();</script>
    @else
        <h5>{!!trans('main.Transferwithoutbus')!!}</h5>
    @endif
</div>


<span id="page" data-info="tour"></span>


