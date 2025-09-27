@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Room Types', 'sub_title' => 'Room Types List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Room Types', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('room_types.create'), \App\RoomTypes::class) !!}
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="room-types-search" class="form-control" placeholder="Search room types..." onkeyup="filterTable('room-types-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('room-types-table', 'room_types_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
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
            </div>
        </div>

    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('room-types-table');
});
</script>
@endpush