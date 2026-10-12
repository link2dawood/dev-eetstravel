{{-- Supplier invoices of one service (see App\Helper\SupplierInvoices). Expects $invoices and $tableId. --}}
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <div class="input-icon" style="max-width: 320px;">
        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
        <input type="text" class="form-control" placeholder="Search invoices..."
               oninput="(function (q, t) { q = q.toLowerCase(); t.querySelectorAll('tbody tr[data-invoice]').forEach(function (r) { r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none'; }); })(this.value, document.getElementById('{{ $tableId }}'))">
    </div>
    <div class="btn-list">
        @if(Auth::user()->can('invoices.create'))
            <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i>New</a>
        @endif
    </div>
</div>
<div class="table-responsive">
    <table id="{{ $tableId }}" class="table table-vcenter card-table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Invoice No</th>
                <th>Due Date</th>
                <th>Received Date</th>
                <th>Tour</th>
                <th>Service</th>
                <th>Office</th>
                <th class="text-end">Total</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr data-invoice>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->invoice_no ?: '-' }}</td>
                    <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : '-' }}</td>
                    <td>{{ $invoice->received_date ? \Carbon\Carbon::parse($invoice->received_date)->format('Y-m-d') : '-' }}</td>
                    <td>{{ $invoice->tour_name ?: '-' }}</td>
                    <td>{{ $invoice->service_name ?: '-' }}</td>
                    <td>{{ $invoice->office_name ?: '-' }}</td>
                    <td class="text-end">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                    <td><span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'partial' ? 'bg-warning' : 'bg-danger') }} text-white">{{ $invoice->status_label }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-warning btn-sm btn-icon" title="View"><i class="ti ti-eye"></i></a>
                        @if(Auth::user()->can('invoices.edit'))
                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-primary btn-sm btn-icon" title="Edit"><i class="ti ti-edit"></i></a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No invoices for this supplier yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
