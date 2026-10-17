@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Office Details')
@section('content')
    @include('layouts.title',
   ['title' => 'Office', 'sub_title' => 'Office finances',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Offices', 'icon' => 'handshake-o', 'route' => route('office.index')],
   ['title' => 'Show', 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="margin_button">
                        <a href="{{ route('office.index') }}">
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
                <ul class="nav nav-tabs" role='tablist'>
                    <li role='presentation' class="active"><a class="nav-link active" href="#info-tab" aria-controls='info-tab' role='tab' data-bs-toggle='tab'>{!!trans('main.Info')!!}</a></li>
					<li role='presentation'><a class="nav-link" href="#office-invoice-tab" aria-controls='office-invoice-tab' role='tab' data-bs-toggle='tab'>{!!trans('Office Invoice')!!}</a></li>
                    
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade show active" role='tabpanel' id='info-tab'>
					<div class="row">
					<div class="col-lg-6">

                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <input id="office_id" type="hidden" name="office_id" value = {{$offices->id}}>
                        <tr>
                            <td>
                                <b><i>{!!trans('Office Name')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $offices->office_name }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Office Address')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $offices->office_address }}</td>
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
                            <td class="info_td_show">{{ $total_tour_expense }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Utility Expenses')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $total_utility_expense }}</td>
                        </tr>
						<tr>
                            <td>
                                <b><i>{!!trans('Employee Salaries')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $total_employee_salary }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Office Earnings')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $total_office_earning }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Total B.Amount')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $total_balance }}</td>
                        </tr>
                        </tbody>
                    </table>
								</div>
								</div>
					
                    <div class="office-create-control" style="float: right;margin:50px 0px 50px 0px">
                        @can('tour_expenses.create')<a class="btn btn-primary" href="{{ url('tour_expenses/create/'.$offices->id) }}">New</a>@endcan
                    </div>
					<h3 style=" margin:50px 0px 50px 0px">{{ trans('TOUR EXPENSES') }}</h3>

					<div class="table-responsive">
						@include('component.table_tools', ['tableId' => 'tour-expenses-table', 'label' => 'tour expenses'])
                        <table id="tour-expenses-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th>id <i class="fa fa-sort"></i></th>
									<th>Tour Name <i class="fa fa-sort"></i></th>
									<th>Tour Expense <i class="fa fa-sort"></i></th>
									<th>Departure Date <i class="fa fa-sort"></i></th>
									<th>Return Date <i class="fa fa-sort"></i></th>
									<th class="actions-button">Actions</th>
								</tr>
							</thead>
							<tbody>
							@forelse($tour_expenses as $expense)
								<tr>
									<td>{{ $expense->id }}</td>
									<td>{{ $expense->tour_name }}</td>
									<td>{{ $expense->tour_expenses }}</td>
									<td>{{ $expense->tour_departure_date }}</td>
									<td>{{ $expense->tour_return_date }}</td>
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
                    <div class="office-create-control" style="float: right; margin:50px 0px 50px 0px">
                        @can('utility_expenses.create')<a class="btn btn-primary" href="{{ url('utility_expenses/create/'.$offices->id) }}">New</a>@endcan
                    </div>
					<h3  style=" margin:50px 0px 50px 0px">{{ trans('UTILITY EXPENSES') }}</h3>

					<div class="table-responsive">
						@include('component.table_tools', ['tableId' => 'utility-expenses-table', 'label' => 'utilities'])
                        <table id="utility-expenses-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th>id <i class="fa fa-sort"></i></th>
									<th>Subject <i class="fa fa-sort"></i></th>
									<th>Month <i class="fa fa-sort"></i></th>
									<th>Monthly Expense <i class="fa fa-sort"></i></th>
									<th class="actions-button">Actions</th>
								</tr>
							</thead>
							<tbody>
							@forelse($utility_expenses as $utility)
								<tr>
									<td>{{ $utility->id }}</td>
									<td>{{ $utility->subject_of_expense }}</td>
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
            <div class="office-create-control" style="float: right; margin:50px 0px 50px 0px">
                @can('employes-salary.create')<a class="btn btn-primary" href="{{ url('employes-salary/create/'.$offices->id) }}">New</a>@endcan
            </div>
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('EMPLOYEE SALARY') }}</h3>

					<div class="table-responsive">
						@include('component.table_tools', ['tableId' => 'employee-salary-table', 'label' => 'salaries'])
                        <table id="employee-salary-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th>id <i class="fa fa-sort"></i></th>
									<th>Name <i class="fa fa-sort"></i></th>
									<th>Salary <i class="fa fa-sort"></i></th>
									<th>Month <i class="fa fa-sort"></i></th>
									<th>Bonuses <i class="fa fa-sort"></i></th>
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
			
			<div class="office-create-control" style="float: right; margin:50px 0px 50px 0px">
                @can('office_earning.create')<a class="btn btn-primary" href="{{ url('office_earning/create/'.$offices->id) }}">New</a>@endcan
            </div>		
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('OFFICE EARNINGS') }}</h3>

					<div class="table-responsive">
						@include('component.table_tools', ['tableId' => 'office-earnings-table', 'label' => 'earnings'])
                        <table id="office-earnings-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th>id <i class="fa fa-sort"></i></th>
									<th>Month <i class="fa fa-sort"></i></th>
									<th>Revenue <i class="fa fa-sort"></i></th>
									<th>Profit <i class="fa fa-sort"></i></th>
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
                @can('office_balance.create')<a class="btn btn-primary" href="{{ url('office_balance/create/'.$offices->id) }}">New</a>@endcan
            </div>
			<h3 style=" margin:50px 0px 50px 0px">{{ trans('OFFICE BALANCES') }}</h3>

					<div class="table-responsive">
						@include('component.table_tools', ['tableId' => 'office-balances-table', 'label' => 'balances'])
                        <table id="office-balances-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%;'>
							<thead>
								<tr>
									<th>id <i class="fa fa-sort"></i></th>
									<th>Subject <i class="fa fa-sort"></i></th>
									<th>Month <i class="fa fa-sort"></i></th>
									<th>Total Amount <i class="fa fa-sort"></i></th>
									<th>Due Date <i class="fa fa-sort"></i></th>
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
					@include('component.table_tools', ['tableId' => 'officesinvoice-table', 'label' => 'office invoices'])
                        <table id="officesinvoice-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
						<thead>
							<tr>
								<th>id <i class="fa fa-sort"></i></th>
								<th>Office Name <i class="fa fa-sort"></i></th>
								<th>Office date <i class="fa fa-sort"></i></th>
								<th>Invoice No <i class="fa fa-sort"></i></th>
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

@include('component.delete_modal_simple')
