@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
           ['title' => 'Driver', 'sub_title' => 'Driver Show',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Drivers', 'icon' => null, 'route' => route('driver.index')],
           ['title' => 'Show', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'>Back</button>
                            </a>
                            <a href="{!! route('driver.edit', $driver->id) !!}">
                                <button class='btn btn-warning'>Edit</button>
                            </a>
                        </div>
                    </div>
                </div>
                <table class = 'table table-bordered'>
                    <tbody>
                    <tr>
                        <td class="show_width_td">
                            <b><i>Name : </i></b>
                        </td>
                        <td>{!!$driver->name!!}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>Phone : </i></b>
                        </td>
                        <td>{!!$driver->phone!!}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>Email : </i></b>
                        </td>
                        <td>{!!$driver->email!!}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>Bus Company : </i></b>
                        </td>
                        <td>{!!@$driver->transfer->name!!}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="files" style="padding: 20px">
                <div class="clearfix"></div>
                @component('component.files', ['files' => $files])@endcomponent
            </div>
        </div>

    </section>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection