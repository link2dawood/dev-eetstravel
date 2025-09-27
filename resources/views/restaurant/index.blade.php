@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
	@include('layouts.title',
   ['title' => 'Restaurants', 'sub_title' => 'Restaurants List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Restaurants', 'icon' => 'coffee', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
            @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
				<div>
					{!! \App\Helper\PermissionHelper::getCreateButton(route('restaurant.create'), \App\Restaurant::class) !!}
				</div>
        @if(session('export_all'))
          	<div class="alert alert-info col-md-12" style="text-align: center;">
         		{{session('export_all')}}
            </div>
        @endif
        <div class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="restaurants-search" class="form-control" placeholder="Search restaurants..." onkeyup="filterTable('restaurants-table', this.value)">
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-success btn-sm" onclick="exportTableToCSV('restaurants-table', 'restaurants_export.csv')">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="restaurants-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%; table-layout: fixed;word-break: break-all;'>
                <thead>
                <tr>
                    <th onclick="sortTable(0, 'restaurants-table')">ID <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(1, 'restaurants-table')">{{trans('main.Name')}} <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(2, 'restaurants-table')">{{trans('main.Address')}} <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(3, 'restaurants-table')">{{trans('main.Country')}} <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(4, 'restaurants-table')">{{trans('main.City')}} <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(5, 'restaurants-table')">{{trans('main.Phone')}} <i class="fa fa-sort"></i></th>
                    <th onclick="sortTable(6, 'restaurants-table')">{{trans('main.Email')}} <i class="fa fa-sort"></i></th>
                    <th class="actions-button" style="width: 140px">{{trans('main.Actions')}}</th>
                </tr>
                </thead>
                <tbody>
                    @forelse($restaurants as $restaurant)
                    <tr>
                        <td>{{ $restaurant->id }}</td>
                        <td>{{ $restaurant->name }}</td>
                        <td>{{ $restaurant->address_first }}</td>
                        <td>{{ $restaurant->country_name ?? '' }}</td>
                        <td>{{ $restaurant->city_name ?? '' }}</td>
                        <td>{{ $restaurant->work_phone }}</td>
                        <td>{{ $restaurant->contact_email }}</td>
                        <td>
                            @include('component.action_buttons', [
                                'show_route' => route('restaurant.show', $restaurant->id),
                                'edit_route' => route('restaurant.edit', $restaurant->id),
                                'delete_route' => route('restaurant.destroy', $restaurant->id),
                                'model' => $restaurant
                            ])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No restaurants found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-12">
                {{ $restaurants->links() }}
            </div>
        </div>
        </div>
        </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('restaurants-table');
    });
</script>
@endpush
