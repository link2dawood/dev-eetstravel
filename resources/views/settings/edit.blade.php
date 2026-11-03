@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Setting')
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
								<a href="{!! route('settings.index') !!}" class="btn btn-primary back_btn">
									<i class="ti ti-arrow-left me-1"></i>{!!trans('main.Back')!!}
								</a>
							</div>
						</div>
					</div>
					{{csrf_field()}}
					{{method_field('PUT')}}
					<div class="form-group">
						<label>{!!trans('main.Name')!!}</label>
						<input type="text" value="{{ $setting->name }}" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label >{!!trans('main.Description')!!}</label>
						<input type="text" value="{{ old('desc', $setting->description) }}" name="desc" class="form-control" maxlength="1000" required>
					</div>
					<div class="form-group">
						<label >{!!trans('main.Value')!!}</label>
						@if(preg_match('/date/', $setting->name))
                            <div class="input-group date">

                                <div class='input-group date' id='datetimepicker2'>
                                    <input type='text' id="value" name="value"  value="{{ old('value', $setting->value) }}" class="form-control" />
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
							<input type="text" value="{{ old('value', $setting->value) }}" name="value" class="form-control">
						@else
						<input type="number" min="0" max="3650" step="1" required value="{{ old('value', $setting->value) }}" name="value" class="form-control">
                        @endif
					</div>
					<div class="mt-3">
						<a href="{{\App\Helper\AdminHelper::getBackButton(route('settings.index'))}}" class="btn btn-secondary">
							<i class="ti ti-x me-1"></i>{!!trans('main.Cancel')!!}
						</a>
						<button class='btn btn-success' type='submit'>
							<i class="ti ti-device-floppy me-1"></i>{!!trans('main.Save')!!}
						</button>
					</div>
				</form>
			</div>
			</div>
		</div>
	</section>
@endsection
@push('scripts')
	<script type="text/javascript" src='{{asset('js/hide_elements.js')}}'></script>
@endpush