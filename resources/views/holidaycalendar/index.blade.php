@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Holidays', 'sub_title' => 'Holidays List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Holidays List', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">

            <br>
            <div class="col-lg-12">
                <div class="alert alert-danger" id="errors_message" style="text-align: center; display: none">
                </div>
            </div>

            <div class="box-body">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('holiday.create'), \App\Holidaycalendarday::class) !!}
                </div>
                <br>
                <br>
                <table id="holidaycalendar-table" class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
                    <thead>
                    <th>ID</th>
                    <th>{!!trans('main.Name')!!}</th>
                    <th>{!!trans('main.Date')!!}</th>
                    <th>{!!trans('main.Color')!!}</th>
                    <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                    </thead>
                </table>
            </div>
        </div>

    </section>
@endsection

@push('scripts')

<script>
    $(document).ready(function() {
        let table = $('#holidaycalendar-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [],
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: "{{route('holidaycalendar_data')}}",
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'start_time', name: 'start_time',
                    render: function(d){
                    return moment(d).format("YYYY-MM-DD");}
                },
                {data: 'backgroundcolor', name: 'backgroundcolor'},
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
            ],
        });
    })
</script>
@endpush