@extends('scaffold-interface.layouts.app')
@section('content')
@include('layouts.title',
['title' => 'Customer Transaction', 'sub_title' => 'Transaction List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]])

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div>
                <div id="tour_create">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('transaction.create'), \App\Invoices::class) !!}
                </div>

            </div>
            @if(session('message_buses'))
            <div class="alert alert-info col-md-12" style="text-align: center;">
                {{session('message_buses')}}
            </div>
            @endif
         
            <br>
            <br>
      

            <table id="inovices-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 90%; table-layout: fixed ; display = "none"'>
                <thead>
            <tr>
				<th>Id</th>
                <th>Date</th>
				<th>Payment To</th>
				<th>Transaction No</th>
				<th>Invoice No</th>
				<th>Amount</th>
                <th>Unallocated</th>
				<th>Action</th>
            </tr>
        </thead>
                <tfoot>
                    <tr>
				<th>Id</th>
                <th>Date</th>
				<th>Payment To</th>
				<th>Transaction No</th>
				<th>Invoice No</th>
				<th>Amount</th>
                <th>Unallocated</th>
				<th>Action</th>
            </tr>
                </tfoot>
              
            </table>
        </div>
    </div>
</section>

@endsection
@push('scripts')

<script>
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#inovices-table').DataTable({
            dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)',
                    },
                    // customize: function (doc) {
                    //     doc.content[1].table.widths = 
                    //     Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    // },
                },
            ],
            language: {
                search: "Global Search :"
            },
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: "{{route('transaction_data')}}",
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
                    data: 'pay_to',
                    name: 'pay_to',
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
                
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    sorting: false,
                    orderable: false
                }
            ],
            'columnDefs': [{

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
        $('#inovices-table tfoot th').each(function() {
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
        $('#inovices-table tfoot th').appendTo('#inovices-table thead');

    })
</script>
@endpush
