@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show Invoice')
@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    :title="$invoices->invoice_no ? ('Invoice ' . $invoices->invoice_no) : ('Invoice #' . $invoices->id)"
    description="Supplier invoice details and payments."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Supplier Invoices', 'href' => route('invoices.index')],
        ['label' => $invoices->invoice_no ?: ('#' . $invoices->id)],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {!! trans('main.Back') !!}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<input id="invoice_id" type="hidden" name="invoice_id" value="{{ $invoices->id }}">
<input id="transaction_id" type="hidden" name="transaction_id" value="{{ $invoices->id }}">

@php $isPaid = trim($invoices->status($invoice_tour) ?? '') === 'Paid'; @endphp

{{-- ============================================================ --}}
{{-- Details --}}
{{-- ============================================================ --}}
<div class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
        <x-ui.icon name="file-text" size="sm" class="text-slate-400" />
        <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Info') !!}</h2>
    </div>
    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Invoice No') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{!! $invoices->invoice_no ?: '—' !!}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Due Date') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{!! $invoices->dueDate ?: '—' !!}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Received Date') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{!! $invoices->receivedDate ?: '—' !!}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Office Name') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{{ $office->office_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Total Price') !!}</dt>
            <dd class="mt-0.5 font-medium text-slate-900">{{ $invoices->total_amount ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Status') !!}</dt>
            <dd class="mt-0.5">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $isPaid ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700' }}">
                    {{ $invoices->status($invoice_tour) }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Tour') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{{ $invoice_tour->tours->name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Service') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{{ $invoice_tour->package->name ?? '—' }}</dd>
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
            @if(Auth::user()->can('invoices.create'))
                <x-ui.button as="a" href="{{ route('add_payment', $invoices->id) }}" icon="plus" size="sm">Add payment</x-ui.button>
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
{{-- Files (inline Tailwind panel — mirrors component.files JS hooks) --}}
{{-- ============================================================ --}}
@php
    $images = collect($files['image'] ?? [])
        ->filter(fn($i) => !empty($i->attach_file_name))->values();
    $attachments = collect($files['attach'] ?? [])
        ->filter(fn($a) => !empty($a->attach_file_name))->values();
    $totalCount = $images->count() + $attachments->count();
@endphp
<div class="mt-6 rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
        <x-ui.icon name="paperclip" size="sm" class="text-slate-400" />
        <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Files') !!}</h2>
    </div>

    @if($totalCount === 0)
        <div class="px-4 py-8 text-center">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-2">
                <x-ui.icon name="paperclip" />
            </div>
            <p class="text-sm font-medium text-slate-700">No files yet</p>
            <p class="mt-1 text-xs text-slate-500">Attach documents from the Edit page.</p>
        </div>
    @else
        @if($images->count())
            <div class="px-4 pt-4">
                <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">
                    {!! trans('main.Photos') !!}
                    <span class="ml-1 text-slate-400 normal-case font-normal">({{ $images->count() }})</span>
                </h3>
                <div class="image grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2">
                    @foreach($images as $image)
                        @php $imgUrl = asset('storage/' . $image->attach_file_name); @endphp
                        <div class="del-container relative group rounded overflow-hidden border border-slate-200 bg-slate-50">
                            <a href="{{ $imgUrl }}" class="block">
                                <img src="{{ $imgUrl }}" alt="" class="w-full h-24 object-cover" loading="lazy" />
                            </a>
                            <button type="button"
                                    class="del-attach absolute top-1 right-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/90 text-danger-600 shadow-subtle opacity-0 group-hover:opacity-100 transition-opacity"
                                    data-attach-url="{{ route('file_delete', ['id' => $image->id]) }}"
                                    aria-label="Delete photo">
                                <x-ui.icon name="x" size="xs" />
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($attachments->count())
            <div class="px-4 pt-4 pb-4">
                <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">
                    {!! trans('main.Files') !!}
                    <span class="ml-1 text-slate-400 normal-case font-normal">({{ $attachments->count() }})</span>
                </h3>
                <ul class="divide-y divide-slate-100 list-none pl-0 m-0">
                    @foreach($attachments as $attach)
                        @php
                            $fileUrl     = asset('storage/' . $attach->attach_file_name);
                            $displayName = basename($attach->attach_file_name);
                        @endphp
                        <li class="del-container py-2 flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded bg-slate-100 text-slate-500 shrink-0">
                                <x-ui.icon name="paperclip" size="sm" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ $fileUrl }}" target="_blank" class="link_file block text-sm font-medium text-slate-700 hover:text-primary-700 truncate">
                                    <span class="name_link_file">{{ $displayName }}</span>
                                </a>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $attach->created_at }}</p>
                            </div>
                            <button type="button"
                                    class="del-attach inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-danger-50 hover:text-danger-700 shrink-0"
                                    data-attach-url="{{ route('file_delete', ['id' => $attach->id]) }}"
                                    aria-label="Delete file">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
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
            <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $invoices->id }}">
            <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['invoices'] }}">

            <div class="flex">
                <button type="submit" id="btn_send_comment" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                    <x-ui.icon name="send" size="sm" />
                    {!! trans('main.Send') !!}
                </button>
            </div>
        </form>
    </div>
</div>

<span id="services_name" data-service-name='accounting' data-history-route="{{ route('services_history', ['id' => $invoices->id]) }}"></span>
@endsection

@push('styles')
<style>
    /* Tame bootstrap-fileinput buttons inside the comments box so the legacy
       widget matches the Tailwind theme. Scoped to #form_comment. */
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
                url: "/accountingServiceTransaction/api/data/2/"+ invoice_id,
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
				{
                    data: 'date',
                    name: 'date',
                    className: 'touredit-name'
                }
					,
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

        // Magnific Popup gallery on the inline files panel.
        if ($.fn.magnificPopup) {
            $('.image').magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: { enabled: true }
            });
        }

        // Inline attachment delete (preserved from component.files).
        $('.del-attach').on('click', function (e) {
            e.preventDefault();
            var btn = this;
            var url = $(btn).attr('data-attach-url');
            if (!url) return;
            if (!confirm("Are you sure you want to delete this attachment?")) return;
            $.ajax({
                url: url,
                method: 'POST',
                data: { "_token": "{{ csrf_token() }}" },
                success: function () { $(btn).closest('.del-container').hide(); },
                error: function (res) { console.log(res); }
            });
        });
    })
</script>
@endpush
