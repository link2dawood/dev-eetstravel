@extends('scaffold-interface.layouts.tabler-app')
@section('title', $heading)
@section('content')
<div class="card">
    <div class="card-header"><h1 class="card-title">{{ $heading }}</h1></div>
    <div class="card-body">
        <dl class="row" id="resource-details">
            @foreach($fields as $label => $value)
                <dt class="col-sm-3">{{ $label }}</dt>
                <dd class="col-sm-9 text-break">{{ $value === null || $value === '' ? '—' : $value }}</dd>
            @endforeach
        </dl>
        <a class="btn btn-primary" href="{{ route($listRoute) }}">Back to list</a>
    </div>
</div>
@endsection
