@extends('scaffold-interface.layouts.tabler-app')
@section('content')
	@include('layouts.title',
   ['title' => 'User', 'sub_title' => 'User Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Users', 'icon' => 'user', 'route' => url('users')],
   ['title' => 'Create', 'route' => null]]])
<style>
    .manage-form-actions-top {
        margin-bottom: 1.5rem;
    }

    .manage-form-actions-bottom {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.75rem;
        margin-bottom: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
</style><section class="content">
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
			<form action="{{route('users.store')}}" method = "post" enctype="multipart/form-data">
				<div class="row manage-form-actions-top">
					<div class="col-md-12">
						<a href="javascript:history.back()" class="btn btn-primary back_btn">
							<i class="ti ti-arrow-left me-1"></i>{{trans('main.Back')}}
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						{!! csrf_field() !!}
						<input type="hidden" name = "user_id">
						<div class="form-group">
							<label for="">{{trans('main.Email')}}</label>
							<input type="text" name = "email" value="{{ old('email') }}" class = "form-control" placeholder = "Email">
						</div>
						<div class="form-group">
							<label for="">{{trans('main.Name')}}</label>
							<input type="text" name = "name" value="{{ old('name') }}" class = "form-control" placeholder = "Name">
						</div>
						<div class="form-group">
							<label for="">{{trans('main.Password')}}</label>
							<input type="password" name = "password" class = "form-control" placeholder = "Password">
						</div>
						<div class="form-group">
							<label for="avatar">{{trans('main.Logo')}}</label>
							<input id="avatar" name="avatar" type="file" class="file" data-show-upload="false" >
						</div>
						<div class="manage-form-actions-bottom">
							<a href="{{ url('users') }}" class="btn btn-secondary">
								<i class="ti ti-x me-1"></i>{{trans('main.Cancel')}}
							</a>
							<button class="btn btn-success pre-loader-func" type="submit">
								<i class="ti ti-device-floppy me-1"></i>{{trans('main.Save')}}
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
@endsection

