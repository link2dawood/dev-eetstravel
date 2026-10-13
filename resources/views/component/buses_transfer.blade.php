@foreach($buses as $bus)
    <option value="{{ $bus->id }}">{{ $bus->name }}@if($bus->bus_number) ({{ $bus->bus_number }})@endif</option>
@endforeach