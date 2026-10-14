@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit payment')
@section('content')
<div class="container-xl">
    <h2 class="page-title mb-3">Edit payment {{ $transaction->trans_no }}</h2>
    @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('transaction.update', $transaction->id) }}" class="card card-body">
        @csrf @method('PUT')
        <div class="row g-3 mb-3">
            <div class="col-md-4"><label for="payment-date" class="form-label">Date</label><input id="payment-date" name="date" type="date" value="{{ old('date', $transaction->date) }}" class="form-control" required></div>
            <div class="col-md-4"><label for="payment-amount" class="form-label">Amount</label><input id="payment-amount" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount', $transaction->amount) }}" class="form-control" required></div>
            <div class="col-md-4"><label for="payment-method" class="form-label">Payment method</label><input id="payment-method" name="payment_method" value="{{ old('payment_method', $transaction->payment_method) }}" class="form-control" required maxlength="191"></div>
        </div>
        <div class="btn-list"><button type="submit" class="btn btn-primary">Save</button><a class="btn btn-secondary" href="{{ route('transaction.show', $transaction->id) }}">Cancel</a></div>
    </form>
</div>
@endsection
