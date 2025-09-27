@extends('scaffold-interface.layouts.app')
@section('content')
	@include('layouts.title',
   ['title' => 'Permission', 'sub_title' => 'Permission Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Permissions', 'icon' => 'key', 'route' => url('permissions')],
   ['title' => 'Edit', 'route' => null]]])
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
			<form action="{{url('permissions/update')}}" method="post">
				<div class="row">
					<div class="col-md-12">
						<div class="margin_button">
							<a href="javascript:history.back()">
								<button class='btn btn-primary back_btn' type="button">{{trans('main.Back')}}</button>
							</a>
							<button class='btn btn-success' type='submit'>{{trans('main.Save')}}</button>
						</div>
					</div>
				</div>
				{!! csrf_field() !!}
				<input type="hidden" name="permission_id" value="{{$permission->id}}">
				<div class="form-group {{$errors->has('name') ? 'has-error' : ''}}">
					<label for="name">{{trans('main.Name')}}</label>
					<input type="text" name="name" class="form-control" placeholder="Name" 
						value="{{ $errors != null && count($errors) > 0 ? old('name') : $permission->name }}">
					@if($errors->has('name'))
						<span class="help-block">
							<strong>{{$errors->first('name')}}</strong>
						</span>
					@endif
				</div>
				<div class="form-group {{$errors->has('alias') ? 'has-error' : ''}}">
					<label for="alias">{{trans('main.Alias')}}</label>
					<input type="text" name="alias" class="form-control" placeholder="Alias" 
						value="{{ $errors != null && count($errors) > 0 ? old('alias') : $permission->alias }}">
					@if($errors->has('alias'))
						<span class="help-block">
							<strong>{{$errors->first('alias')}}</strong>
						</span>
					@endif
				</div>
				<button class='btn btn-success' type="submit">{{trans('main.Save')}}</button>
				<a href="{{ url('permissions') }}">
					<button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
				</a>
			</form>
		</div>
	</div>
</section>
@endsection