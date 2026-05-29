@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show')
@section('post_styles')
    @include('component.datatables_cdn')
@endsection
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

					<div class="table-responsive">
						<table id="tour-expenses-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th onclick="sortTable(0, 'tour-expenses-table')">id <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(1, 'tour-expenses-table')">Tour Name <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(2, 'tour-expenses-table')">Tour Expense <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(3, 'tour-expenses-table')">Departure Date <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(4, 'tour-expenses-table')">Return Date <i class="fa fa-sort"></i></th>
									<th class="actions-button">Actions</th>
								</tr>
							</thead>
							<tbody>
							@forelse($tour_expenses as $expense)
								<tr>
									<td>{{ $expense->id }}</td>
									<td>{{ $expense->tour_name }}</td>
									<td>{{ $expense->tour_expenses }}</td>
									<td>{{ $expense->date_depart }}</td>
									<td>{{ $expense->date_return }}</td>
									<td>{!! $expense->action_buttons !!}</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="text-center">No tour expenses found</td>
								</tr>
							@endforelse
							</tbody>
						</table>
					</div>
                    <div id="tour_create" style="float: right; margin:50px 0px 50px 0px">
                        {!! \App\Helper\PermissionHelper::getCreateButton(url('utility_expenses/create/'.$offices->id), \App\Tour::class) !!}
                    </div>
					<h3  style=" margin:50px 0px 50px 0px">{{ trans('UTILITY EXPENSES') }}</h3>

					<div class="table-responsive">
						<table id="utility-expenses-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th onclick="sortTable(0, 'utility-expenses-table')">id <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(1, 'utility-expenses-table')">Subject <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(2, 'utility-expenses-table')">Month <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(3, 'utility-expenses-table')">Monthly Expense <i class="fa fa-sort"></i></th>
									<th class="actions-button">Actions</th>
								</tr>
							</thead>
							<tbody>
							@forelse($utility_expenses as $utility)
								<tr>
									<td>{{ $utility->id }}</td>
									<td>{{ $utility->subject }}</td>
									<td>{{ $utility->month }}</td>
									<td>{{ $utility->monthly_expense }}</td>
									<td>{!! $utility->action_buttons !!}</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="text-center">No utility expenses found</td>
								</tr>
							@endforelse
							</tbody>
						</table>
					</div>
            <div id="tour_create" style="float: right; margin:50px 0px 50px 0px">
                {!! \App\Helper\PermissionHelper::getCreateButton(url('employes-salary/create/'.$offices->id), \App\Tour::class) !!}
            </div>
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('EMPLOYEE SALARY') }}</h3>

					<div class="table-responsive">
						<table id="employee-salary-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th onclick="sortTable(0, 'employee-salary-table')">id <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(1, 'employee-salary-table')">Name <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(2, 'employee-salary-table')">Salary <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(3, 'employee-salary-table')">Month <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(4, 'employee-salary-table')">Bonuses <i class="fa fa-sort"></i></th>
									<th class="actions-button">Actions</th>
								</tr>
							</thead>
							<tbody>
							@forelse($employee_salaries as $salary)
								<tr>
									<td>{{ $salary->id }}</td>
									<td>{{ $salary->employe_name }}</td>
									<td>{{ $salary->employe_salary }}</td>
									<td>{{ $salary->month }}</td>
									<td>{{ $salary->bonuses }}</td>
									<td>{!! $salary->action_buttons !!}</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="text-center">No employee salaries found</td>
								</tr>
							@endforelse
							</tbody>
						</table>
					</div>
			
			<div id="tour_create" style="float: right; margin:50px 0px 50px 0px">
                {!! \App\Helper\PermissionHelper::getCreateButton(url('office_earning/create/'.$offices->id), \App\Tour::class) !!}
            </div>		
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('OFFICE EARNINGS') }}</h3>

					<div class="table-responsive">
						<table id="office-earnings-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th onclick="sortTable(0, 'office-earnings-table')">id <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(1, 'office-earnings-table')">Month <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(2, 'office-earnings-table')">Revenue <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(3, 'office-earnings-table')">Profit <i class="fa fa-sort"></i></th>
									<th class="actions-button">Actions</th>
								</tr>
							</thead>
							<tbody>
							@forelse($office_earnings as $earning)
								<tr>
									<td>{{ $earning->id }}</td>
									<td>{{ $earning->month }}</td>
									<td>{{ $earning->revenue }}</td>
									<td>{{ $earning->profit }}</td>
									<td>{!! $earning->action_buttons !!}</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="text-center">No office earnings found</td>
								</tr>
							@endforelse
							</tbody>
						</table>
					</div>
			
            <div id="office_balance" style="float: right; margin:50px 0px 50px 0px">
                {!! \App\Helper\PermissionHelper::getCreateButton(url('office_balance/create/'.$offices->id), \App\Tour::class) !!}
            </div>
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('OFFICE BALANCES') }}</h3>

					<div class="table-responsive">
						<table id="office-balances-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th onclick="sortTable(0, 'office-balances-table')">id <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(1, 'office-balances-table')">Subject <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(2, 'office-balances-table')">Month <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(3, 'office-balances-table')">Total Amount <i class="fa fa-sort"></i></th>
									<th onclick="sortTable(4, 'office-balances-table')">Due Date <i class="fa fa-sort"></i></th>
									<th class="actions-button">Actions</th>
								</tr>
							</thead>
							<tbody>
							@forelse($balances as $balance)
								<tr>
									<td>{{ $balance->id }}</td>
									<td>{{ $balance->subject_of_balance }}</td>
									<td>{{ $balance->month }}</td>
									<td>{{ $balance->total_amount }}</td>
									<td>{{ $balance->due_date }}</td>
									<td>{!! $balance->action_buttons !!}</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="text-center">No office balances found</td>
								</tr>
							@endforelse
							</tbody>
						</table>
					</div>
                    <div style="clear: both"></div>
                   
                </div>
				
				
				
				<div class="tab-pane fade in " role='tabpanel' id='office-invoice-tab'>
					<h1>Offices</h1>
					<div id="office_invoices" style="float: right; margin:50px 0px 50px 0px">
                {!! \App\Helper\PermissionHelper::getCreateButton(url('officeInvoices/create/'.$offices->id), \App\Tour::class) !!}
            </div>
				<div class="table-responsive">
					<table id="officesinvoice-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
						<thead>
							<tr>
								<th onclick="sortTable(0, 'officesinvoice-table')">id <i class="fa fa-sort"></i></th>
								<th onclick="sortTable(1, 'officesinvoice-table')">Office Name <i class="fa fa-sort"></i></th>
								<th onclick="sortTable(2, 'officesinvoice-table')">Office date <i class="fa fa-sort"></i></th>
								<th onclick="sortTable(3, 'officesinvoice-table')">Invoice No <i class="fa fa-sort"></i></th>
								<th class="actions-button">Actions</th>
							</tr>
						</thead>
						<tbody>
						@forelse($office_invoices as $invoice)
							<tr>
								<td>{{ $invoice->officeinvoice_dataId }}</td>
								<td>{{ $invoice->officeName }}</td>
								<td>{{ $invoice->date }}</td>
								<td>{{ $invoice->invoice_no }}</td>
								<td>{!! $invoice->action_buttons !!}</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" class="text-center">No office invoices found</td>
							</tr>
						@endforelse
						</tbody>
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