@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Tour Package — Missing Tour')
@section('content')
<div class="card">
    <div class="card-header"><h1 class="card-title">Tour Package #{{ $tourPackage->id }}</h1></div>
    <div class="card-body">
        <div class="alert alert-warning" role="alert" id="package-missing-tour">
            This package has no active parent tour. Its stored details are available below, but editing is disabled until its tour association is restored.
        </div>
        <dl class="row">
            <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $tourPackage->name ?: '—' }}</dd>
            <dt class="col-sm-3">Description</dt><dd class="col-sm-9">{{ $tourPackage->description ?: '—' }}</dd>
            <dt class="col-sm-3">From</dt><dd class="col-sm-9">{{ $tourPackage->time_from ?: '—' }}</dd>
            <dt class="col-sm-3">To</dt><dd class="col-sm-9">{{ $tourPackage->time_to ?: '—' }}</dd>
            <dt class="col-sm-3">Pax</dt><dd class="col-sm-9">{{ $tourPackage->pax ?? '—' }}</dd>
            <dt class="col-sm-3">Stored amount</dt><dd class="col-sm-9">{{ $tourPackage->total_amount ?? '—' }}</dd>
        </dl>
        <a class="btn btn-primary" href="{{ route('tour_package.index') }}">Back to packages</a>
    </div>
</div>
@endsection
