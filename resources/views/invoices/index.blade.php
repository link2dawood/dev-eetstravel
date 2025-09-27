@extends('scaffold-interface.layouts.app')
@section('content')
@include('layouts.title',
['title' => 'Supplier Invoices', 'sub_title' => 'Invoice List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]])

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div>
                <div id="tour_create">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class) !!}
                </div>

            </div>
            @if(session('message_buses'))
            <div class="alert alert-info col-md-12" style="text-align: center;">
                {{session('message_buses')}}
            </div>
            @endif
         
            <br>
            <br>
      
			<div class="table-responsive">
            	<table id="inovices-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%; display = "none"'>
					<thead>
						<tr>
							<th>id</th>
							<th>Invoice No</th>
							<th>Due Date</th>
							<th>Recieved Date</th>
							<th>Tour</th>
							<th>Service</th>
							<th>Office Name</th>
							<th>Total Price</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					@foreach($invoicesData as $invoice)
						<tr>
							<td>{{ $invoice->id }}</td>
							<td>{{ $invoice->invoice_no }}</td>
							<td>{{ $invoice->dueDate }}</td>
							<td>{{ $invoice->receivedDate }}</td>
							<td>{{ $invoice->tour }}</td>
							<td>{{ $invoice->package }}</td>
							<td>{{ $invoice->officeName }}</td>
							<td>{{ $invoice->total_amount }}</td>
							<td>{{ $invoice->status }}</td>
							<td>{!! $invoice->action_buttons !!}</td>
						</tr>
					@endforeach
					</tbody>
					<tfoot>
						<tr>
							 <th>id</th>
							<th>Invoice No</th>
							<th>Invoice Date</th>
							<th>Invoice Date</th>
							<th>Tour</th>
							<th>Service</th>
							<th>Office Name</th>
							<th>Total Price</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</tfoot>

            	</table>
			</div>
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
            dom: "<'row'<'col-md-5'l><'col-md-2'B><'col-md-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Supplier Invoice List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Supplier Invoice List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Supplier Invoice List',
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
            pageLength: 50,
            columnDefs: [
                { targets: [9], orderable: false } // Actions column not sortable
            ],
            'columnDefs': [{
                'targets': 5,
                'createdCell': function(td, cellData, rowData, row, col) {
                    
					var url = "{{ route('tour.update', ':id') }}".replace(':id', rowData.id);

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
