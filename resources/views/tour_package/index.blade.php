@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h1>
                {!!trans('main.TourPackage')!!}
            </h1>
        </div>
        <div class="box-body">
            <form class = 'col s3' method = 'get' action = '{!!url("tour_package")!!}/create'>
                <button class = 'btn btn-success' type = 'submit'><i class="fa fa-plus fa-md" aria-hidden="true"></i> {!!trans('main.New')!!}</button>
            </form>
            <br>
            <table class = "table table-striped table-bordered table-hover" style = 'background:#fff'>
                <thead>
                <th>{!!trans('main.name')!!}</th>
                <th>{!!trans('main.description')!!}</th>
                <th>{!!trans('main.status')!!}</th>
                <th>{!!trans('main.actions')!!}</th>
                </thead>
                <tbody>
                @foreach($tourPackages as $tour_package)
                    <tr>
                        <td>{!!$tour_package->name!!}</td>
                        <td>{!!$tour_package->description!!}</td>
                        <td>
                            @if($tour_package->status)
                                <button type="button" class="btn btn-success  btn-xs">{!!trans('main.Yes')!!}</button>
                            @else
                                <button type="button" class="btn btn-danger  btn-xs">{!!trans('main.No')!!}</button>
                        @endif
                        <td>
                            <a href = '#' class = 'viewShow btn btn-warning btn-xs' data-link = '/tour_package/{!!$tour_package->id!!}'><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                            <a href = '/tour_package/{!!$tour_package->id!!}/edit' class = 'btn btn-primary btn-xs' data-link = '/tour_package/{!!$tour_package->id!!}/edit'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <a data-toggle="modal" data-target="#myModal" class = 'delete btn btn-danger btn-xs' data-link = "/tour_package/{!!$tour_package->id!!}/deleteMsg" ><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $tourPackages->render() !!}
        </div>
    </div>
</section>
@endsection