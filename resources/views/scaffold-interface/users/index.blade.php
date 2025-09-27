@extends('scaffold-interface.layouts.app')
@section('content')
	@include('layouts.title',
   ['title' => 'Users', 'sub_title' => 'Users List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Users', 'icon' => 'user', 'route' => null]]])
	<section class="content">
	<div class="box box-primary">
	<div class="box-body">
        @if (Session::has('message'))
            <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
        @endif
		<a href="{{url('/users/create')}}" class="btn btn-success"><i class="fa fa-plus fa-md" aria-hidden="true"></i> {{trans('main.New')}}</a>
		<br><br>
		<table class = "table table-hover">
			<thead>
			<tr>
				<th>{{trans('main.Name')}}</th>
				<th>{{trans('main.Email')}}</th>
				<th>{{trans('main.Roles')}}</th>
				<th>{{trans('main.Permissions')}}</th>
				<th style="width: 100px">{{trans('main.Actions')}}</th>
			</tr>
			</thead>
		<tbody>
			@foreach($users as $user)
			<tr>
				<td>{{$user->name}}</td>
				<td>{{$user->email}}</td>
				<td>
				@if(!empty($user->roles))
					@foreach($user->roles as $role)
					<small class = 'label bg-blue'>{{$role->name}}</small><hr style="margin: 2px">
					@endforeach
				@else
					<small class = 'label bg-red'>{{trans('main.NoRoles')}}</small>
				@endif
				</td>
				<td>
				@if(!empty($user->permissions))
					@foreach($user->permissions as $permission)
							<p style="display: inline-block;"><small class = 'label bg-orange'>{{$permission->alias}}</small></p>
					@endforeach
				@else
					<small class = 'label bg-red'>{{trans('main.NoPermissions')}}</small>
				@endif
				</td>
				<td style="width: 100px">
					<a href="{{url('/users/'.$user->id.'/edit')}}" class = 'btn btn-primary btn-sm'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
					{{-- <hr style="margin: 2px"> --}}
			
					<form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline-block;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">
        <i class="fa fa-trash-o"></i>
    </button>
</form>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
</div>
</section>
@endsection
