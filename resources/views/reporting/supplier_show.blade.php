@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Supplier report')
@section('content')
<div class="container-xl">
    <div class="page-header mb-3"><h2 class="page-title">{{ $supplier->name }} — Supplier report</h2></div>
    <a class="btn btn-secondary mb-3" href="{{ route('reporting.index') }}">Back to reports</a>
    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card card-body"><div class="text-muted">Invoice total</div><strong>{{ number_format($invoices->sum('total_amount'), 2) }}</strong></div></div>
        <div class="col-md-4"><div class="card card-body"><div class="text-muted">Paid</div><strong>{{ number_format($invoices->sum('paid_amount'), 2) }}</strong></div></div>
        <div class="col-md-4"><div class="card card-body"><div class="text-muted">Remaining</div><strong>{{ number_format($invoices->sum('total_amount') - $invoices->sum('paid_amount'), 2) }}</strong></div></div>
    </div>
    <div class="card mb-3"><div class="card-header"><h3 class="card-title">Invoices received by month</h3></div><div class="table-responsive"><table class="table"><thead><tr><th>Month</th><th>Amount</th></tr></thead><tbody>@foreach($months as $month => $amount)<tr><td>{{ $month }}</td><td>{{ number_format($amount, 2) }}</td></tr>@endforeach</tbody></table></div></div>
    <div class="card"><div class="card-header"><h3 class="card-title">Supplier invoices</h3></div><div class="card-body">@include('component.supplier_invoices', ['tableId' => 'supplier-report-invoices'])</div></div>
</div>
@endsection
