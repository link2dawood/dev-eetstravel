@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Statuses', 'sub_title' => 'Statuses List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Statuses', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">

            <br>
            <div class="col-lg-12">
                <div class="alert alert-danger" id="errors_message" style="text-align: center; display: none">
                </div>
            </div>


            <div class="box-body">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('status.create'), \App\Status::class) !!}
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="status-search" class="form-control" placeholder="Search statuses..." onkeyup="filterTable('status-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('status-table', 'statuses_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="status-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'status-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'status-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'status-table')">{!!trans('main.Type')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'status-table')">{!!trans('main.SortOrder')!!} <i class="fa fa-sort"></i></th>
                                <th>{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($status as $statusItem)
                            <tr>
                                <td>{{ $statusItem->id }}</td>
                                <td>{{ $statusItem->name ?? '' }}</td>
                                <td>{{ $statusItem->status_type ?? '' }}</td>
                                <td>{{ $statusItem->sort_order ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('status.show', ['status' => $statusItem->id]),
                                        'edit' => route('status.edit', ['status' => $statusItem->id]),
                                        'delete_msg' => "/status/{$statusItem->id}/deleteMsg"
                                    ], false, $statusItem) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No statuses found</td>
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
    initializeBootstrapTable('status-table');
});
</script>
@endpush