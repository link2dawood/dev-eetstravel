@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Settings')
@section('content')
	@include('layouts.title', [
		'title' => 'Settings',
		'sub_title' => 'Settings List',
		'breadcrumbs' => [
			['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
			['title' => 'Settings', 'icon' => null, 'route' => null]
		]
	])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<a href="{{route('settings.create')}}" class="btn btn-success"><i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a> 
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
							<th style="width: 140px">{!!trans('main.Actions')!!}</th>
						</tr>
					</thead>
					<tbody>
						@forelse($settings as $setting)
						<tr>
							<td>{{@$setting->description}}</td>
							<td>{{@$setting->value}}</td>
							<td>
								<div class="btn-list flex-nowrap">
									<!-- EDIT BUTTON -->
									<a href="{{ route('settings.edit', ['setting' => $setting->id]) }}" class="btn btn-icon btn-ghost-warning" title="Edit">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
											<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
											<path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
											<path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
											<path d="M16 5l3 3" />
										</svg>
									</a>

									<!-- DELETE BUTTON -->
									<a href="{{ route('settings.destroy', $setting->id) }}"
									   class="btn btn-icon btn-ghost-danger"
									   title="Delete"
									   onclick="event.preventDefault(); confirmDelete('{{ route('settings.destroy', $setting->id) }}', '{{ $setting->id }}');">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
											<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
											<path d="M4 7l16 0" />
											<path d="M10 11l0 6" />
											<path d="M14 11l0 6" />
											<path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
											<path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
										</svg>
									</a>

									<!-- Hidden Delete Form -->
									<form id="delete-form-{{ $setting->id }}" action="{{ route('settings.destroy', $setting->id) }}" method="POST" style="display: none;">
										@csrf
										@method('DELETE')
									</form>
								</div>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="3" class="text-center text-secondary py-4">
								No settings found
							</td>
						</tr>
						@endforelse
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

function confirmDelete(url, settingId) {
	if (confirm('Are you sure you want to delete this setting?')) {
		document.getElementById('delete-form-' + settingId).submit();
	}
}
</script>
@endpush

<style>
	.table tbody td {
		vertical-align: middle;
	}
</style>