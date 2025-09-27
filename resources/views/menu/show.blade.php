@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
    ['title' => 'Menu', 'sub_title' => 'Create Menu',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Create', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class="btn btn-primary back_btn">{{trans('main.Back')}}</button>
                                </a>
                                <a href="{{route('menu.edit', ['menu' =>  $menu->id])}}">
                                    <button class="btn btn-warning">{{trans('main.Edit')}}</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <table class = 'table_show col-lg-6 table table-bordered'>
                                <tbody>
                                <tr>
                                    <td>
                                        <b><i>{{trans('main.Name')}}: </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$menu->name!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{{trans('main.Price')}}: </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$menu->price!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{{trans('main.Description')}}: </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$menu->description!!}</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-md-6">
                            <span id="page" data-page="create"></span>

                        </div>
                    </div>
        </div>

        <script>


        </script>
        </div>
    </section>
@endsection