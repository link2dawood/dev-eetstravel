@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Office Invoice Details')

@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    title="Invoice items"
    description="Office invoice detail"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Office invoices'],
        ['label' => 'Detail'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('office_invoices_pdf_export', ['id' => $officeinvoice_dataId, 'pdf_type' => 'short']) }}" variant="secondary" icon="file-text">
            Invoice PDF
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(session('message_buses'))
    <div class="mb-4 flex items-start gap-3 rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700">
        <x-ui.icon name="info" class="mt-0.5 text-info-600" />
        <div class="flex-1">{{ session('message_buses') }}</div>
    </div>
@endif

<div class="rounded border border-slate-200 bg-white">
    <div class="overflow-x-auto px-4 py-4">
        <input id="offices_id" type="hidden" name="offices_id" value="{{ $officeinvoice_dataId }}">
        <table id="officesinvoicedetail-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Items</th>
                    <th class="px-3 py-2">Date</th>
                    <th class="px-3 py-2">Item code</th>
                    <th class="px-3 py-2">Amount</th>
                    <th class="px-3 py-2">Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Items</th>
                    <th class="px-3 py-2">Date</th>
                    <th class="px-3 py-2">Item code</th>
                    <th class="px-3 py-2">Amount</th>
                    <th class="px-3 py-2">Action</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let permission = $('#permission').attr('data-permission');
    let classNameStatus = permission ? 'officeinvoiceedit-status' : '';
    let office_id = $("#offices_id").val();
    let table = $('#officesinvoicedetail-table').DataTable({
        dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'csv',      title: 'Tours List',         exportOptions: { columns: ':not(.actions-button)' } },
            { extend: 'excel',    title: 'Tours List',         exportOptions: { columns: ':not(.actions-button)' } },
            { extend: 'pdfHtml5', title: 'Office Invoice List', exportOptions: { columns: ':not(.actions-button)' } },
        ],
        language: { search: "Global Search :" },
        processing: true,
        serverSide: true,
        pageLength: 50,
        ajax: { url: `{{ url('office-invoices-details/api/data/${office_id}') }}` },
        columns: [
            { data: 'officeInvoiceId',       name: 'officeInvoiceId' },
            { data: 'officeinvoice_item',    name: 'officeinvoice_item',    className: 'officeinvoiceedit-name' },
            { data: 'officeinvoice_date',    name: 'officeinvoice_date',    className: 'officeinvoiceedit-name' },
            { data: 'officeinvoice_code',    name: 'officeinvoice_code',    className: 'officeinvoiceedit-name' },
            { data: 'officeinvoice_amount',  name: 'officeinvoice_amount',  className: 'officeinvoiceedit-name' },
            { data: 'action',                name: 'action', searchable: false, sorting: false, orderable: false },
        ],
        columnDefs: [{
            targets: 4,
            createdCell: function (td, cellData, rowData) {
                var url = "{{ route('tour.update', ['tour' => '__ID__']) }}".replace('__ID__', rowData.id);
                $(td).attr('data-status-link', url);
            }
        }],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                if (column.footer().className == 'select_search') {
                    var select = $('<select class="form-control"><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    column.data().unique().sort().each(function (d) {
                        select.append('<option value="' + d + '">' + d + '</option>');
                    });
                }
            });
        }
    });
    $('#officesinvoicedetail-table tfoot th').each(function () {
        if (this.className !== 'not') {
            let title = $(this).text();
            $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        } else {
            $(this).html('<span> </span>');
        }
    });
    table.columns().every(function () {
        let that = this;
        $('input', this.footer()).on('keyup change', function () {
            if (that.search() !== this.value) that.search(this.value).draw();
        });
    });
    $('#officesinvoicedetail-table tfoot th').appendTo('#officesinvoicedetail-table thead');
});
</script>
@endpush
