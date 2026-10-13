@extends('scaffold-interface.layouts.tabler-app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Rates', 'sub_title' => 'Rates List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Rates', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <div class="input-icon flex-grow-1" style="max-width: 420px;">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="rate-search" class="form-control" placeholder="Search rates..." name="search" value="{{ request('search') }}" data-list-search>
                    </div>
                    <div class="ms-auto d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-success" onclick="exportTableToCSV('rate-table', 'rates_export.csv')">
                            <i class="ti ti-download me-1"></i> Export CSV
                        </button>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('rate.create'), \App\Rate::class) !!}
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="rate-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'rate-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'rate-table')">{{trans('main.Name')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'rate-table')">{{trans('main.Mark')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'rate-table')">{{trans('main.Ratetype')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'rate-table')">{{trans('main.Sortorder')}} <i class="fa fa-sort"></i></th>
                                <th>{{trans('main.Actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rates as $rate)
                            <tr>
                                <td>{{ $rate->id }}</td>
                                <td>{{ $rate->name ?? '' }}</td>
                                <td>{{ $rate->mark ?? '' }}</td>
                                <td>{{ $rate->rate_type ?? '' }}</td>
                                <td>{{ $rate->sort_order ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('rate.show', ['rate' => $rate->id]),
                                        'edit' => route('rate.edit', ['rate' => $rate->id]),
                                        'delete_msg' => "/rate/{$rate->id}/deleteMsg"
                                    ], false, $rate) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No rates found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($rates->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">Showing {{ $rates->firstItem() }} to {{ $rates->lastItem() }} of {{ $rates->total() }} entries</div>
                    <div>{{ $rates->links() }}</div>
                </div>
                @endif
            </div>
        </div>

    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script src="{{ asset('js/list-search.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('rate-table');
});
</script>
@endpush