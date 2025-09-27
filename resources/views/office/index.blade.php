@extends('scaffold-interface.layouts.app')
@section('content')
@include('layouts.title',
['title' => 'Office Fees', 'sub_title' => 'Office List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]])

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
			
            <div>
                <div id="tour_create">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('office.create'), \App\Tour::class) !!}
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
            <table id="offices-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>

                <thead>
                    <tr>
                        <th>id</th>
                        <th>Office Name</th>
                        <th>Office Address</th>
						<th>Bank Name</th>
                        <th>Account No</th>
                        <th>Swift Code</th>
                        <th>Tel</th>
                        <th>Fax</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($officesData as $office)
                    <tr>
                        <td>{{ $office->id }}</td>
                        <td>{{ $office->office_name }}</td>
                        <td>{{ $office->office_address }}</td>
                        <td>{{ $office->bank_name }}</td>
                        <td>{{ $office->account_no }}</td>
                        <td>{{ $office->swift_code }}</td>
                        <td>{{ $office->tel }}</td>
                        <td>{{ $office->fax }}</td>
                        <td>{!! $office->action_buttons !!}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th >id</th>
                        <th>Office Name</th>
                        <th>Office Address</th>
						<th>Bank Name</th>
                        <th>Account No</th>
                        <th>Swift Code</th>
                        <th>Tel</th>
                        <th>Fax</th>
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
	
	
//	$(function(){
  //$('.selectedOfice').select2().on('change', function(e) {
  //  var data = $(".selectedOfice option:selected").text();
	//  alert(data);
   // $("#test").val(data);
	  
  //});

//});
	
	
	
	
	
	
	
	
	

	
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#offices-table').DataTable({
            dom: "<'row'<'col-md-4'l><'col-md-4'B><'col-md-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Offices List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Offices List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Offices List',
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
                { targets: [8], orderable: false } // Actions column not sortable
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
        $('#offices-table tfoot th').each(function() {
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
        $('#offices-table tfoot th').appendTo('#offices-table thead');

    })
</script>
@endpush
