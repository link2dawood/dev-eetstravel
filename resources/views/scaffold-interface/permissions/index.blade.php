@extends('scaffold-interface.layouts.app')
@section('content')
	@include('layouts.title',
   ['title' => 'Permissions', 'sub_title' => 'Permissions List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Permissions', 'icon' => 'key', 'route' => null]]])
<section class="content">
	<div class="box box-primary">
		<div class="box-body">
			<a href="{{url('permissions/create')}}" class = "btn btn-success"><i class="fa fa-plus fa-md" aria-hidden="true"></i> {{trans('main.New')}}</a>
			<span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
				@include('legend.permissions_legend')
                    </span>
			<br>
			<br>
			<table class="table table-striped">
				<thead>
				<tr>
					<th>{{trans('main.Permission')}}</th>
					<th>{{trans('main.Alias')}}</th>
					<th style="width: 100px">{{trans('main.Actions')}}</th>
				</tr>
				</thead>
				<tbody>
					@foreach($permissions as $permission)
					<tr>
						<td>{{$permission->name}}</td>
						<td>{{$permission->alias}}</td>
						<td style="width: 100px">
							<a href="{{url('/permissions')}}/{{$permission->id}}/edit" class = "btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
							{{--<a href="{{url('/permissions/delete')}}/{{$permission->id}}" class = "btn btn-danger btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i></a>--}}
<form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display: inline-block;">
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
