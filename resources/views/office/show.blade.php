@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
   ['title' => 'Customer offices', 'sub_title' => 'accounting Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'accountings', 'icon' => 'handshake-o', 'route' => route('accounting.index')],
   ['title' => 'Show', 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="margin_button">
                        <a href="javascript:history.back()">
                            <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                        </a>
						{{--
                        <a href="{!! route('offices.edit', $offices->id) !!}">
                            <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                        </a>--}}
                    </div>
                </div>
            </div>
            <div id="fixed-scroll" class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>{!!trans('main.Info')!!}</a></li>
					<li role='presentation'><a href="#office-invoice-tab" aria-controls='office-invoice-tab' role='tab' data-toggle='tab'>{!!trans('Office Invoice')!!}</a></li>
                    
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
					<div class="row">
					<div class="col-lg-6">

                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <input id="office_id" type="hidden" name="office_id" value = {{$offices->id}}>
                        <tr>
                            <td>
                                <b><i>{!!trans('Office Name')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offices->office_name!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Office Address')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offices->office_address!!}</td>
                        </tr>
                        </tbody>
                    </table>
						</div>
						<div class="col-lg-6">
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <tr>
                            <td>
                                <b><i>{!!trans('Tour Expenses')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$total_tour_expense!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Utility Expenses')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$total_utility_expense !!}</td>
                        </tr>
						<tr>
                            <td>
                                <b><i>{!!trans('Employee Salaries')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$total_employee_salary !!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Office Earnings')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$total_office_earning!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Total B.Amount')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$total_balance !!}</td>
                        </tr>
                        </tbody>
                    </table>
								</div>
								</div>
					
                    <div id="tour_create" style="float: right;margin:50px 0px 50px 0px">
                        {!! \App\Helper\PermissionHelper::getCreateButton(url('tour_expenses/create/'.$offices->id), \App\Tour::class) !!}
                    </div>
					<h3 style=" margin:50px 0px 50px 0px">{{ trans('TOUR EXPENSES') }}</h3>
					
					<table id="tour-expenses-table" class="table table-striped table-bordered table-hover " style='background:#fff; width: 98%; table-layout: fixed ; display = "none" ; margin-top:50px'>
						<thead>
							<tr>
								<th>id</th>
								<th>Tour Name</th>
								<th>Tour Expense</th>
								<th>Departure Date</th>
								<th>Return Date</th>
								<th>Actions</th>					   
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>id</th>
								<th>Tour Name</th>
								<th>Tour Expense</th>
								<th>Departure Date</th>
								<th>Return Date</th>
								<th>Actions</th>	
							</tr>
						</tfoot>
				</table>
                    <div id="tour_create" style="float: right; margin:50px 0px 50px 0px">
                        {!! \App\Helper\PermissionHelper::getCreateButton(url('utility_expenses/create/'.$offices->id), \App\Tour::class) !!}
                    </div>
					<h3  style=" margin:50px 0px 50px 0px">{{ trans('UTILITY EXPENSES') }}</h3>
					
					<table id="utility-expenses-table" class="table table-striped table-bordered table-hover " style='background:#fff; width: 98%; table-layout: fixed ; display = "none" ; margin-top:50px'>
                	<thead>
                       	<tr>
							<th>id</th>
							<th>Subject</th>
							<th>Month</th>
							<th>Monthly Expense</th>
							<th>Actions</th>
						</tr>
               		</thead>
                <tfoot>
                       <tr>
							<th>id</th>
							<th>Subject</th>
							<th>Month</th>
							<th>Monthly Expense</th>
							<th>Actions</th>
						</tr>
                </tfoot>
              
            </table>
            <div id="tour_create" style="float: right; margin:50px 0px 50px 0px">
                {!! \App\Helper\PermissionHelper::getCreateButton(url('employes-salary/create/'.$offices->id), \App\Tour::class) !!}
            </div>
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('EMPLOYEE SALARY') }}</h3>
					
					<table id="employee-salary-table" class="table table-striped table-bordered table-hover " style='background:#fff; width: 98%; table-layout: fixed ; display = "none" ; margin-top:50px'>
                <thead>
                    <tr>
                        
                        <th>id</th>
                        <th>Name</th>
                        <th>Salary</th>
						<th>Month</th>
						<th>Bonuses</th>
						<th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                    	<th>id</th>
                        <th>Name</th>
                        <th>Salary</th>
						<th>Month</th>
						<th>Bonuses</th>
						<th>Actions</th>
                    </tr>
                </tfoot>
              
            </table>
			
			<div id="tour_create" style="float: right; margin:50px 0px 50px 0px">
                {!! \App\Helper\PermissionHelper::getCreateButton(url('office_earning/create/'.$offices->id), \App\Tour::class) !!}
            </div>		
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('OFFICE EARNINGS') }}</h3>
					
					<table id="office-earnings-table" class="table table-striped table-bordered table-hover " style='background:#fff; width: 98%; table-layout: fixed ; display = "none" ; margin-top:50px'>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Month</th>
                        <th>Revenue</th>
						<th>Profit</th>
						<th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                     <tr>
                        <th>id</th>
                        <th>Month</th>
                        <th>Revenue</th>
						<th>Profit</th>
						<th>Actions</th>
                    </tr>
                </tfoot>
              
            </table>
			
            <div id="office_balance" style="float: right; margin:50px 0px 50px 0px">
                {!! \App\Helper\PermissionHelper::getCreateButton(url('office_balance/create/'.$offices->id), \App\Tour::class) !!}
            </div>
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('OFFICE BALANCES') }}</h3>
					
					<table id="office-balances-table" class="table table-striped table-bordered table-hover " style='background:#fff; width: 98%; table-layout: fixed ; display = "none" ; margin-top:50px'>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Subject</th>
                        <th>Month</th>
                        <th>Total Amount</th>
                        <th>Due Date</th>
						<th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>id</th>
                        <th>Subject</th>
                        <th>Month</th>
                        <th>Total Amount</th>
                        <th>Due Date</th>
						<th>Actions</th>
                    </tr>
                </tfoot>
              
            </table>
                    <div style="clear: both"></div>
                   
                </div>
				
				
				
				<div class="tab-pane fade in " role='tabpanel' id='office-invoice-tab'>
					<h1>Offices</h1>
					<div id="office_invoices" style="float: right; margin:50px 0px 50px 0px">
                {!! \App\Helper\PermissionHelper::getCreateButton(url('officeInvoices/create/'.$offices->id), \App\Tour::class) !!}
            </div>
				<div>
                <table id="officesinvoice-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%; table-layout: fixed ; display = "none"'>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Office Name</th>
                        <th>Office date</th>
						<th>Invoice No</th>
						<th>Actions</th>
                    </tr>
                </thead>
				<tfoot>
                    <tr>
                       <th>id</th>
                        <th>Office Name</th>
                        <th>Office date</th>
						<th>Invoice No</th>
						<th>Actions</th>
                    </tr>
                </tfoot>
              
            </table>
            </div>
</section>
    <span id="services_name" data-service-name='accounting' data-history-route="{{route('services_history', ['id' => $offices->id])}}"></span>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
@push('scripts')
<script>
  $(document).ready(function() {
        let office_id = $("#office_id").val();
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#tour-expenses-table').DataTable({
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
				url: `{{url('tour_expenses/api/data/${office_id}')}}`,
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'tour_name',
                    name: 'tour_name',
                    className: 'touredit-name'
                },
                {
                    data: 'tour_expenses',
                    name: 'tour_expenses',
                    className: 'touredit-departure_date'
                },
                //        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                {
                    data: 'tour_departure_date',
                    name: 'tour_departure_date',
                    className: 'touredit-country_begin'
                },
                {
                    data: 'tour_return_date',
                    name: 'tour_return_date',
                    className: 'touredit-city_begin'
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
        $('#tour-table tfoot th').each(function() {
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
        $('#tour-table tfoot th').appendTo('#tour-table thead');

    })
		</script>
<script>
	  $(document).ready(function() {
        let office_id = $("#office_id").val();
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#utility-expenses-table').DataTable({
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
				url: `{{url('utility_expenses/api/data/${office_id}')}}`,
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'subject_of_expense',
                    name: 'subject_of_expense',
                    className: 'touredit-name'
                },
                {
                    data: 'month',
                    name: 'month',
                    className: 'touredit-departure_date'
                },
                //        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                {
                    data: 'monthly_expense',
                    name: 'monthly_expense',
                    className: 'touredit-country_begin'
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
        $('#tour-table tfoot th').each(function() {
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
        $('#tour-table tfoot th').appendTo('#tour-table thead');

    })

		</script>
		<script>
  $(document).ready(function() {
        let office_id = $("#office_id").val();
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#employee-salary-table').DataTable({
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
				url: `{{url('employes-salary/api/data/${office_id}')}}`,
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'employe_name',
                    name: 'employe_name',
                    className: 'touredit-name'
                },
                {
                    data: 'employe_salary',
                    name: 'employe_salary',
                    className: 'touredit-departure_date'
                },
                //        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                {
                    data: 'month',
                    name: 'month',
                    className: 'touredit-country_begin'
                },
                {
                    data: 'bonuses',
                    name: 'bonuses',
                    className: 'touredit-city_begin'
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
        $('#tour-table tfoot th').each(function() {
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
        $('#tour-table tfoot th').appendTo('#tour-table thead');

    })
		</script>
<script>
	  $(document).ready(function() {
        let office_id = $("#office_id").val();
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#office-earnings-table').DataTable({
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
				url: `{{url('office_earning/api/data/${office_id}')}}`,
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'month',
                    name: 'month',
                    className: 'touredit-name'
                },
                {
                    data: 'revenue',
                    name: 'revenue',
                    className: 'touredit-departure_date'
                },
                //        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                {
                    data: 'profit',
                    name: 'profit',
                    className: 'touredit-country_begin'
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
        $('#tour-table tfoot th').each(function() {
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
        $('#tour-table tfoot th').appendTo('#tour-table thead');

    })

		</script>
<script>
	  $(document).ready(function() {
        let office_id = $("#office_id").val();
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#office-balances-table').DataTable({
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
				url: `{{url('office_balance/api/data/${office_id}')}}`,
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'subject_of_balance',
                    name: 'subject_of_balance',
                    className: 'touredit-name'
                },
                {
                    data: 'month',
                    name: 'month',
                    className: 'touredit-departure_date'
                },
                //        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
                {
                    data: 'total_amount',
                    name: 'total_amount',
                    className: 'touredit-country_begin'
                },
				 {
                    data: 'due_date',
                    name: 'due_date',
                    className: 'touredit-country_begin'
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
        $('#tour-table tfoot th').each(function() {
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
        $('#tour-table tfoot th').appendTo('#tour-table thead');

    })

		</script>
				
				<script>
					/// ------ Invoice datatable ----->
    $(document).ready(function() {
		let office_id = $("#office_id").val();
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'officeinvoiceedit-status' : '';
        let table = $('#officesinvoice-table').DataTable({
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
                url: `{{url('officeInvoices/api/data/${office_id}')}}`,
				//dataSrc: 'data'
            },columns: [
    {
        data: 'officeinvoice_dataId',
        name: 'officeinvoice_dataId',
    },
    {
        data: 'officeName',
        name: 'officeName',
        className: 'officeinvoiceedit-name'
    },
    {
        data: 'date',
        name: 'date',
        className: 'officeinvoiceedit-name'
    },
    {
        data: 'invoice_no',
        name: 'invoice_no',
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
        $('#officesinvoice-table tfoot th').each(function() {
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
        $('#officesinvoice-table tfoot th').appendTo('#officesinvoice-table thead');

    })
</script>


@endpush