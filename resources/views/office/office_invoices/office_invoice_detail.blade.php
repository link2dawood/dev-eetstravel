@extends('scaffold-interface.layouts.app')

@section('title', 'Office Invoice Details')

@section('content')
    @include('layouts.title', [
        'title' => 'Invoice Items',
        'sub_title' => 'Office Invoice Detail',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'officeInvoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'office_invoice_detail', 'route' => null],
        ],
    ])

	<section class="content">
    <div class="box box-primary">
		<a class="btn btn-default"
                                               
                                               href="{{route('office_invoices_pdf_export', ['id' =>$officeinvoice_dataId, 'type' => 'short'])}}" style = "float:right;">Invoice PDF</a>
        <div class="box-body">
            <div>
             
            </div>
            @if(session('message_buses'))
            <div class="alert alert-info col-md-12" style="text-align: center;">
                {{session('message_buses')}}
            </div>
            @endif
         
            <br>
            <br>
      

           <table id="officesinvoicedetail-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 90%; table-layout: fixed ; display = "none"'>
			     <input id="offices_id" type="hidden" name="offices_id" value = {{$officeinvoice_dataId}}>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Items</th>
                        <th>Date</th>
                        <th>Item Code</th>
						<th>Amount</th>
						<th>Action</th>
                        
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                       <th>id</th>
                        <th>Items</th>
                        <th>Date</th>
                        <th>Item Code</th>
						<th>Amount</th>
						<th>Action</th>
                        
                    </tr>
                </tfoot>
              
            </table>
        </div>
    </div>
</section>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
@push('scripts')

 		<script>
					/// ------ Invoice Details datatable ----->
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'officeinvoiceedit-status' : '';
		let office_id = $("#offices_id").val();
        let table = $('#officesinvoicedetail-table').DataTable({
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
                    title: 'Office Invoice List',
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
                url: `{{url('office-invoices-details/api/data/${office_id}')}}`,
				//dataSrc: 'data'
            },columns: [
    {
        data: 'officeInvoiceId',
        name: 'officeInvoiceId',
    },
    
  
				
	{
        data: 'officeinvoice_item',
        name: 'officeinvoice_item',
        className: 'officeinvoiceedit-name'
    },
				
	{
        data: 'officeinvoice_date',
        name: 'officeinvoice_date',
        className: 'officeinvoiceedit-name'
    },
	
	{
        data: 'officeinvoice_code',
        name: 'officeinvoice_code',
        className: 'officeinvoiceedit-name' 
    },
				
	{
        data: 'officeinvoice_amount',
        name: 'officeinvoice_amount',
        className: 'officeinvoiceedit-name'
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
                'targets': 4,
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
        $('#officesinvoicedetail-table tfoot th').each(function() {
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
        $('#officesinvoicedetail-table tfoot th').appendTo('#officesinvoicedetail-table thead');

    })
</script>
@endpush
	
