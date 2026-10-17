@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Email Templates')
@section('content')
    @include('layouts.title',
           ['title' => 'Email', 'sub_title' => 'Email Templates',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Email', 'icon' => 'envelope', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <table id="templates" class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
                    <thead>
                    <tr>
                        <th style="width:100%">{!!trans('main.ServiceName')!!}</th>
                        <th>{!!trans('main.Actions')!!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($templatesData as $template)
                        <tr>
                            <td>{{ $template->name }}</td>
                            <td>{!! $template->action_buttons !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th style="width:100%">{!!trans('main.ServiceName')!!}</th>
                        <th>{!!trans('main.Actions')!!}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
{{-- DataTables is loaded on demand by window.loadDataTables --}}
@include('component.datatables_cdn')
<script>
    $(document).ready(function() {
        $('#templates').find("tfoot").remove();

        // the whole row opens the service's templates
        $('#templates tbody').on( 'click', 'tr', function () {
            let url = $(this).find("a").attr('href');
            if (url) window.location.href = url;
        } );

        (window.loadDataTables ? window.loadDataTables() : Promise.resolve()).then(function () {
            if ($.fn.DataTable) {
                $('#templates').DataTable({
                    columnDefs: [
                        { targets: [1], orderable: false } // Actions column not sortable
                    ]
                });
            }
        }).catch(function () {});
    })
</script>
@endpush