@extends('scaffold-interface.layouts.tabler-app')
@section('title','Guides')
@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Guides</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">Guides</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('guide.create'), \App\Guide::class) !!}
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
                        <div class="card-header">
                            <h3 class="card-title">Guides List</h3>
                            <div class="col-auto ms-auto">
                                <div class="btn-list">
                                    <button class="btn btn-success btn-sm" onclick="exportTableToCSV('guides-table', 'guides_export.csv')">
                                        <i class="ti ti-download"></i> Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                        @if (Session::has('message'))
                            <div class="card-body">
                                <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                            </div>
                        @endif
                        @if(session('export_all'))
                        <div class="card-body">
                            <div class="alert alert-info">
                                {{session('export_all')}}
                            </div>
                        </div>
                        @endif
                        <div class="card-body">
                            <div class="mb-3">
                                <input type="text" id="guides-search" class="form-control" placeholder="Search guides..." onkeyup="filterTable('guides-table', this.value)">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="guides-table" class="table card-table table-vcenter text-nowrap datatable" style='background:#fff'>
                                <thead>
                                  <tr>
                                    <th onclick="sortTable(0, 'guides-table')">ID <i class="ti ti-arrows-sort"></i></th>
                                    <th onclick="sortTable(1, 'guides-table')">{!!trans('main.Name')!!} <i class="ti ti-arrows-sort"></i></th>
                                    <th onclick="sortTable(2, 'guides-table')">{!!trans('main.Address')!!} <i class="ti ti-arrows-sort"></i></th>
                                    <th onclick="sortTable(3, 'guides-table')">{!!trans('main.Country')!!} <i class="ti ti-arrows-sort"></i></th>
                                    <th onclick="sortTable(4, 'guides-table')">{!!trans('main.City')!!} <i class="ti ti-arrows-sort"></i></th>
                                    <th onclick="sortTable(5, 'guides-table')">{!!trans('main.WorkPhone')!!} <i class="ti ti-arrows-sort"></i></th>
                                    <th onclick="sortTable(6, 'guides-table')">{!!trans('main.WorkContact')!!} <i class="ti ti-arrows-sort"></i></th>
                                    <th class="actions-button" style="width: 140px">{!!trans('main.Actions')!!}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($guides as $guide)
                                    <tr>
                                        <td>{{ $guide->id }}</td>
                                        <td>{{ $guide->name }}</td>
                                        <td>{{ $guide->address }}</td>
                                        <td>{{ $guide->country_name ?? '' }}</td>
                                        <td>{{ $guide->city_name ?? '' }}</td>
                                        <td>{{ $guide->work_phone }}</td>
                                        <td>{{ $guide->work_contact }}</td>
                                        <td>
                                            @include('component.action_buttons', [
                                                'show_route' => route('guide.show', $guide->id),
                                                'edit_route' => route('guide.edit', $guide->id),
                                                'delete_route' => route('guide.destroy', $guide->id),
                                                'model' => $guide
                                            ])
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No guides found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            {{ $guides->links() }}
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
        initializeBootstrapTable('guides-table');
    });
</script>
@endpush
