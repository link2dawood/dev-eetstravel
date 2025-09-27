@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Criteria', 'sub_title' => 'Criteria List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Criteria', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
                    <div>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('criteria.create'), \App\Criteria::class) !!}
                    </div>
                <br>
                <br>
                <table id="criteria-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 98%; table-layout: fixed'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{!!trans('main.Name')!!}</th>
                        <th>{!!trans('main.ShortName')!!}</th>
                        <th>{!!trans('main.Icon')!!}</th>
                        <th>{!!trans('main.CriteriaType')!!}</th>
                        <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($criteriaData as $criteria)
                        <tr>
                            <td>{{ $criteria->id }}</td>
                            <td>{{ $criteria->name }}</td>
                            <td>{{ $criteria->short_name }}</td>
                            <td>{!! $criteria->formatted_icon !!}</td>
                            <td>{{ $criteria->criteria_type }}</td>
                            <td>{!! $criteria->action_buttons !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th>{!!trans('main.Name')!!}</th>
                        <th>{!!trans('main.ShortName')!!}</th>
                        <th>{!!trans('main.Icon')!!}</th>
                        <th>{!!trans('main.CriteriaType')!!}</th>
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
        let table = $('#criteria-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Criteria List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Criteria List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Criteria List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            pageLength: 50,
            columnDefs: [
                { targets: [5], orderable: false } // Actions column not sortable
            ]
        });

        $('#criteria-table tfoot th').each( function () {
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

        $('#criteria-table tfoot th').appendTo('#criteria-table thead');
    })
</script>
@endpush