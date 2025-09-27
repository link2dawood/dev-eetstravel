@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Tour Quotations', 'sub_title' => 'Quotation List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Quotation', 'icon' => 'list-ul', 'route' => null]]])

    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="box-header with-border">
                    <div>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create', ['is_quotation' => 1]), \App\Tour::class) !!}
                    </div>
					
					<div class="toggle" style = "float:right">
					<input type="checkbox" id= "check" onclick = "myfunction()" checked/>
					<label></label>
				</div>
                </div>
				

                <br>
               
                {{--     TAB QUOTATION    --}}
                <div class="tab-content">
                   
                        <table id="quotation_table" class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
                            <thead>
                            <th>ID</th>
                            <th>{{trans('main.Name')}}</th>
                            <th>{{trans('main.Tour')}}</th>
                            <th>{{trans('main.Assigned')}}</th>
                            <th>{{trans('main.CreatedAt')}}</th>
                            <th class="actions-button">{{trans('main.Frontsheet')}}</th>
                            <th class="actions-button" style="width: 140px!important">{{trans('main.Actions')}}</th>
                            </thead>
                            <tfoot>
                            <tr>
                                <th class="not"></th>
                                <th>{{trans('main.Name')}}</th>
                                <th>{{trans('main.Tour')}}</th>
                                <th>{{trans('main.Assigned')}}</th>
                                <th>{{trans('main.CreatedAt')}}</th>
                                <th class="not"></th>
                                <th class="not"></th>
                            </tr>
                            </tfoot>
                        </table>
						
						
						       <table id="go-ahead-table" class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%; '>
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{trans('main.Name')}}</th>
                                <th>{{trans('main.DepDate')}}</th>
                                <th>{{trans('main.CountryBegin')}}</th>
                                <th>{{trans('main.CityBegin')}}</th>
                                <th>{{trans('main.Status')}}</th>
                                <th>{{trans('main.Externalname')}}</th>
								{{--<th>Room Types</th>--}}
								
                                <th class="actions-button" style="width: 140px">{{trans('main.Actions')}}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th class="not"></th>
                                <th>{{trans('main.Name')}}</th>
                                <th>{{trans('main.Depdate')}}</th>
                                <th>{{trans('main.CountryBegin')}}</th>
                                <th>{{trans('main.CityBegin')}}</th>
                                <th class="select_search">{{trans('main.Status')}}</th>
                                <th>{{trans('main.ExternalName')}}</th>
                                <th class="not"></th>
                            </tr>
                            </tfoot>
                        </table>
                

                {{--     TAB QUOTATION    --}}

                   

                </div>

            </div>
        </div>
    </section>
@endsection


@push('scripts')

    <script>
		
        $(document).ready(function() {
			
            let table = $('#quotation_table').DataTable({
                dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
                        extend: 'csv',
                        title: 'List Quotation of The Agency',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'List Quotation of The Agency',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'List Quotation of The Agency',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    },
//                    {
//                        text: 'Import',
//                        action: () => {
//                            console.log('import');
//                            getModalForImport(true);
//                        }
//                    },
                ],
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: "{{route('quotation.data')}}",
                },
                columns: [
                    {data: 'id', name: 'quotations.id'},
                    {data: 'name', name: 'quotations.name'},
                    {data: 'tour_name', name: 'tours.name'},
                    {data: 'user_name', name: 'users.name'},
                    {data: 'created_at', name: 'quotations.created_at'},
                    {data: 'comparison', name: 'quotations.comparison', searchable: false, sorting: false, orderable: false},
                    {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
                ],
            });
            $('#quotation_table tfoot th').each( function () {
                let column = this;
                if (column.className !== 'not') {
                    let title = $(this).text();
                    $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
                }
            });
            table.columns().every( function () {
                let that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if(that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
            $('#quotation_table tfoot th').appendTo('#quotation_table thead');
        })
    </script>
    <script>
        $(document).ready(function() {
            let table = $('#pending-table').DataTable({
                dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
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
                language : {
                    search: "Global Search :"
                },
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: "{{route('tour_data_quotation')}}",
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name', className: 'touredit-name'},
                    {data: 'departure_date', name: 'departure_date', className: 'touredit-departure_date'},
//        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                    {data: 'country_begin', name: 'country_begin', className: 'touredit-country_begin'},
                    {data: 'city_begin', name: 'city_begin', className: 'touredit-city_begin'},
                    {data: 'status_name', className: 'touredit-status'},
                    {data: 'external_name', name: 'external_name', className: 'touredit-external_name'},
                    {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
                ],
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        if(column.footer().className == 'select_search'){
                            var select = $('<select class="form-control"><option value=""></option></select>')
                                .appendTo( $(column.footer()).empty() )
                                .on( 'change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search( val ? '^'+val+'$' : '', true, false ).draw();
                                });

                            column.data().unique().sort().each( function ( d, j ) {
                                select.append( '<option value="'+d+'">'+d+'</option>' )
                            });
                        }
                    });
                }
            });
            $('#tour-table tfoot th').each( function () {
                let column = this;
                if (column.className !== 'not') {
                    let title = $(this).text();
                    $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
                }
            });
            table.columns().every( function () {
                let that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if(that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
            $('#tour-table tfoot th').appendTo('#tour-table thead');

        })
    </script>

<script>
        $(document).ready(function() {
			
            let table = $('#go-ahead-table').DataTable({
                dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
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
                language : {
                    search: "Global Search :"
                },
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: "{{route('goahead_data_quotation')}}",
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name', className: 'touredit-name'},
                    {data: 'departure_date', name: 'departure_date', className: 'touredit-departure_date'},
//        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                    {data: 'country_begin', name: 'country_begin', className: 'touredit-country_begin'},
                    {data: 'city_begin', name: 'city_begin', className: 'touredit-city_begin'},
                    {data: 'status_name', className: 'touredit-status'},
                    {data: 'external_name', name: 'external_name', className: 'touredit-external_name'},
                    {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
                ],
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        if(column.footer().className == 'select_search'){
                            var select = $('<select class="form-control"><option value=""></option></select>')
                                .appendTo( $(column.footer()).empty() )
                                .on( 'change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search( val ? '^'+val+'$' : '', true, false ).draw();
                                });

                            column.data().unique().sort().each( function ( d, j ) {
                                select.append( '<option value="'+d+'">'+d+'</option>' )
                            });
                        }
                    });
                }
            });
            $('#tour-table tfoot th').each( function () {
                let column = this;
                if (column.className !== 'not') {
                    let title = $(this).text();
                    $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
                }
            });
            table.columns().every( function () {
                let that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if(that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
            $('#tour-table tfoot th').appendTo('#tour-table thead');

        })
    </script>


<script>
        $(document).ready(function() {
            let table = $('#cancelled-table').DataTable({
                dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
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
                language : {
                    search: "Global Search :"
                },
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: "{{route('cancelled_data_quotation')}}",
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name', className: 'touredit-name'},
                    {data: 'departure_date', name: 'departure_date', className: 'touredit-departure_date'},
//        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                    {data: 'country_begin', name: 'country_begin', className: 'touredit-country_begin'},
                    {data: 'city_begin', name: 'city_begin', className: 'touredit-city_begin'},
                    {data: 'status_name', className: 'touredit-status'},
                    {data: 'external_name', name: 'external_name', className: 'touredit-external_name'},
                    {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
                ],
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        if(column.footer().className == 'select_search'){
                            var select = $('<select class="form-control"><option value=""></option></select>')
                                .appendTo( $(column.footer()).empty() )
                                .on( 'change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search( val ? '^'+val+'$' : '', true, false ).draw();
                                });

                            column.data().unique().sort().each( function ( d, j ) {
                                select.append( '<option value="'+d+'">'+d+'</option>' )
                            });
                        }
                    });
                }
            });
            $('#tour-table tfoot th').each( function () {
                let column = this;
                if (column.className !== 'not') {
                    let title = $(this).text();
                    $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
                }
            });
            table.columns().every( function () {
                let that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if(that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
            $('#tour-table tfoot th').appendTo('#tour-table thead');

        })
    </script>


<script>
        $(document).ready(function() {
            let table = $('#tour-table').DataTable({
                dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
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
                language : {
                    search: "Global Search :"
                },
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: "{{route('tour_data')}}",
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name', className: 'touredit-name'},
                    {data: 'departure_date', name: 'departure_date', className: 'touredit-departure_date'},
//        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                    {data: 'country_begin', name: 'country_begin', className: 'touredit-country_begin'},
                    {data: 'city_begin', name: 'city_begin', className: 'touredit-city_begin'},
                    {data: 'status_name', className: 'touredit-status'},
                    {data: 'external_name', name: 'external_name', className: 'touredit-external_name'},
                    {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
                ],
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        if(column.footer().className == 'select_search'){
                            var select = $('<select class="form-control"><option value=""></option></select>')
                                .appendTo( $(column.footer()).empty() )
                                .on( 'change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search( val ? '^'+val+'$' : '', true, false ).draw();
                                });

                            column.data().unique().sort().each( function ( d, j ) {
                                select.append( '<option value="'+d+'">'+d+'</option>' )
                            });
                        }
                    });
                }
            });
            $('#tour-table tfoot th').each( function () {
                let column = this;
                if (column.className !== 'not') {
                    let title = $(this).text();
                    $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
                }
            });
            table.columns().every( function () {
                let that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if(that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
            $('#tour-table tfoot th').appendTo('#tour-table thead');

        })

	function myfunction(){
		
		  var checkBox = document.getElementById("check");
  // Get the output text
		
  var text = document.getElementById("text");
		if (checkBox.checked == true){
   
			 $('#go-ahead-table').hide();
			 $('#go-ahead-table_wrapper').hide();
			 $('#quotation_table').show();
			 $('#quotation_table_wrapper').show();
		
  } else {
   
	  $('#go-ahead-table').show();
	  $('#go-ahead-table_wrapper').show();
	  $('#quotation_table').hide();
	   $('#quotation_table_wrapper').hide();
	  
	  	
		
	
	}
   
  }
		
	 </script>
<style>
.toggle {
  position: relative;
  height: 42px;
  display: flex;
  align-items: center;
  box-sizing:border-box;
}
.toggle input[type="checkbox"] {
  position: absolute;
  left: 0;
  top: 0;
  z-index: 10;
  width: 100%;
  height: 100%;
  cursor: pointer;
  opacity: 0;
}
.toggle label {
  position: relative;
  display: flex;
  height: 100%;
  align-items: center;
  box-sizing:border-box;
}
.toggle label:before  , .toggle label:after {
  font-size: 18px;
  font-weight: bold;
  font-family:arial;
  transition: 0.2s ease-in;
  box-sizing:border-box;
}
.toggle label:before {
  content: "Quotations";
  background: #fff;
  color: #000;
  height: 42px;
  width: 140px;
  display: inline-flex;
  align-items: center;
  padding-left: 15px;
  border-radius: 30px;
  border: 1px solid #eee;
  box-shadow: inset 140px 0px 0 0px #000;
font-size:10px
}
.toggle label:after {
  content: "GoAhead";
  position: absolute;
  left: 80px;
  line-height: 42px;
  top: 0;
  color: #FFF;
font-size:10px
}
.toggle input[type="checkbox"]:checked + label:before {
    color: #000;
    box-shadow: inset 0px 0px 0 0px #000;
}
.toggle input[type="checkbox"]:checked + label:after {
  color: #FFF;
}
@endpush


