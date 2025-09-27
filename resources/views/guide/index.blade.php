@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Guides', 'sub_title' => 'Guides List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Guides', 'icon' => 'street-view', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
                    <div>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('guide.create'), \App\Guide::class) !!}
                    </div>
                @if(session('export_all'))
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    {{session('export_all')}}
                </div>
                @endif
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="guides-search" class="form-control" placeholder="Search guides..." onkeyup="filterTable('guides-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('guides-table', 'guides_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="guides-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff'>
                        <thead>
                          <tr>
                            <th onclick="sortTable(0, 'guides-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'guides-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'guides-table')">{!!trans('main.Address')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'guides-table')">{!!trans('main.Country')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'guides-table')">{!!trans('main.City')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'guides-table')">{!!trans('main.WorkPhone')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'guides-table')">{!!trans('main.WorkContact')!!} <i class="fa fa-sort"></i></th>
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

                <div class="row">
                    <div class="col-md-12">
                        {{ $guides->links() }}
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
        initializeBootstrapTable('guides-table');
    });
</script>
@endpush
