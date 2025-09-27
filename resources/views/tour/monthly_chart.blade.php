@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Monthly Chart', 'sub_title' => 'Tours List',
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
				
				<div class="tab-content">
				
				<div class="row mb-3">
					<div class="col-md-3">
						<label for="year-filter">Filter by Year:</label>
						<select id="year-filter" class="form-control">
							<option value="">All Years</option>
							@foreach ($years as $year)
								<option value="{{ $year }}">{{ $year }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<label for="month-filter">Filter by Month:</label>
						<select id="month-filter" class="form-control">
							<option value="">All Months</option>
							@foreach ($months as $key => $month)
								<option value="{{ $key }}">{{ $month }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4">
						<label for="tour-search">Search:</label>
						<input type="text" id="tour-search" class="form-control" placeholder="Search tours..." onkeyup="filterTable('monthly-chart-table', this.value)">
					</div>
					<div class="col-md-2">
						<label>&nbsp;</label>
						<div>
							<button class="btn btn-success btn-sm" onclick="exportTableToCSV('monthly-chart-table', 'tours_export.csv')">
								<i class="fa fa-download"></i> Export CSV
							</button>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table id="monthly-chart-table" class="table table-striped table-bordered table-hover bootstrap-table">
						<thead>
						  <tr>
							<th onclick="sortTable(0, 'monthly-chart-table')" style='width: 30px!important;'>ID <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(1, 'monthly-chart-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(2, 'monthly-chart-table')">{!!trans('main.DepDate')!!} <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(3, 'monthly-chart-table')">{!!trans('main.CountryBegin')!!} <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(4, 'monthly-chart-table')">{!!trans('main.CityBegin')!!} <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(5, 'monthly-chart-table')">{!!trans('main.Status')!!} <i class="fa fa-sort"></i></th>
							<th onclick="sortTable(6, 'monthly-chart-table')">{!!trans('main.ExternalName')!!} <i class="fa fa-sort"></i></th>
							<th style="width:140px; text-align: center;">{!!trans('main.Actions')!!}</th>
						  </tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="8" class="text-center">Loading...</td>
							</tr>
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
@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    let permission = $('#permission').attr('data-permission');
    let classNameStatus = permission ? 'touredit-status' : '';

    initializeBootstrapTable('monthly-chart-table');

    function loadTourData() {
        const year = $('#year-filter').val();
        const month = $('#month-filter').val();

        $.get("{{route('monthly_chart_data')}}", { year: year, month: month })
        .done(function(data) {
            const tbody = $('#monthly-chart-table tbody');
            tbody.empty();

            if(data.data && data.data.length > 0) {
                data.data.forEach(function(row) {
                    let rowClass = '';
                    switch(row.status_name) {
                        case 'Pending':
                            rowClass = 'style="background: rgb(255 249 176)"';
                            break;
                        case 'Cancelled':
                            rowClass = 'style="background: #ffbbb2"';
                            break;
                        case 'Confirmed':
                            rowClass = 'style="background: rgb(159 255 135)"';
                            break;
                        default:
                            rowClass = 'style="background: rgb(202 255 189)"';
                            break;
                    }

                    let statusCellClass = permission ? 'touredit-status' : '';
                    let statusCellAttr = permission ? `data-status-link="{{ route('tour.update', ['tour' => '__ID__']) }}".replace('__ID__', '${row.id}')` : '';

                    tbody.append(`
                        <tr ${rowClass}>
                            <td>${row.id}</td>
                            <td class="touredit-name">${row.name}</td>
                            <td class="touredit-departure_date">${row.departure_date}</td>
                            <td class="touredit-country_begin">${row.country_begin}</td>
                            <td class="touredit-city_begin">${row.city_begin}</td>
                            <td class="${statusCellClass}" ${statusCellAttr}>${row.status_name}</td>
                            <td class="touredit-external_name">${row.external_name}</td>
                            <td>${row.action}</td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="8" class="text-center">No tours found</td></tr>');
            }
        })
        .fail(function() {
            $('#monthly-chart-table tbody').html('<tr><td colspan="8" class="text-center">Error loading data</td></tr>');
        });
    }

    // Load initial data
    loadTourData();

    // Reload data when filters change
    $('#year-filter, #month-filter').on('change', function() {
        loadTourData();
    });
});
</script>
@endpush
