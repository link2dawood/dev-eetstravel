<?php

namespace App\Services;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvoicePayments
{
    public static function rows(Request $request, bool $required = false): array
    {
        $request->validate([
            'paid_amount' => ($required ? 'required' : 'nullable') . '|array|min:1',
            'paid_amount.*' => 'required|numeric|min:0.01|max:999999999.99',
            'paymentdate' => 'required_with:paid_amount|array',
            'paymentdate.*' => 'required|date_format:Y-m-d',
            'payment_method' => 'required_with:paid_amount|array',
            'payment_method.*' => 'required|string|max:191',
        ]);
        $amounts = $request->input('paid_amount', []) ?? [];
        $dates = $request->input('paymentdate', []) ?? [];
        $methods = $request->input('payment_method', []) ?? [];
        if (array_keys($amounts) !== array_keys($dates) || array_keys($amounts) !== array_keys($methods)) {
            throw ValidationException::withMessages(['paid_amount' => 'Every payment needs a date, amount and payment method.']);
        }
        $rows = [];
        foreach ($amounts as $key => $amount) {
            $rows[] = ['date' => $dates[$key], 'amount' => round((float) $amount, 2), 'payment_method' => $methods[$key]];
        }
        return $rows;
    }

    public static function ensureWithinBalance(array $rows, $total, $alreadyPaid = 0): void
    {
        $cents = array_sum(array_map(fn ($row) => (int) round($row['amount'] * 100), $rows));
        if ($cents + (int) round($alreadyPaid * 100) > (int) round($total * 100)) {
            throw ValidationException::withMessages(['paid_amount' => 'Payments cannot exceed the invoice balance.']);
        }
    }

    public static function save(array $rows, int $invoiceId, string $payTo): void
    {
        foreach ($rows as $row) {
            Transaction::create($row + ['trans_no' => 'TXN-' . \Illuminate\Support\Str::uuid(), 'invoice_id' => $invoiceId, 'pay_to' => $payTo]);
        }
    }
}
