@extends('scaffold-interface.layouts.tabler-app')
@section('content')
	@include('layouts.title',
   ['title' => 'Permission', 'sub_title' => 'Permission Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Permissions', 'icon' => 'key', 'route' => url('permissions')],
   ['title' => 'Edit', 'route' => null]]])
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
			<form action="{{url('permissions/update')}}" method="post">
                <div class="row manage-form-actions-top">
                    <div class="col-md-12">
                        <a href="javascript:history.back()" class="btn btn-primary back_btn">
                            <i class="ti ti-arrow-left me-1"></i>{{trans('main.Back')}}
                        </a>
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
                <div class="manage-form-actions-bottom">
                    <a href="{{ url('permissions') }}" class="btn btn-secondary">
                        <i class="ti ti-x me-1"></i>{{trans('main.Cancel')}}
                    </a>
                    <button class="btn btn-success" type="submit">
                        <i class="ti ti-device-floppy me-1"></i>{{trans('main.Save')}}
                    </button>
                </div>
			</form>
		</div>
	</div>
</section>
@endsection