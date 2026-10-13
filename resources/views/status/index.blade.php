@extends('scaffold-interface.layouts.tabler-app')
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
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <div class="input-icon flex-grow-1" style="max-width: 420px;">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="status-search" class="form-control" placeholder="Search statuses..." name="search" value="{{ request('search') }}" data-list-search>
                    </div>
                    <div class="ms-auto d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-success" onclick="exportTableToCSV('status-table', 'statuses_export.csv')">
                            <i class="ti ti-download me-1"></i> Export CSV
                        </button>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('status.create'), \App\Status::class) !!}
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
                                <td data-delete-label>{{ $statusItem->name ?? '' }}</td>
                                <td>{{ $statusItem->status_type ?? '' }}</td>
                                <td>{{ $statusItem->sort_order ?? '' }}</td>
                                <td class="text-end">
                                    @include('component.action_buttons', [
                                        'item' => $statusItem,
                                        'routePrefix' => 'status'
                                    ])
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
                @if($status->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">Showing {{ $status->firstItem() }} to {{ $status->lastItem() }} of {{ $status->total() }} entries</div>
                    <div>{{ $status->links() }}</div>
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
    initializeBootstrapTable('status-table');
});
</script>
@endpush

@include('component.delete_modal_simple')