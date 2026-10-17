@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Initialize front sheet')
@section('content')
<div class="card">
    <div class="card-header"><h1 class="card-title">Front sheet — {{ $quotation->name }}</h1></div>
    <div class="card-body">
        @include('comparison.initialization-notice')
        <a href="{{ route('tour.show', ['tour' => $quotation->tour_id]) }}" class="btn btn-secondary">Back to tour</a>
    </div>
</div>
@endsection
