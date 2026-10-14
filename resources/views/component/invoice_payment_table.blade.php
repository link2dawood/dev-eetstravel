@php
    $payments = \App\Transaction::where('invoice_id', $invoice->id)->where('pay_to', $payTo)->orderBy('date')->orderBy('id')->get();
    $invoiceTotal = $payTo === 'Client' ? $invoice->amount_receiveable : $invoice->total_amount;
    $balance = round($invoiceTotal - $payments->sum('amount'), 2);
@endphp
<p class="text-muted">Paid: {{ number_format($payments->sum('amount'), 2) }} · Remaining: {{ number_format($balance, 2) }}</p>
<table id="service-transactions-table" class="table table-striped table-bordered">
    <thead><tr><th>ID</th><th>Date</th><th>Transaction No</th><th>Method</th><th>Amount</th></tr></thead>
    <tbody>
    @forelse($payments as $payment)
        <tr><td>{{ $payment->id }}</td><td>{{ $payment->date }}</td><td>{{ $payment->trans_no }}</td><td>{{ $payment->payment_method }}</td><td>{{ number_format($payment->amount, 2) }}</td></tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">No payments recorded</td></tr>
    @endforelse
    </tbody>
</table>
