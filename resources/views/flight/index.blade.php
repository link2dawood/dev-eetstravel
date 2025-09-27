@extends('scaffold-interface.layouts.app')
@section('title', 'Flight')
@section('content')
	@include('layouts.title',
   ['title' => 'Flights', 'sub_title' => 'Flights List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Flights', 'icon' => 'plane', 'route' => null]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
					<div>
						{!! \App\Helper\PermissionHelper::getCreateButton(route('flights.create'), \App\Flight::class) !!}
					</div>
				@if(session('export_all'))
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    {{session('export_all')}}
                </div>
                @endif
				<br>
				<br>
				<!-- Date Filter Form -->
				<div class="row mb-3">
					<div class="col-md-12">
						<form id="search-form" method="GET" action="{{ route('flights.index') }}">
							<div class="row">
								<div class="col-md-3">
									<label for="date_from">Date From:</label>
									<input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
								</div>
								<div class="col-md-3">
									<label for="date_to">Date To:</label>
									<input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
								</div>
								<div class="col-md-3">
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary form-control">Filter</button>
								</div>
								<div class="col-md-3">
									<label>&nbsp;</label>
									<a href="{{ route('flights.index') }}" class="btn btn-secondary form-control">Clear</a>
								</div>
							</div>
						</form>
					</div>
				</div>
				<br>

				<table class="table table-striped table-bordered table-hover" style='background:#fff; width: 98%; table-layout: fixed'>
					<thead>
					<tr>
						<th>Id</th>
						<th>{!!trans('main.Name')!!}</th>
						<th>{!!trans('main.CountryFrom')!!}</th>
						<th>{!!trans('main.Cityfrom')!!}</th>
						<th>{!!trans('main.Countryto')!!}</th>
						<th>{!!trans('main.Cityto')!!}</th>
						<th class="actions-button" style="width: 140px; ">{!!trans('main.Actions')!!}</th>
					</tr>
					</thead>
					<tbody>
						@forelse($flights ?? [] as $flight)
						<tr>
							<td>{{ $flight->id }}</td>
							<td>{{ $flight->name ?? '' }}</td>
							<td>{{ $flight->country_from ?? '' }}</td>
							<td>{{ $flight->city_from ?? '' }}</td>
							<td>{{ $flight->country_to ?? '' }}</td>
							<td>{{ $flight->city_to ?? '' }}</td>
							<td>{!! $flight->action_buttons ?? '' !!}</td>
						</tr>
						@empty
						<tr>
							<td colspan="7" class="text-center">No flights found</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</section>
	<span id="service-name" hidden data-service-name='Flight'></span>
@endsection
