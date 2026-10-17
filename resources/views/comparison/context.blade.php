@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Comparisons')
@section('content')
<div class="card">
    <div class="card-header"><h1 class="card-title">Comparisons</h1></div>
    <div class="card-body" id="comparison-context">
        <p>A comparison front sheet belongs to a quotation. Standalone comparison creation is not available.</p>
        <p>Select a quotation from the quotation list to work with its front sheet.</p>
        <a class="btn btn-primary" href="{{ route('quotation.index') }}">Open quotations</a>
    </div>
</div>
@endsection
