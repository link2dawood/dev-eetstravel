@extends('scaffold-interface.layouts.app')
@section('content')
	@include('layouts.title',
   ['title' => 'Role', 'sub_title' => 'Role Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Roles', 'icon' => 'user-plus', 'route' => url('roles')],
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

			<form action="{{url('roles/store')}}" method = "post">
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
						<div class="form-group">
							<label for="">{{trans('main.Role')}}</label>
							<input type="text" name = "name" class = "form-control" placeholder = "Name">
						</div>
						<button class = 'btn btn-success pre-loader-func' type = "submit">{{trans('main.Save')}}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
@endsection
