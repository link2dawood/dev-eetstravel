@extends('scaffold-interface.layouts.app')
@section('title', 'Settings')
@section('content')
	@include('layouts.title',
        ['title' => 'Settings Create', 'sub_title' => 'Settings Create', 'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Settings', 'icon' => null, 'route' => route('settings.index')]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<div class="col-md-4">
					<form action="{{route('settings.store')}}" method="post">
					{{csrf_field()}}
						<div class="form-group">
							<label for="name">{!!trans('main.Name')!!}</label>
							<input type="text" name="name" placeholder="add name" class="form-control" required>
						</div>
						<div class="form-group">
							<label for="description">{!!trans('main.Description')!!}</label>
							<textarea class="form-control" style="resize: vertical;" placeholder="add description" name="description"></textarea>
						</div>
						<div class="form-group">
							<label for="value">{!!trans('main.Value')!!}</label>
							<input type="text" name="value" placeholder="setting value" class="form-control" required>
						</div>
						<button type="submit" class="btn btn-default">{!!trans('main.Create')!!}</button>
					</form>
				</div>
			</div>
		</div>
	</section>
@endsection
