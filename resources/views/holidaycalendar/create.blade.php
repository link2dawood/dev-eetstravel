@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
       ['title' => 'Holidays', 'sub_title' => 'Holiday Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Holidays', 'icon' => null, 'route' => route('holiday.index')],
       ['title' => 'Create', 'route' => null]]])
       
       
      
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                @if (count($errors) > 0)
                    <br>
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method='POST' action='{!!url("holiday")!!}'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>
                            <input type='hidden' name='modal_create' value="0">
                            <div class="form-group">
                                <label >{!!trans('main.Name')!!}</label>
                                <input type="text" value="{{ old('name') }}" name="name" class="form-control" required  oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity('')"  >
                            </div>
                            
                            <div class="form-group color_view" style="display: none">
                                <label>{!!trans('main.Color')!!}</label>
                                <div id="cp2" class="input-group colorpicker-component">
                                    <input type="text" value="{{ old('backgroundcolor') }}" name="backgroundcolor" class="form-control">
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                            
                            
                            <div class="form-group">
                                <label for="type_status">{!!trans('main.Date')!!}</label>
                                <input class="form-control pull-right datepicker" id="start_time" name="start_time" type="text" value="">                                
                            </div>


                        </div>
                    </div>
                    <br/>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </form>
            </div>
        </div>
    </section>


    <script>
        function colorView() {            
                $('.color_view').css({'display': 'block'});           
        }

        $(document).ready(function (e) {
            colorView($('#backgroundcolor'));
        });
    </script>
@endsection


@section('colorpicker-js')
    <script src="{{ asset('js/colorpicker.js') }}"></script>
    <div class="colorpicker dropdown-menu colorpicker-hidden colorpicker-with-alpha colorpicker-right"><div class="colorpicker-saturation"><i><b></b></i></div><div class="colorpicker-hue"><i></i></div><div class="colorpicker-alpha"><i></i></div><div class="colorpicker-color"><div></div></div><div class="colorpicker-selectors"></div></div>
    <script> $(function() { $('#cp2').colorpicker(); }); </script>
@endsection

@section('colorpicker-css')
    <link rel="stylesheet" href="{{ asset('css/colorpicker.css') }}">
@endsection
