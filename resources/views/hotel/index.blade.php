@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
	@include('layouts.title',
   ['title' => 'Hotels', 'sub_title' => 'Hotels List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Hotels', 'icon' => 'hotel', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <br>
            <div class="col-lg-12">
                <div class="alert alert-danger" id="errors_message" style="text-align: center; display: none">
                </div>
            </div>
            <div class="box-body">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('hotel.create'), \App\Hotel::class) !!}
                </div>
                @if(session('export_all'))
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    {{session('export_all')}}
                </div>
                @endif
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="hotels-search" class="form-control" placeholder="Search hotels..." onkeyup="filterTable('hotels-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('hotels-table', 'hotels_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="hotels-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff;width: 98%;'>
                        <thead>
                          <tr>
                            <th onclick="sortTable(0, 'hotels-table')" style="width: 1%;">Id <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'hotels-table')" style="width: 10%;">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'hotels-table')" style="width: 10%;">{!!trans('main.Address')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'hotels-table')" style="width: 10%;">{!!trans('main.Country')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'hotels-table')" style="width: 10%;">{!!trans('main.City')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'hotels-table')" style="width: 10%;">{!!trans('main.WorkPhone')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'hotels-table')" style="width: 10%;">{!!trans('main.ContactEmail')!!} <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width:150px; text-align: center;">{!!trans('main.Actions')!!}</th>
                          </tr>
                        </thead>
                        <tbody>
                            @forelse($hotels as $hotel)
                            <tr>
                                <td>{{ $hotel->id }}</td>
                                <td>{{ $hotel->name }}</td>
                                <td>{{ $hotel->address }}</td>
                                <td>{{ $hotel->country_name ?? '' }}</td>
                                <td>{{ $hotel->city_name ?? '' }}</td>
                                <td>{{ $hotel->work_phone }}</td>
                                <td>{{ $hotel->contact_email }}</td>
                                <td>
                                    @include('component.action_buttons', [
                                        'show_route' => route('hotel.show', $hotel->id),
                                        'edit_route' => route('hotel.edit', $hotel->id),
                                        'delete_route' => route('hotel.destroy', $hotel->id),
                                        'model' => $hotel
                                    ])
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No hotels found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        {{ $hotels->links() }}
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
        initializeBootstrapTable('hotels-table');
    });
</script>
@endpush
