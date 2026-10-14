@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Office invoice')
@section('content')
<div class="container-xl">
    <div class="page-header mb-3"><h2 class="page-title">Office invoice {{ $invoice->invoice_no }}</h2></div>
    <div class="btn-list mb-3">
        <a class="btn btn-secondary" href="{{ route('office.show', $invoice->from_office) }}">Back to office</a>
        <a class="btn btn-primary" href="{{ route('office_invoices_pdf_export', ['id' => $officeinvoice_dataId, 'pdf_type' => 'short']) }}">Download PDF</a>
        @can('office.edit')<a class="btn btn-warning" href="{{ route('officeInvoices.edit', $officeinvoice_dataId) }}">Edit</a>@endcan
    </div>
    <div class="card"><div class="card-body">
        <p>Date: {{ $invoice->date }}</p>
        <div class="table-responsive"><table class="table table-striped">
            <thead><tr><th>ID</th><th>Item</th><th>Date</th><th>Code</th><th>Description</th><th>Amount</th></tr></thead>
            <tbody>@forelse($items as $item)
                <tr><td>{{ $item->officeInvoiceId }}</td><td>{{ $item->officeinvoice_item }}</td><td>{{ $item->officeinvoice_date }}</td><td>{{ $item->officeinvoice_code }}</td><td>{{ $item->des }}</td><td>{{ number_format($item->officeinvoice_amount, 2) }}</td></tr>
            @empty<tr><td colspan="6">No invoice items</td></tr>@endforelse</tbody>
            <tfoot><tr><th colspan="5">Total</th><td>{{ number_format($items->sum('officeinvoice_amount'), 2) }}</td></tr></tfoot>
        </table></div>
    </div></div>
</div>
@endsection
