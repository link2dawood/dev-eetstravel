@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
    ['title' => 'Menu', 'sub_title' => 'Create Menu',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Create', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <form method='POST' action='{{route('menu.update', ['menu' => $menu->id])}}'>
                {{ method_field('PUT') }}
                <div class="box box-body border_top_none">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success pre-loader-func' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {{csrf_field()}}
                            @include('component.js-validate')
                            <div class="form-group">
                                {{Form::label('name')}}
                                {{Form::input('text', 'name', $menu->name, ['class' => 'form-control'])}}
                            </div>
                            <div class="form-group">
                                {{Form::label('price')}}
                                {{Form::input('text', 'price', $menu->price, ['class' => 'form-control'])}}
                            </div>
                            <div class="form-group">
                                {{Form::label('description')}}
                                {{Form::textarea('description', $menu->description, ['class' => 'form-control textarea_editor', 'id' => 'description'])}}
                            </div>


                            <button class='btn btn-success pre-loader-func' type='submit'>{{trans('main.Save')}}</button>
                            <a href="{{$menu->getParentRoute()}}">
                                <button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <span id="page" data-page="create"></span>

                        </div>
                    </div>
            </form>
        </div>

        <script>
            $(document).ready(function(){
                CKEDITOR.replace( 'description',{
                    title : false,
                    height: '400px'
                } );
            });


        </script>
        </div>
    </section>
@endsection