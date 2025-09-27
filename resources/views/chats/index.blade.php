@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title', [
                    'title' => 'Chats',
                     'sub_title' => 'List'])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <br>
                <table id="ajax-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
                    <thead>
                    <th>ID</th>
                    <th>{!!trans('main.Title')!!}</th>
                    <th>{!!trans('main.Description')!!}</th>
                    <th>{!!trans('main.Type')!!}</th>
                    <th>{!!trans('main.Author')!!}</th>
                    <th class="actions-button" style="width: 140px">{!!trans('main.Actions')!!}</th>
                    </thead>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th>{!!trans('main.Title')!!}</th>
                        <th>{!!trans('main.Description')!!}</th>
                        <th>{!!trans('main.Type')!!}</th>
                        <th>{!!trans('main.Author')!!}</th>
                        <th class="not"></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </section>
@endsection
@push('scripts')

<script>
    $(document).ready(function() {
        let table = $('#ajax-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Chats List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Chats List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Chats List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            language : {
                search: "Global Search :"
            },
            pageLength: 50,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{route('chat.data')}}",
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'description', name: 'description'},
                {data: 'type', name: 'type'},
                {data: 'author', name: 'users.name'},
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
            ],
        });
        $('#ajax-table tfoot th').each( function () {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
            }
        });
        table.columns().every( function () {
            let that = this;

            $('input', this.footer()).on('keyup change', function() {
                if(that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#ajax-table tfoot th').appendTo('#ajax-table thead');
    })
</script>
@endpush