@foreach($buses as $bus)
    <option value="{{ $bus->id }}">{{ $bus->name }}</option>
@endforeach