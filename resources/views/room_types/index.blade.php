@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Room Types')
@section('content')
    @include('layouts.title',
           ['title' => 'Room Types', 'sub_title' => 'Room Types List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Room Types', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <div class="input-icon flex-grow-1" style="max-width: 420px;">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input aria-label="Search room types..." type="text" id="room-types-search" class="form-control" placeholder="Search room types..." name="search" value="{{ request('search') }}" data-list-search>
                    </div>
                    <div class="ms-auto d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-success" onclick="exportTableToCSV('room-types-table', 'room_types_export.csv')">
                            <i class="ti ti-download me-1"></i> Export CSV
                        </button>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('room_types.create'), \App\RoomTypes::class) !!}
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="room-types-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'room-types-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'room-types-table')">{{trans('main.Name')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'room-types-table')">{{trans('main.Code')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'room-types-table')">{{trans('main.Sortorder')}} <i class="fa fa-sort"></i></th>
                                <th>{{trans('main.Actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($room_types as $room_type)
                            <tr>
                                <td>{{ $room_type->id }}</td>
                                <td>{{ $room_type->name ?? '' }}</td>
                                <td>{{ $room_type->code ?? '' }}</td>
                                <td>{{ $room_type->sort_order ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('room_types.show', ['room_type' => $room_type->id]),
                                        'edit' => route('room_types.edit', ['room_type' => $room_type->id]),
                                        'delete_msg' => "/room_types/{$room_type->id}/deleteMsg"
                                    ], false, $room_type) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No room types found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($room_types->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">Showing {{ $room_types->firstItem() }} to {{ $room_types->lastItem() }} of {{ $room_types->total() }} entries</div>
                    <div>{{ $room_types->links() }}</div>
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
    initializeBootstrapTable('room-types-table');
});
</script>
@endpush