@extends('scaffold-interface.layouts.app')
@section('title', 'Settings')
@section('content')
	@include('layouts.title',
        ['title' => 'Settings', 'sub_title' => 'Settings List', 'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Settings', 'icon' => null, 'route' => null]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				 <a href="{{route('settings.create')}}" class="btn btn-success">New</a> 
				<br><br>
				<div class="mb-3">
					<div class="row">
						<div class="col-md-6">
							<input type="text" id="settings-search" class="form-control" placeholder="Search settings..." onkeyup="filterTable('settings-table', this.value)">
						</div>
						<div class="col-md-6 text-right">
							<button class="btn btn-success btn-sm" onclick="exportTableToCSV('settings-table', 'settings_export.csv')">
								<i class="fa fa-download"></i> Export CSV
							</button>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table id="settings-table" class="table table-striped table-bordered table-hover bootstrap-table">
					<thead>
						<tr>
							<th>{!!trans('main.Description')!!}</th>
							<th>{!!trans('main.Value')!!}</th>
							<th>{!!trans('main.Actions')!!}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($settings as $setting)
						<tr>
						<td>{{@$setting->description}}</td>
						<td>{{@$setting->value}}</td>
						<td style="width: 100px">
        <a href="{{ route('settings.edit', ['setting' => $setting->id]) }}" class="btn btn-primary btn-sm">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
        </a>
<a href="{{ route('settings.destroy', $setting->id) }}"
   class="btn btn-danger btn-sm"
   onclick="event.preventDefault(); document.getElementById('delete-form-{{ $setting->id }}').submit();">
    <i class="fa fa-trash-o" aria-hidden="true"></i>
</a>

<form id="delete-form-{{ $setting->id }}" action="{{ route('settings.destroy', $setting->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>


    </td>
						<tr>
						@endforeach

					</tbody>
					
				</table>
				</div>
			</div>
		</div>
	</section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('settings-table');
});
</script>
@endpush