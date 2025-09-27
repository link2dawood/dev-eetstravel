@extends('scaffold-interface.layouts.app')
@section('content')
@include('layouts.title', ['title' => 'Supplier Search', 'sub_title' => 'global', 'breadcrumbs' => null])
<div class="modal fade" id="addTourModal" tabindex="-1" aria-labelledby='addTourLabel' style="padding-left: 17px;padding-right: 17px;">
	<div class="modal-dialog modal-lg" role='document' style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
				<h4 class="modal-title" id="addTourLabel">{!!trans('main.AddforTour')!!}</h4>
			</div>
			<div class="modal-body">
				<table id="tour-table" class="table table-hover table-bordered table-striped" width="100%">
					<thead>
						<tr>
							<th>Id</th>
							<th>{!!trans('main.Name')!!}</th>
							<th>{!!trans('main.DepDate')!!}</th>
							<th>{!!trans('main.Retdate')!!}</th>
							<th>Pax</th>
							<th>{!!trans('main.Choose')!!}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<section class="content">
	<div class="box box-primary">
		<div class="box-body">
		    <div class="col-md-12">
		    	<form action="{{route('supplier_show')}}">
		    		<div class="form-group col-md-5">
                            <div class="form-group">
                                <input type='text' class="form-control" id='searchTextField' placeholder="Name" value=''>
                            </div>
                            <div class="form-group">
                                <select id="service-select" class="form-control">
                                    <option selected>{!!trans('main.All')!!}</option>
                                        @foreach($options as $option)
										<option>@if($option ==='Transfer') Bus Company @else {{$option}} @endif</option>
									@endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                {!! Form::select('country', \App\Helper\Choices::getCountriesSupplierSearchArray(), '', ['class' => 'form-control', 'id' => 'country']) !!}
                            </div>
                            <div class="form-group">
                                <input id="city" name="city" type="text" class="form-control"
                                       value="" placeholder="City">
                                <input type="hidden" name="city_code" id="city_code"
                                       value="">
                            </div>
                            <input type='button' id='supplierSearchButton' class="btn" value='Search' style="background-color:#3c8dbc; color:#fff"/>                        
		    		</div>
					<span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
						@include('legend.supplier_search')
                    </span>
			    	<div class="filters col-md-7">
			    		
			    	</div>
		    	</form>
		    </div>


			<div class="alert alert-info block-error-driver-transfer" style="text-align: center; display: none; overflow: hidden">

			</div>

			<div class="col-md-12" style="margin-top: 20px">
    <table id="search-table" class="table table-bordered table-striped table-hover" width="100%">
        <thead>
            <tr>
                <th>{!! trans('main.Name') !!}</th>
                <th>{!! trans('main.Address') !!}</th>
                <th>{!! trans('main.Country') !!}</th>
                <th>{!! trans('main.City') !!}</th>
                <th>{!! trans('main.Phone') !!}</th>
                <th>{!! trans('main.ContactName') !!}</th>
                <th class="actions-button"></th>
            </tr>
        </thead>
        <tbody>
            {{-- Leave empty → DataTables / Ajax will fill dynamically --}}
        </tbody>
    </table>
</div>
		</div>
	</div>
</section>


<div class="modal fade" id="selectDateForTour" tabindex="-1" aria-labelledby='selectDateForTourLabel'>
	<div class="modal-dialog modal-lg" role='document'>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
				<h4 class="modal-title" id="addTourLabel">{!!trans('main.SelectDate')!!}</h4>
			</div>
			<div class="box box-body" style="border-top: none">

				<div class="alert alert-info error_date" style="text-align: center; display: none;">

				</div>

				<div class="form-group">

					<label for="departure_date">{!!trans('main.DateFrom')!!}</label>

					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						{!! Form::text('date_service', '', ['class' => 'form-control pull-right datepickerDisabled',
						 'id' => 'date_service']) !!}
					</div>

				</div>

				<div class="form-group">

					<label for="departure_date">{!!trans('main.DateTo')!!}</label>

					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						{!! Form::text('date_service_retirement', '', [
						'class' => 'form-control pull-right datepickerDisabled',
						'id' => 'date_service_retirement',
						'disabled' => true
						]) !!}
					</div>

				</div>
				<div style="overflow: hidden; display:  block">
					<button class="addTourWithDate btn btn-success pull-right pre-loader-func" type="button">{!!trans('main.Add')!!}</button>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" tabindex="-1" id="select-driver-and-bus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="form_transfer_buses_drivers">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
					<h4 class="modal-title">{!!trans('main.Selectdriversandbuses')!!}</h4>
					<div class="alert alert-info block-error-driver" style="text-align: center; display: none;">

					</div>
				</div>
				<div class="box box-body" style="border-top: none">
					<div class="list-driver-and-buses"></div>

					<div class="overlay" style="display: none">
						<i class="fa fa-refresh fa-spin"></i>
					</div>

					<div class="modal-footer">
						<div class="btn-send-driver">
							<button type="button" class="btn btn-success btn-send-transfer_add">{!!trans('main.Add')!!}</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


{{-- Transfer Modal Date--}}
<div class="modal fade" id="selectDateForTransferPackage" tabindex="-1" aria-labelledby='selectDateForTransferPackageLabel'>
	<div class="modal-dialog modal-lg" role='document'>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
				<h4 class="modal-title">{!!trans('main.SelectDate')!!}</h4>
			</div>
			<div class="modal-body">

				<div class="alert alert-info error_date" style="text-align: center; display: none;">

				</div>

				<div class="form-group">

					<label for="departure_date">{!!trans('main.DateFrom')!!}</label>

					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						{!! Form::text('date_service_package', '', ['class' => 'form-control pull-right datepickerDisabledTransferPackage',
                         'id' => 'date_service_transfer_package']) !!}
					</div>

				</div>

				<div class="form-group">

					<label for="departure_date">{!!trans('main.DateTo')!!}</label>

					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						{!! Form::text('date_service_retirement_package', '', [
                        'class' => 'form-control pull-right datepickerDisabledTransferPackage',
                        'id' => 'date_service_transfer_retirement_package'
                        ]) !!}
					</div>

				</div>
				<div style="overflow: hidden; display: block">
					<button class="addTransferPackageWithDate btn btn-success pull-right" type="button">{!!trans('main.Next')!!}</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@push('scripts')
	<script type="text/javascript">
		$(document).ready(setTimeout(focusSearch, 1000));

		function focusSearch (){
            $('#searchTextField').focus();            
		}
		
		
	</script>
<script type="text/javascript" src="{{asset('js/supplier-search.js')}}"></script>
<script type="text/javascript">
	globalSearchApp.run();
</script>
@endpush