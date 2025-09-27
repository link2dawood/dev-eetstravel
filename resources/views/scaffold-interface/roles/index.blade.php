@extends('scaffold-interface.layouts.app')
@section('content')
	@include('layouts.title',
   ['title' => 'Roles', 'sub_title' => 'Roles List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Roles', 'icon' => 'user', 'route' => null]]])
<section class="content">
	<div class="box box-primary">
		<div class="box-body">
			<a href="{{url('roles/create')}}" class = "btn btn-success"><i class="fa fa-plus fa-md" aria-hidden="true"></i> {{trans('main.New')}}</a>
			<span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle " aria-hidden="true"></i>
				@include('legend.roles_legend')
                    </span>
			<br>
			<br>
			<table class="table table-striped">
				<thead>
				<tr>
					<th>{{trans('main.Role')}}</th>
					<th>{{trans('main.Permissions')}}</th>
					<th style="width: 100px">{{trans('main.Actions')}}</th>
				</tr>
				</thead>
				<tbody>
					@foreach($roles as $role)
					<tr>
						<td>{{$role->name}}</td>
						<td>
							@if(!empty($role->permissions))
								@foreach($role->permissions as $permission)
									<p style="display: inline-block"><small class = 'label bg-orange'>{{$permission->alias}}</small></p>
								@endforeach
							@else
								<small class = 'label bg-red'>{{trans('main.NoPermissions')}}</small>
							@endif
						</td>
						<td style="width: 100px">
							<a href="{{url('/roles')}}/{{$role->id}}/edit" class = "btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
<form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline-block;">
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
