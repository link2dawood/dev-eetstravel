@extends('scaffold-interface.layouts.tabler-app')
@section('title','Hotels')
@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Hotels</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">
                        Hotels
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('hotel.create'), \App\Hotel::class) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        @if(session('export_all'))
                        <div class="alert alert-info m-3">
                            {{session('export_all')}}
                        </div>
                        @endif

                        <div class="card-header">
                            <h3 class="card-title">Hotels List</h3>
                            <div class="col-auto ms-auto">
                                <button class="btn btn-success btn-sm" onclick="exportTableToCSV('hotels-table', 'hotels_export.csv')">
                                    <i class="ti ti-download"></i> Export CSV
                                </button>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3">
                            <div class="d-flex">
                                <div class="text-muted">
                                    Show
                                    <div class="mx-2 d-inline-block">
                                        <input type="text" class="form-control form-control-sm" value="10" size="3" aria-label="Items per page">
                                    </div>
                                    entries
                                </div>
                                <div class="ms-auto text-muted">
                                    Search:
                                    <div class="ms-2 d-inline-block">
                                        <input type="text" id="hotels-search" class="form-control form-control-sm" placeholder="Search hotels..." onkeyup="filterTable('hotels-table', this.value)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="hotels-table" class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1" onclick="sortTable(0, 'hotels-table')">Id <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(1, 'hotels-table')">{!!trans('main.Name')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(2, 'hotels-table')">{!!trans('main.Address')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(3, 'hotels-table')">{!!trans('main.Country')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(4, 'hotels-table')">{!!trans('main.City')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(5, 'hotels-table')">{!!trans('main.WorkPhone')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(6, 'hotels-table')">{!!trans('main.ContactEmail')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th class="w-1">{!!trans('main.Actions')!!}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($hotels as $hotel)
                                    <tr>
                                        <td><span class="text-muted">{{ $hotel->id }}</span></td>
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
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="ti ti-building icon mb-2" style="font-size: 3rem;"></i>
                                            <p>No hotels found</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            {{ $hotels->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('hotels-table');
    });
</script>
@endpush
