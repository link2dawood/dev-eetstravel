@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Settings')
@section('content')
	@include('layouts.title',
        ['title' => 'Settings Create', 'sub_title' => 'Settings Create', 'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Settings', 'icon' => null, 'route' => route('settings.index')]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				@if ($errors->any())
					<div class="alert alert-danger">
						<ul class="mb-0">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
				<div class="col-md-6">
					<form action="{{route('settings.store')}}" method="post">
						<div class="mb-3">
							<a href="{{ route('settings.index') }}" class="btn btn-primary back_btn">
								<i class="ti ti-arrow-left me-1"></i>{!!trans('main.Back')!!}
							</a>
						</div>
					{{csrf_field()}}
						<div class="form-group">
							<label for="name">{!!trans('main.Name')!!}</label>
							<input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. tour_service_notification" class="form-control" maxlength="191" pattern="[a-z0-9_]+" required>
							<small class="form-text text-muted">Lowercase letters, numbers and _ only. A name containing <code>date</code> takes a date and time (YYYY-MM-DD HH:MM:SS), one containing <code>text</code> takes text, anything else a whole number of days.</small>
						</div>
						<div class="form-group">
							<label for="description">{!!trans('main.Description')!!}</label>
							<textarea id="description" class="form-control" style="resize: vertical;" placeholder="add description" name="description" maxlength="1000" required>{{ old('description') }}</textarea>
						</div>
						<div class="form-group">
							<label for="value">{!!trans('main.Value')!!}</label>
							<input type="text" id="value" name="value" value="{{ old('value') }}" placeholder="setting value" class="form-control" maxlength="191" required>
						</div>
						<div class="mt-3">
							<button type="submit" class="btn btn-success">
								<i class="ti ti-device-floppy me-1"></i>{!!trans('main.Save')!!}
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
@endsection
