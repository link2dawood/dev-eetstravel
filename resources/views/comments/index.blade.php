@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
    ['title' => 'Comments', 'sub_title' => 'Comments List',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Comments', 'icon' => 'comment', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (session('not_found'))
                    <div class="alert alert-info">
                        {{session('not_found')}}
                    </div>
                @endif
                <table id="comment-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{!!trans('main.Content')!!}</th>
                        <th>{!!trans('main.Time')!!}</th>
                        <th>{!!trans('main.Sender')!!}</th>
                        <th class="actions-button" style="width: 140px">{!!trans('main.Actions')!!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($commentsData as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->content }}</td>
                            <td>{{ $comment->created_at }}</td>
                            <td>{{ $comment->sender }}</td>
                            <td>{!! $comment->action_buttons !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th>{!!trans('main.Content')!!}</th>
                        <th>{!!trans('main.Time')!!}</th>
                        <th>{!!trans('main.Sender')!!}</th>
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
        let table = $('#comment-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Comments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Comments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Comments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            pageLength: 50,
            "bJQueryUI": true,
            columnDefs: [
                { targets: [4], orderable: false } // Actions column not sortable
            ]
        });
        $('#comment-table tfoot th').each( function () {
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
        $('#comment-table tfoot th').appendTo('#comment-table thead');
    })
</script>
@endpush
