@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Tours', 'sub_title' => 'Tours List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
					<div id = "tour_create">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class) !!}
					</div>
                    <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        @include('legend.tour_legend')
                    </span>
                </div>
				<br>
				<div id="fixed-scroll" class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role='tablist'>

                        <li role='presentation' class="active"><a href="#tour_tab" aria-controls='tour_tab' role='tab'
                                                   data-toggle='tab' >{{trans('main.Tours')}}</a></li>
						<li role='presentation' ><a href="#client_tour_tab" aria-controls='client_tour_tab'
                                                                  role='tab' data-toggle='tab' >{{trans('Requested Tours')}}</a></li>
						<li role='presentation' ><a href="#monthly_chart_tab" aria-controls='monthly_chart_tab'
                                                                  role='tab' data-toggle='tab' >{{trans('Monthly Chart')}}</a></li>
						<li role='presentation' ><a href="#archieve_tours_tab" aria-controls='monthly_chart_tab'
                                                                  role='tab' data-toggle='tab' >{{trans('Archived Tours')}}</a></li>
                    </ul>
                </div>
				<div class="tab-content">
					<div role='tabpanel' class="tab-pane fade in active" id="tour_tab">
				<div class="toggle" style="float:right">
					<select id="filterDropdown" class="form-control">
						<option value="">All</option>
						<option value="quotations">Quotations</option>
						<option value="go_ahead">Go Ahead</option>
					</select>

            	</div>
                @if(session('message_buses'))
                    <div class="alert alert-info col-md-12" style="text-align: center;">
                        {{session('message_buses')}}
                    </div>
                @endif
                <br>
                <br>

                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="tour-search" class="form-control" placeholder="Search tours..." onkeyup="filterTable('tour-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('tour-table', 'tours_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                <table id="tour-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
                    <thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th>{!!trans('main.Name')!!}</th>
                        <th>{!!trans('main.DepDate')!!}</th>
						<th>{!!trans('Responsible Users')!!}</th>
						 <th>{!!trans('Assigned Users')!!}</th>
                        <th>{!!trans('main.Status')!!}</th>
                        <th>{!!trans('main.ExternalName')!!}</th>
                        <th class="actions-button" style="width:140px; text-align: center;">{!!trans('main.Actions')!!}</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($tours as $tour)
                        <tr style="background: {{ $tour->getRowBackgroundColor() }}; cursor: pointer;"
                            onclick="window.location='{{ route('tour.show', ['tour' => $tour->id]) }}'"
                            title="Click to view tour details">
                            <td>{{ $tour->id }}</td>
                            <td>{{ $tour->name }}</td>
                            <td>{{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '' }}</td>
                            <td>{{ $tour->responsible_user_names ?? '' }}</td>
                            <td>{{ $tour->assigned_user_names ?? '' }}</td>
                            <td>
                                <span class="label" style="background-color: {{ $tour->getStatusColor() }}">
                                    {{ $tour->getStatusName() }}
                                </span>
                            </td>
                            <td>{{ $tour->external_name }}</td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
			<div role='tabpanel' class="tab-pane fade" id="client_tour_tab">
				<div class="table-responsive">
				<table id="client-tour-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
					<thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th>{!!trans('main.Name')!!}</th>
						<th>{!!trans('Client Name')!!}</th>
                        <th>{!!trans('main.DepDate')!!}</th>
                        <th>{!!trans('main.Status')!!}</th>
                        <th>{!!trans('main.ExternalName')!!}</th>
                        <th class="actions-button" style="width:140px; text-align: center;">{!!trans('main.Actions')!!}</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($clientTours as $tour)
                        <tr style="background: {{ $tour->getRowBackgroundColor() }}; cursor: pointer;"
                            onclick="window.location='{{ route('tour.show', ['tour' => $tour->id]) }}'"
                            title="Click to view tour details">
                            <td>{{ $tour->id }}</td>
                            <td>{{ $tour->name }}</td>
                            <td>{{ $tour->client_name ?? '' }}</td>
                            <td>{{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '' }}</td>
                            <td>
                                <span class="label" style="background-color: {{ $tour->getStatusColor() }}">
                                    {{ $tour->getStatusName() }}
                                </span>
                            </td>
                            <td>{{ $tour->external_name }}</td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
				</table>
			</div>
			</div>
			<div role='tabpanel' class="tab-pane fade" id="monthly_chart_tab">
				<div class="toggle" style="float:right">
					<select id="year-filter" class="form-control">
						<option value="">All Years</option>
						@foreach ($years as $year)
							<option value="{{ $year }}">{{ $year }}</option>
						@endforeach
					</select>
				</div>
				<div class="toggle" style="float:right; margin:0 20px 0 0">
					<select id="month-filter" class="form-control">
						<option value="">All Months</option>
						@foreach ($months as $key => $month)
							<option value="{{ $key }}">{{ $month }}</option>
						@endforeach
					</select>
				</div>
				<h1>On Going Projects</h1>
				<div class="table-responsive">
				<table id="monthly-chart-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
					<thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th>{!!trans('main.Name')!!}</th>
						  <th>{!!trans('Responsible Users')!!}</th>
                        <th>{!!trans('main.Status')!!}</th>
                        <th>{!!trans('main.ExternalName')!!}</th>
                        <th class="actions-button" style="width:30px; text-align: center;">{!!trans('main.Actions')!!}</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyChartTours as $tour)
                        <tr style="background: {{ $tour->getRowBackgroundColor() }}; cursor: pointer;"
                            onclick="window.location='{{ route('tour.show', ['tour' => $tour->id]) }}'"
                            title="Click to view tour details">
                            <td>{{ $tour->id }}</td>
                            <td>{{ $tour->name }}</td>
                            <td>{{ $tour->responsible_user_names ?? '' }}</td>
                            <td>
                                <span class="label" style="background-color: {{ $tour->getStatusColor() }}">
                                    {{ $tour->getStatusName() }}
                                </span>
                            </td>
                            <td>{{ $tour->external_name }}</td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
				</table>
					</div>
				<h1>Cancelled Projects</h1>
					<div class="table-responsive">
				<table id="cancelled-chart-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
					<thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th>{!!trans('main.Name')!!}</th>
						 <th>{!!trans('Responsible Users')!!}</th>
                        <th>{!!trans('main.Status')!!}</th>
                        <th>{!!trans('main.ExternalName')!!}</th>
                        <th class="actions-button" style="width:30px; text-align: center;">{!!trans('main.Actions')!!}</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($cancelledChartTours as $tour)
                        <tr style="background: {{ $tour->getRowBackgroundColor() }}; cursor: pointer;"
                            onclick="window.location='{{ route('tour.show', ['tour' => $tour->id]) }}'"
                            title="Click to view tour details">
                            <td>{{ $tour->id }}</td>
                            <td>{{ $tour->name }}</td>
                            <td>{{ $tour->responsible_user_names ?? '' }}</td>
                            <td>
                                <span class="label" style="background-color: {{ $tour->getStatusColor() }}">
                                    {{ $tour->getStatusName() }}
                                </span>
                            </td>
                            <td>{{ $tour->external_name }}</td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
				</table>
						</div>

			</div>
			<div role='tabpanel' class="tab-pane fade" id="archieve_tours_tab">
				<div class="table-responsive">
				<table id="archieve-tour-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
					<thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th>{!!trans('main.Name')!!}</th>
						 <th>{!!trans('Responsible Users')!!}</th>
                        <th>{!!trans('main.DepDate')!!}</th>
                        <th>{!!trans('main.Status')!!}</th>
                        <th>{!!trans('main.ExternalName')!!}</th>
                        <th class="actions-button" style=" text-align: center;">{!!trans('main.Actions')!!}</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedTours as $tour)
                        <tr style="background: {{ $tour->getRowBackgroundColor() }}; cursor: pointer;"
                            onclick="window.location='{{ route('tour.show', ['tour' => $tour->id]) }}'"
                            title="Click to view tour details">
                            <td>{{ $tour->id }}</td>
                            <td>{{ $tour->name }}</td>
                            <td>{{ $tour->responsible_user_names ?? '' }}</td>
                            <td>{{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '' }}</td>
                            <td>
                                <span class="label" style="background-color: {{ $tour->getStatusColor() }}">
                                    {{ $tour->getStatusName() }}
                                </span>
                            </td>
                            <td>{{ $tour->external_name }}</td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
				</table>
				</div>
			</div>
        </div>

	</div>

</div>

    </section>
    {{-- <button class='btn btn-default btn-sm' data-toggle='modal' data-target='#tour-clone-modal'><i class='fa fa-plus'></i></button> --}}
<div class="modal fade" id="tour-clone-modal" tabindex="-1" role='dialog' aria-labelledby='tour-clone-label'>
    <div class="modal-dialog" role='document'>
        <div class="modal-content">
            <div class="box box-body" style="border-top: none">
                <div class="alert alert-info block-error" style="text-align: center; display: none;">

                </div>

                <form id="tour-clone-modal-form">
                    <div class="form-group">
                            <label for="departure_date">{!!trans('main.DepartureDate')!!}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                        {!! Form::text('departure_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success pre-loader-func" id="clone_tour_send">{!!trans('main.Submit')!!}</button>
                </form>
            </div>
        </div>
    </div>
</div>


{{--Tour Status Error--}}
<div class="modal fade" tabindex="-1" id="error_tour">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form_confirmed_hotel">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                        <h4 class="modal-title">{!!trans('main.Warning')!!}!</h4>
                    </div>
                    <div class="modal-body">
                        <h3 class="error_tour_message"></h3>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-send-confirmed_hotel">
                            <button type="reset" class="btn btn-success modal-close" data-dismiss="modal">Ok</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>

    <span id="permission" data-permission="{{ \App\Helper\PermissionHelper::checkPermission('tour.edit') }}"></span>

@endsection