@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Taxes', 'sub_title' => 'Taxes List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Currencies', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body table-responsive">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('taxes.create'), \App\Currencies::class) !!}
                </div>
                <br>
                <br>
                <table id="currencies-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{!!trans('Name')!!}</th>
                        <th>{!!trans('Value')!!}</th>
                        <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($taxesData as $tax)
                        <tr>
                            <td>{{ $tax->id }}</td>
                            <td>{{ $tax->name }}</td>
                            <td>{{ $tax->value }}</td>
                            <td>{!! $tax->action_buttons !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th>{!!trans('Name')!!}</th>
						<th>{!!trans('Value')!!}</th>
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
        let table = $('#currencies-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Taxes List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Taxes List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Taxes List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            pageLength: 50,
            columnDefs: [
                { targets: [3], orderable: false } // Actions column not sortable
            ]
        });
        $('#currencies-table tfoot th').each( function () {
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
        $('#currencies-table tfoot th').appendTo('#currencies-table thead');
    })
</script>
@endpush