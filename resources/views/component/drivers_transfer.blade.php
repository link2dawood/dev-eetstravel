@foreach($drivers as $driver)
    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
@endforeach