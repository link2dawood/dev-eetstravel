@extends('scaffold-interface.layouts.tabler-app')
@section('content')
	@include('layouts.title',
   ['title' => 'Permission', 'sub_title' => 'Permission Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Permissions', 'icon' => 'key', 'route' => url('permissions')],
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
			<form action="{{url('permissions/store')}}" method="post">
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
                <div class="manage-form-actions-bottom">
                    <a href="{{ url('permissions') }}" class="btn btn-secondary">
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