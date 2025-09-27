@extends('scaffold-interface.layouts.app')
@section('content')
@include('layouts.title',
['title' => 'Client Invoices', 'sub_title' => 'Invoices according to tours',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div>
                <div id="tour_create">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class) !!}
                </div>

            </div>
            @if(session('message_buses'))
            <div class="alert alert-info col-md-12" style="text-align: center;">
                {{session('message_buses')}}
            </div>
            @endif
         
            <br>
            <br>
      

            <table id="transactions-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%; display = "none"'>
                <thead>
                    <tr>
                        <th>id</th>
						<th>Date</th>
						<th>Invoice No</th>
                        <th>Tour Name</th>
						<th>Client Name</th>
                        <th>Amount Recieveable</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($accountingData as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->date }}</td>
                        <td>{{ $transaction->invoice_no }}</td>
                        <td>{{ $transaction->tourName }}</td>
                        <td>{{ $transaction->clientName }}</td>
                        <td>{{ $transaction->amount_receiveable }}</td>
                        <td>{{ $transaction->Status }}</td>
                        <td>{!! $transaction->action_buttons !!}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>id</th>
						<th>Date</th>
						<th>Invoice No</th>
                        <th>Tour Name</th>
						<th>Client Name</th>
                        <th>Amount Recieveable</th>
                        <th>Status</th>
                        <th>Actions</th>
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
        let table = $('#transactions-table').DataTable({
            dom: "<'row'<'col-md-4'l><'col-md-4'B><'col-md-4'f>>" +
                "<tr>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Invoice List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Invoice List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Invoice List',
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
                { targets: [7], orderable: false } // Actions column not sortable
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
        $('#transactions-table tfoot th').each(function() {
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
        $('#transactions-table tfoot th').appendTo('#transactions-table thead');

    })
</script>
@endpush
