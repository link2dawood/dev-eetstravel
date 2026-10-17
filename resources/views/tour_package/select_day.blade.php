@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'New Tour Package')
@section('content')
<div class="card">
    <div class="card-header"><h1 class="card-title">New Tour Package</h1></div>
    <div class="card-body">
        <p>Select the tour and day for this package.</p>
        @if($days->isEmpty())
            <div class="alert alert-info" role="status">No tour days are available. Add a day to a tour before creating a package.</div>
        @else
            <form method="GET" action="{{ url('tour_package/create') }}">
                <label class="form-label" for="tour_day_id">Tour and day</label>
                <select class="form-select mb-3" name="tour_day_id" id="tour_day_id" required>
                    <option value="">Select a tour day</option>
                    @foreach($days as $day)
                        <option value="{{ $day->id }}">{{ $day->getRelation('tour')->name }} (#{{ $day->tour }}) — {{ $day->date }} (day #{{ $day->id }})</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Continue</button>
            </form>
        @endif
        <a class="btn btn-outline-secondary mt-3" href="{{ route('tour_package.index') }}">Cancel</a>
    </div>
</div>
@endsection
