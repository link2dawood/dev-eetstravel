@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Quotation Details')
@section('content')
<div class="card">
    <div class="card-header"><h1 class="card-title">Quotation #{{ $quotation->id }}</h1></div>
    <div class="card-body">
        <dl class="row" id="quotation-details">
            <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $quotation->name ?: '—' }}</dd>
            <dt class="col-sm-3">Tour</dt><dd class="col-sm-9">{{ $quotation->tour ? $quotation->tour->name : 'Tour unavailable' }}</dd>
            <dt class="col-sm-3">Note</dt><dd class="col-sm-9 text-break">{{ $quotation->note ?: '—' }}</dd>
            <dt class="col-sm-3">Created</dt><dd class="col-sm-9">{{ $quotation->created_at ?: '—' }}</dd>
        </dl>
        @if($quotation->tour)
            <a class="btn btn-primary" href="{{ route('quotation.edit', ['quotation' => $quotation->id]) }}">Open quotation</a>
        @else
            <p class="text-muted">The linked tour is unavailable. This quotation is shown as a read-only summary.</p>
        @endif
        <a class="btn btn-outline-secondary" href="{{ route('quotation.index') }}">Back to quotations</a>
    </div>
</div>
@endsection
