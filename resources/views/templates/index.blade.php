@extends('scaffold-interface.layouts.app')
@section('title','Index')
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

<script>


    $(document).ready(function() {
        $('#templates').DataTable( {
            columnDefs: [
                { targets: [1], orderable: false } // Actions column not sortable
            ]
        } );

        $('#templates').find("tfoot").remove();

        $('#templates tbody').on( 'click', 'tr', function () {
            let url = $(this).find("a").attr('href');
            window.location.href = url;
        } );

    })

</script>
@endpush