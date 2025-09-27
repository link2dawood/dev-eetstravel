@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
	@include('layouts.title',
   ['title' => 'Cruise', 'sub_title' => 'Cruise Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Cruises', 'icon' => 'ship', 'route' => route('cruises.index')],
   ['title' => 'Create', 'route' => null]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box box-body border_top_none">
				<form method='POST' action='{{route('cruises.store')}}' enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-12">
							<div class="margin_button">
								<a href="javascript:history.back()">
									<button type="button" class='btn btn-primary back_btn'>{!!trans('main.Back')!!}</button>
								</a>
								<button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">

								{{csrf_field()}}

								<div class="form-group {{$errors->has('name') ? 'has-error' : ''}}">
									<label for="name">{!!trans('main.Name')!!}</label>
									<input id="name" name="name" type="text" class="form-control" value="{{old('name')}}">
									@if($errors->has('name'))
										<strong>{{$errors->first('name')}}</strong>
									@endif
								</div>



							@component('component.city_form', ['country_label' => 'country_from', 'country_translation' => 'main.CountryFrom', 'country_default' =>0,
                                                                'city_label' => 'city_from','city_translation' =>'main.Cityfrom', 'city_default' => 0])
							@endcomponent
							@component('component.city_form', ['country_label' => 'country_to', 'country_translation' => 'main.CountryTo', 'country_default' => 0,
                                                                'city_label' => 'city_to','city_translation' =>'main.CityTo', 'city_default' => 0])
							@endcomponent
								<div class="form-group col-md-6 col-lg-6" style="padding-left: 0">
									<label for="departure_date">{!!trans('main.DateFrom')!!}</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('from_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'from_date']) !!}
									</div>
								</div>

								<div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
									<label for="departure_date">{!!trans('main.TimeFrom')!!}</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-clock-o"></i>
										</div>
										{!! Form::text('from_time', '12:00', ['class' => 'form-control pull-right timepicker', 'id' => 'from_time']) !!}
									</div>
								</div>

								<div class="form-group col-md-6 col-lg-6" style="padding-left: 0">
									<label for="departure_date">{!!trans('main.DateTo')!!}</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('to_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'to_date']) !!}
									</div>
								</div>

								<div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
									<label for="departure_date">{!!trans('main.TimeTo')!!}</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-clock-o"></i>
										</div>
										{!! Form::text('to_time', '13:00', ['class' => 'form-control pull-right timepicker', 'id' => 'to_time']) !!}
									</div>
								</div>

								<div class="form-group col-md-12" style="padding: 0">
									<input type="text" hidden name="date_from">
									@if($errors->has('date_from'))
										<strong>{{$errors->first('date_from')}}</strong>
									@endif
								</div>

								<div class="form-group">
									<label for="address_first">{!!trans('main.AddressFirst')!!}</label>
									<input id="address_first" name="address_first" type="text" class="form-control" value="{{old('address_first')}}">
								</div>
								<div class="form-group">
									<label for="address_second">{!!trans('main.AddressSecond')!!}</label>
									<input id="address_second" name="address_second" type="text" class="form-control" value="{{old('address_second')}}">
								</div>
								<div class="form-group">
									<label for="code">{!!trans('main.Code')!!}</label>
									<input id="code" name="code" type="text" class="form-control" value="{{old('code')}}">
								</div>
								<div class="form-group">
									<label for="work_phone">{!!trans('main.WorkPhone')!!}</label>
									<input id="work_phone" name="work_phone" type="text" class="form-control" value="{{old('work_phone')}}">
								</div>
								<div class="form-group">
									<label for="work_fax">{!!trans('main.WorkFax')!!}</label>
									<input id="work_fax" name="work_fax" type="text" class="form-control" value="{{old('work_fax')}}">
								</div>
								<div class="form-group">
									<label for="work_email">{!!trans('main.WorkEmail')!!}</label>
									<input id="work_email" name="work_email" type="text" class="form-control" value="{{old('work_email')}}">
								</div>
								<div class="form-group">
									<label for="int_comments">{!!trans('main.IntComments')!!}</label>
									<input id="int_comments" name="int_comments" type="text" class="form-control" value="{{old('int_comments')}}">
								</div>
								<div class="form-group">
									<label for="comments">{!!trans('main.Comments')!!}</label>
									<input id="comments" name="comments" type="text" class="form-control" value="{{old('comments')}}">
								</div>
								<div class="form-group">
									<label for="contact_name">{!!trans('main.ContactName')!!}</label>
									<input id="contact_name" name="contact_name" type="text" class="form-control" value="{{old('contacts_name')}}">
								</div>
								<div class="form-group">
									<label for="contact_phone">{!!trans('main.ContactPhone')!!}</label>
									<input id="contact_phone" name="contact_phone" type="text" class="form-control" value="{{old('contact_phone')}}">
								</div>
								<div class="form-group">
									<label for="website">{!!trans('main.Website')!!}</label>
									<input id="website" name="website" type="text" class="form-control" value="{{old('website')}}">
								</div>
								<div class="form-group">
									<label for="criteria">{!!trans('main.Criteria')!!}</label>
								</div>
								@foreach($criterias as $criteria)
									<div class="form-group criteria_block">
										<input type="checkbox" value="{{ $criteria->id }}" name="criterias">
										<label for="">{{ $criteria->name }}</label>
									</div>
								@endforeach

								<div class="form-group">
									<label for="rate">{!!trans('main.Rate')!!}</label>
									<select name="rate" id="rate" class="form-control">
										@foreach($rates as $rate)
											<option {{ old('rate') == $rate->id ? 'selected' : '' }} value="{{ $rate->id }}">{{ $rate->name }}</option>
										@endforeach
									</select>
								</div>
								<input type="text" hidden name="place_id" id="place_id">
								<div class="form-group">
									<label>{!!trans('main.Files')!!}</label>
									@component('component.file_upload_field')@endcomponent
								</div>
								<button class="btn btn-success" type="submit">{!!trans('main.Save')!!}</button>

						</div>
						<div class="col-md-6">
							<span id="page" data-page="create"></span>
							<button class="btn btn-primary" id="btn_generate_map">{!!trans('main.GenerateLocation')!!}</button>
							<button class="btn btn-primary btn_google_maps" id="btn_select_location">{!!trans('main.SelectLocation')!!}</button>
							<br>
							<span id="error_map"></span>
							<div class="block_map">
								<div id="map"></div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
	@endsection

@push('scripts')
	<script src="{{ asset('js/google_map_flights_and_cruise.js') }}"></script>
@endpush
