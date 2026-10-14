<?php

namespace App\Helper;

use Illuminate\Support\Facades\DB;

/**
 * Supplier invoices for one service (hotel, event, guide, transfer, restaurant, ...).
 * Invoices are linked to tour packages (invoices_tours.package_id), and a package points
 * at its service through tour_packages.type + tour_packages.reference.
 */
class SupplierInvoices
{
    public static function forService($serviceType, $serviceId)
    {
        $invoices = DB::table('supplier_invoices as si')
            ->join('invoices_tours as it', 'it.invoices_id', '=', 'si.id')
            ->join('tour_packages as tp', 'tp.id', '=', 'it.package_id')
            ->leftJoin('tours as t', 't.id', '=', 'it.invoices_tours_id')
            ->leftJoin('office_fees as o', 'o.id', '=', 'si.office_id')
            ->where('tp.type', $serviceType)
            ->where('tp.reference', $serviceId)
            ->whereNull('tp.deleted_at')
            ->select(
                'si.id',
                'it.id as link_id',
                'si.invoice_no',
                'si.dueDate as due_date',
                'si.receivedDate as received_date',
                'si.total_amount',
                't.name as tour_name',
                'tp.name as service_name',
                'o.office_name'
            )
            ->orderByDesc('si.id')
            ->get()
            ->unique('id')
            ->values();

        // Same rule as Invoices::status(): supplier payments recorded as transactions
        $paid = DB::table('transactions')
            ->whereIn('invoice_id', $invoices->pluck('id'))
            ->where('pay_to', 'Supplier')
            ->groupBy('invoice_id')
            ->pluck(DB::raw('SUM(amount)'), 'invoice_id');

        return $invoices->map(function ($invoice) use ($paid) {
            $sum = (float) ($paid[$invoice->id] ?? 0);
            $total = (float) $invoice->total_amount;
            $invoice->paid_amount = $sum;
            $invoice->status = $sum >= $total && $total > 0 ? 'paid' : ($sum > 0 ? 'partial' : 'open');
            $invoice->status_label = $invoice->status === 'paid' ? 'Paid' : 'You owe ' . number_format($total - $sum, 2);
            return $invoice;
        });
    }
}
