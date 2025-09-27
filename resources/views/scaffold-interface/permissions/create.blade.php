@extends('scaffold-interface.layouts.app')
@section('content')
	@include('layouts.title',
   ['title' => 'Permission', 'sub_title' => 'Permission Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Permissions', 'icon' => 'key', 'route' => url('permissions')],
   ['title' => 'Create', 'route' => null]]])
<section class="content">
	<div class="box box-primary">
		<div class="box box-body border_top_none">
			@if (count($errors) > 0)
				<br>
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			<form action="{{url('permissions/store')}}" method="post">
				<div class="row">
					<div class="col-md-12">
						<div class="margin_button">
							<a href="javascript:history.back()">
								<button type="button" class='btn btn-primary back_btn'>{{trans('main.Back')}}</button>
							</a>
							<button class='btn btn-success pre-loader-func' type='submit'>{{trans('main.Save')}}</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						{!! csrf_field() !!}
						<div class="form-group {{$errors->has('name') ? 'has-error' : ''}}">
							<label for="name">{{trans('main.Permission')}}</label>
							<input type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}">
							@if($errors->has('name'))
								<span class="help-block">
									<strong>{{$errors->first('name')}}</strong>
								</span>
							@endif
						</div>
						<div class="form-group {{$errors->has('alias') ? 'has-error' : ''}}">
							<label for="alias">{{trans('main.Alias')}}</label>
							<input type="text" name="alias" class="form-control" placeholder="Alias" value="{{ old('alias') }}">
							@if($errors->has('alias'))
								<span class="help-block">
									<strong>{{$errors->first('alias')}}</strong>
								</span>
							@endif
						</div>
						<button class='btn btn-success pre-loader-func' type="submit">{{trans('main.Save')}}</button>
						<a href="{{ url('permissions') }}">
							<button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
@endsection