@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
	@include('layouts.title',
   ['title' => 'Cruises', 'sub_title' => 'Cruises List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Cruises', 'icon' => 'ship', 'route' => null]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
					<div>
						{!! \App\Helper\PermissionHelper::getCreateButton(route('cruises.create'), \App\Cruises::class) !!}
					</div>
				@if(session('export_all'))
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    {{session('export_all')}}
                </div>
                @endif
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="cruise-search" class="form-control" placeholder="Search cruises..." onkeyup="filterTable('cruise-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('cruise-table', 'cruises_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="cruise-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'cruise-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'cruise-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'cruise-table')">{!!trans('main.Datefrom')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'cruise-table')">{!!trans('main.Dateto')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'cruise-table')">{!!trans('main.CountryFrom')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'cruise-table')">{!!trans('main.Cityfrom')!!} <i class="fa fa-sort"></i></th>
                                <th>{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cruises as $cruise)
                            <tr>
                                <td>{{ $cruise->id }}</td>
                                <td>{{ $cruise->name ?? '' }}</td>
                                <td>{{ $cruise->date_from ? \Carbon\Carbon::parse($cruise->date_from)->format('Y-m-d H:i') : '' }}</td>
                                <td>{{ $cruise->date_to ? \Carbon\Carbon::parse($cruise->date_to)->format('Y-m-d H:i') : '' }}</td>
                                <td>{{ $cruise->country_from ?? '' }}</td>
                                <td>{{ $cruise->city_from ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('cruises.show', ['cruise' => $cruise->id]),
                                        'edit' => route('cruises.edit', ['cruise' => $cruise->id]),
                                        'delete_msg' => "/cruises/{$cruise->id}/delete_msg"
                                    ], false, $cruise) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No cruises found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{ $cruises->links() }}
                    </div>
                </div>
			</div>
		</div>
	</section>
<span id="service-name" hidden data-service-name='Cruises'></span>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('cruise-table');
});
</script>
@endpush
