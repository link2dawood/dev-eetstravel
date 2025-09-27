@extends('scaffold-interface.layouts.app')
@section('title', 'Edit')
@section('content')
	@include('layouts.title',
        ['title' => 'Settings', 'sub_title' => 'Settings Edit', 'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Settings', 'icon' => null, 'route' => null]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
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
				<form method='POST' action="{{ route('settings.update', ['setting' => $setting->id]) }}">
					<div class="row">
						<div class="col-md-12">
							<div class="margin_button">
								<a href="{!! route('settings.index') !!}">
									<button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
								</a>
								<button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
							</div>
						</div>
					</div>
					{{csrf_field()}}
					{{method_field('PUT')}}
					<div class="form-group">
						<label >{!!trans('main.Description')!!}</label>
						<input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $setting->description}}{{ old('desc') }}" name="desc" class="form-control">
					</div>
					<div class="form-group">
						<label >{!!trans('main.Value')!!}</label>
						@if(preg_match('/date/', $setting->name))
                            <div class="input-group date">

                                <div class='input-group date' id='datetimepicker2'>
                                    <input type='text' id="value" name="value"  value="{{ $errors != null && count($errors) > 0 ? '' : $setting->value}}{{ old('value') }}" class="form-control" />
                                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>

                    </span>
                                </div>
                                <br><small style="color: grey;">*{!!trans('main.ServerTime')!!}: {{\Carbon\Carbon::now()}}</small>
                            </div>
                            <script type="text/javascript">
                                $(function () {
                                    $('#datetimepicker2').datetimepicker({
                                        locale: 'en',
                                        viewMode: 'months',
                                        format: 'YYYY-MM-DD HH:mm:ss'
                                    });
                                });
                            </script>
						@elseif (preg_match('/text/', $setting->name))
							<input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $setting->value}}{{ old('value') }}" name="value" class="form-control">
						@else
						<input type="number" min="0" value="{{ $errors != null && count($errors) > 0 ? '' : $setting->value}}{{ old('value') }}" name="value" class="form-control">
                        @endif
					</div>
					<button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
					<a href="{{\App\Helper\AdminHelper::getBackButton(route('settings.index'))}}">
						<button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
					</a>
				</form>
			</div>
			</div>
		</div>
	</section>
@endsection
@push('scripts')
	<script type="text/javascript" src='{{asset('js/hide_elements.js')}}'></script>
@endpush