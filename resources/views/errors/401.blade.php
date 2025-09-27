@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    <section class="content">
        <div class="box box-danger">
            <div class="box-body">
                <h3>{!!trans('main.Sorryyouhavenopermissions')!!}</h3>

                <h3>{!!trans('main.Goto')!!} <a href="{{url('home')}}">{!!trans('main.dashboard')!!}</a></h3>
            </div>
        </div>
    </section>
@endsection