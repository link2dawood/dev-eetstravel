@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Profile')
@section('content')
	@include('layouts.title',
   ['title' => 'Profile', 'sub_title' => 'Edit Profile',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Profile', 'icon' => 'user', 'route' => url('profile')],
   ['title' => 'Edit', 'route' => null]]])
   <style>
	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    background: #f5f5f5!important;
    border: none;
    border-right: 1px solid #aaa;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
    color: #999;
    cursor: pointer;
    font-size: 1em;
    font-weight: bold;
    padding: 0 4px;
    position: relative!important;
    left: -5px!important;
    top: 0!important;
}
.select2-container--default .select2-search--inline .select2-search__field {
	position: absolute;
}
   </style>
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
			<form action="{{url('/users/'.$user->id)}}" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-12">
						<div class="margin_button">
							<a href="{{url('profile')}}">
								<button class='btn btn-primary back_btn' type="button">{{trans('main.Back')}}</button>
							</a>
							<button class='btn btn-success' type='submit'>{{trans('main.Save')}}</button>
						</div>
					</div>
				</div>
				{!! csrf_field() !!}
				<input type="hidden" name="user_id" value="{{$user->id}}">
				<input type="hidden" name="edit_profile" value="1">

				<div class="form-group">
					<label for="">{{trans('main.Email')}}</label>
					<input type="email" name="email" value="{{ $errors != null && count($errors) > 0 ? old('email') : $user->email }}" class="form-control" required>
				</div>

				<div class="form-group">
					<label for="">{{trans('main.Name')}}</label>
					<input type="text" name="name" value="{{ $errors != null && count($errors) > 0 ? old('name') : $user->name }}" class="form-control" required>
				</div>

				<div class="form-group">
					<label for="">{{trans('main.Password')}}</label>
					<input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
					<small class="form-text text-muted">Only fill this if you want to change your password</small>
				</div>

                <div class="form-group">
                    <label for="email_login">{{ trans('main.EmailLogin') }} (SnappyMail)</label>
                    <input type="email"
                           name="email_login"
                           id="email_login"
                           value="{{ old('email_login', $user->email_login) }}"
                           class="form-control"
                           placeholder="user@example.com">
                    <small class="form-text text-muted">This login is used to connect the user’s SnappyMail inbox.</small>
                </div>

                <div class="form-group">
                    <label for="email_password">{{ trans('main.Email') }} {{ trans('main.Password') }} (SnappyMail)</label>
                    <input type="password"
                           name="email_password"
                           id="email_password"
                           class="form-control"
                           placeholder="Enter new SnappyMail password">
                    <small class="form-text text-muted">Leave blank to keep the existing SnappyMail password.</small>
                </div>

				<div class="form-group">
					<label for="">{{trans('main.Education')}}</label>
					<input type="text" name="education" value="{{ $errors != null && count($errors) > 0 ? old('education') : $user->education }}" class="form-control" placeholder="Education">
				</div>

				<div class="form-group">
					<label for="">{{trans('main.Location')}}</label>
					<input type="text" name="location" value="{{ $errors != null && count($errors) > 0 ? old('location') : $user->location }}" class="form-control" placeholder="Location">
				</div>

				<div class="form-group">
					<label for="">{{trans('main.Note')}}</label>
					<input type="text" name="note" value="{{ $errors != null && count($errors) > 0 ? old('note') : $user->note }}" class="form-control" placeholder="Note">
				</div>

                <div class="form-group">
                    <label for="">Avatar</label>
                    <div style="margin-bottom: 10px;">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="Current Avatar" style="max-width: 150px; height: auto; border-radius: 50%;">
                        @else
                            <img src="{{ asset('img/avatar.png') }}" alt="Default Avatar" style="max-width: 150px; height: auto; border-radius: 50%;">
                        @endif
                    </div>
                    <input id="avatar" name="avatar" type="file" class="file" data-show-upload="false" accept="image/*">
                    <small class="form-text text-muted">Upload a new avatar image (optional)</small>
                </div>

				<button class="btn btn-success" type="submit">{{trans('main.Save')}}</button>
				<a href="{{url('profile')}}">
					<button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
				</a>
			</form>
		</div>
	</div>

	@if(Auth::user()->hasRole('admin'))
	<div class="row">
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header">
					<h3>{{$user->name}} {{trans('main.Roles')}}</h3>
				</div>
				<div class="box-body">
					<form action="{{url('users/addRole')}}" method="post">
						{!! csrf_field() !!}
						<input type="hidden" name="user_id" value="{{$user->id}}">
						<div class="form-group">
							<select name="role_name" id="" class="form-control">
								@foreach($roles as $key => $role)
								<option value="{{$role}}">{{$role}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group">
							<button class='btn btn-primary'>{{trans('main.Addrole')}}</button>
						</div>
					</form>
					<table class='table'>
						<thead>
							<th>{{trans('main.Role')}}</th>
							<th>{{trans('main.Action')}}</th>
						</thead>
						<tbody>
							@foreach($userRoles as $role)
							<tr>
								<td>{{$role}}</td>
								<td>
									<form action="{{ route('user.remove_role') }}" method="POST">
										{{ csrf_field() }}
										<input type="text" hidden name="user_id" value="{{$user->id}}">
										<input type="text" hidden name="role" value="{{$role}}">
										<button type="submit" class="btn btn-danger btn-sm">
											<i class="ti ti-trash icon"></i>
										</button>
									</form>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header">
					<h3>{{$user->name}} {{trans('main.Permissions')}}</h3>
				</div>
				<div class="box-body">
					<form action="{{url('users/addPermission')}}" method="post">
						{!! csrf_field() !!}
						<input type="hidden" name="user_id" value="{{$user->id}}">
						<div class="form-group">
							<select name="permission_name[]" id="" class="js-state form-control select22"
									multiple="multiple">
								@foreach($permissions as $key => $permission)
									<option value="{{$key}}">{{$permission}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group">
							<button class='btn btn-primary'>{{trans('main.Addpermission')}}</button>
						</div>
					</form>
					<table class='table'>
						<thead>
							<th>{{trans('main.Permission')}}</th>
							<th>{{trans('main.Action')}}</th>
						</thead>
						<tbody>
							@foreach($userPermissions as $key => $permission)
							<tr>
								<td>{{$permission}}</td>
								<td><a href="{{url('users/removePermission')}}/{{$user->id}}/{{$key}}" class="btn btn-danger btn-sm"><i class="ti ti-trash icon"></i></a></td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	@endif
</section>
@endsection

@section('post_scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$('.select22').select2({
			placeholder: "Select permissions",
			allowClear: true
		});
	});
</script>
@endsection
