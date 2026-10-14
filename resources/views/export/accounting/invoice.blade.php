<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><title>Invoice {{ $invoice->invoice_no }}</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#182433}table{border-collapse:collapse;width:100%;margin:16px 0}th,td{border:1px solid #ccd2d8;padding:7px;text-align:left}th{background:#f1f5f9}.amount{text-align:right}h1{font-size:22px}p{margin:5px 0}</style></head><body>
<h1>Client invoice {{ $invoice->invoice_no }}</h1>
<p>{{ $office->office_name }}</p><p>{{ $office->office_address }}</p>
<p>Date: {{ $invoice->date }} · Currency: {{ $invoice->currency }}</p>
<p>Client: {{ $client->name }}</p><p>{{ $client->invoice_address ?: $client->address }}</p>
<p>Tour: {{ $tour->name }}</p>
<table><thead><tr><th>Item</th><th>Quantity</th><th>Unit price</th><th>VAT %</th><th>Total incl. VAT</th></tr></thead><tbody>
@foreach($items as $item)<tr><td>{{ $item->item_name }}</td><td>{{ $item->quantity }}</td><td class="amount">{{ number_format($item->amount, 2, '.', '') }}</td><td>{{ number_format($item->vat * 100, 2, '.', '') }}</td><td class="amount">{{ number_format($item->total_amount, 2, '.', '') }}</td></tr>@endforeach
@if($invoice->extra_cost)<tr><td colspan="4">Extra cost</td><td class="amount">{{ number_format($invoice->extra_cost, 2, '.', '') }}</td></tr>@endif
<tr><th colspan="4">Invoice total</th><th class="amount">{{ number_format($invoice->amount_receiveable, 2, '.', '') }}</th></tr>
<tr><td colspan="4">Paid</td><td class="amount">{{ number_format($payments->sum('amount'), 2, '.', '') }}</td></tr>
<tr><th colspan="4">Remaining balance</th><th class="amount">{{ number_format($invoice->amount_receiveable - $payments->sum('amount'), 2, '.', '') }}</th></tr>
</tbody></table>
@if($payments->isNotEmpty())<h2>Payments</h2><table><thead><tr><th>Date</th><th>Reference</th><th>Method</th><th>Amount</th></tr></thead><tbody>@foreach($payments as $payment)<tr><td>{{ $payment->date }}</td><td>{{ $payment->trans_no }}</td><td>{{ $payment->payment_method }}</td><td class="amount">{{ number_format($payment->amount, 2, '.', '') }}</td></tr>@endforeach</tbody></table>@endif
@if($invoice->note)<p>Note: {{ $invoice->note }}</p>@endif
<p>Bank: {{ $office->bank_name }} · Account: {{ $office->account_no }} · SWIFT: {{ $office->swift_code }}</p>
</body></html>
