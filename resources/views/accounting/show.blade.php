@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show Client Invoice')
@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    :title="$transactions->invoice_no ? ('Invoice ' . $transactions->invoice_no) : ('Invoice #' . $transactions->id)"
    description="Client invoice details and payments received."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Client Invoices', 'href' => route('accounting.index')],
        ['label' => $transactions->invoice_no ?: ('#' . $transactions->id)],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {!! trans('main.Back') !!}
        </x-ui.button>
        <x-ui.button as="a" href="{{ route('accounting_pdf_export', ['id' => $transactions->id, 'pdf_type' => 'short']) }}" variant="secondary" icon="file-text">
            PDF
        </x-ui.button>
        <x-ui.button as="a" href="{{ route('accounting_excel_export', ['id' => $transactions->id]) }}" variant="secondary" icon="file-spreadsheet">
            Excel
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<input id="invoice_id" type="hidden" name="invoice_id" value="{{ $transactions->id }}">

@php $isPaid = trim($transactions->status($transactions) ?? '') === 'Paid'; @endphp

{{-- ============================================================ --}}
{{-- Details --}}
{{-- ============================================================ --}}
<div class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
        <x-ui.icon name="receipt" size="sm" class="text-slate-400" />
        <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Info') !!}</h2>
    </div>
    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Invoice No') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{!! $transactions->invoice_no ?: '—' !!}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Date') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{!! $transactions->date ?: '—' !!}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Status') !!}</dt>
            <dd class="mt-0.5">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $isPaid ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700' }}">
                    {!! $transactions->status($transactions) !!}
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Tour Name') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{!! $tour->name ?? '—' !!}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Client Name') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{!! $transactions->client->name ?? '—' !!}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Amount Receiveable') !!}</dt>
            <dd class="mt-0.5 font-medium text-slate-900">{!! $transactions->amount_receiveable ?? '—' !!}</dd>
        </div>
    </dl>
</div>

{{-- ============================================================ --}}
{{-- Payments (server-side DataTable — JS preserved below) --}}
{{-- ============================================================ --}}
<div class="mt-6 rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <x-ui.icon name="credit-card" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Paid To Payment Amount</h2>
        </div>
        <div id="payment_create">
            @if(Auth::user()->can('transactions.create'))
                <x-ui.button as="a" href="{{ route('add__invoice_payment', $transactions->id) }}" icon="plus" size="sm">Add payment</x-ui.button>
            @endif
        </div>
    </div>
    <div class="px-4 py-4">
        <div class="overflow-x-auto">
            <table id="service-transactions-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff; width:100%;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">Id</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Transaction No</th>
                        <th class="px-4 py-3">Invoice No</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Unallocated</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Transaction No</th>
                        <th>Invoice No</th>
                        <th>Amount</th>
                        <th>Unallocated</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- Comments — anchor IDs preserved for comment.js --}}
{{-- ============================================================ --}}
<div class="mt-6 rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
        <x-ui.icon name="message-circle" size="sm" class="text-slate-400" />
        <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Comments') !!}</h2>
    </div>
    <div class="px-4 py-4">
        <div class="max-h-80 overflow-y-auto">
            <div id="show_comments"></div>
        </div>
    </div>
    <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 rounded-b">
        <form method='POST' action='{{ route('comment.store') }}' enctype="multipart/form-data" id="form_comment" class="space-y-3">
            <div>
                <span id="author_name" class="hidden mb-2 inline-flex items-center gap-2 rounded bg-primary-50 px-2 py-1 text-xs text-primary-700">
                    Replying to <span id="name" class="font-medium"></span>
                    <a href="#" id="reply_close" class="text-primary-700/70 hover:text-primary-900"><x-ui.icon name="x" size="xs" /></a>
                </span>
                <textarea
                    class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                    id="content" name="content"
                    placeholder="Add a comment — Ctrl + Enter to post"
                    rows="3"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Files') !!}</label>
                @component('component.file_upload_field')@endcomponent
            </div>
            <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
            <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $transactions->id }}">
            <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['client_invoices'] }}">

            <div class="flex">
                <button type="submit" id="btn_send_comment" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                    <x-ui.icon name="send" size="sm" />
                    {!! trans('main.Send') !!}
                </button>
            </div>
        </form>
    </div>
</div>

<span id="services_name" data-service-name='accounting' data-history-route="{{ route('services_history', ['id' => $transactions->id]) }}"></span>
@endsection

@push('styles')
<style>
    #form_comment .file-input .btn-primary { background-color:#0d9488; border-color:#0d9488; color:#fff; }
    #form_comment .file-input .btn-primary:hover { background-color:#0f766e; border-color:#0f766e; }
    #form_comment .file-input .btn-default { background-color:#fff; border-color:#cbd5e1; color:#475569; }
    #form_comment .file-input .btn-default:hover { background-color:#f8fafc; }
    #form_comment .file-caption, #form_comment .file-input input.form-control,
    #form_comment .file-caption .form-control { border-color:#cbd5e1; border-radius:0.375rem; font-size:0.875rem; }
</style>
@endpush

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection

@push('scripts')
	<script type="text/javascript" src="{{asset('js/jspdf.min.js')}}"></script>
	 <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
<script>

  $(document).ready(function() {
	  let invoice_id = $("#invoice_id").val();
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#service-transactions-table').DataTable({
            dom: "<'row'<'col-sm-4'l><'col-sm-4'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Payments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Payments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Payments List',
                    exportOptions: {
                        columns: ':not(.actions-button)',
                    },
                },
            ],
            language: {
                search: "Global Search :"
            },
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: "/accountingServiceTransaction/api/data/1/"+ invoice_id,
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
				{
                    data: 'date',
                    name: 'date',
                    className: 'touredit-name'
                },

					  {
                    data: 'trans_no',
                    name: 'trans_no',
                    className: 'touredit-name'
                },
					  {
                    data: 'invoice_no',
                    name: 'invoice_no',
                    className: 'touredit-name'
                },
				 {
                    data: 'amount',
                    name: 'amount',
                    className: 'touredit-name'
                },
                {
                    data: 'unallocated',
                    name: 'unallocated',
                    className: 'touredit-name'
                },

            ],
            'columnDefs': [{
                'targets': 5,
                'createdCell': function(td, cellData, rowData, row, col) {
                    var url = "{{ route('tour.update', ['tour' => '__ID__']) }}".replace('__ID__', rowData.id);
                    $(td).attr('data-status-link', url);
                }
            }],
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    if (column.footer().className == 'select_search') {
                        var select = $('<select class="form-control"><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>')
                        });
                    }
                });
            }
        });
        $('#service-transactions-table tfoot th').each(function() {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
            } else {
                $(this).html('<span> </span>');
            }
        });
        table.columns().every(function() {
            let that = this;

            $('input', this.footer()).on('keyup change', function() {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#service-transactions-table tfoot th').appendTo('#service-transactions-table thead');

    })
		</script>

@endpush
