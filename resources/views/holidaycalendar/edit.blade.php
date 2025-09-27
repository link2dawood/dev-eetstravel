@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
           ['title' => 'Holidays', 'sub_title' => 'Holiday Edit',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Holidays', 'icon' => null, 'route' => route('holiday.index')],
           ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                    </a>
                    <button type="button" class="btn btn-success update-holiday">{!!trans('main.Save')!!}</button>
                    <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        @include('legend.quotation_legend_edit')
                    </span>
                </div>
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
                <form method='POST' action='{!! url("holiday")!!}/{!!$holidaycalendarday->id!!}/update' id="update_holiday_form">
                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label >{!!trans('main.Name')!!}</label>

                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $holidaycalendarday->name}}{{ old('name') }}" name="name" class="form-control">

                                </div>

                                <div class="form-group color_view" style="display: none">
                                    <label>{!!trans('main.Color')!!}</label>
                                    <div id="cp2" class="input-group colorpicker-component">
                                        <input type="text" value="@if (old('backgroundcolor')) {{ old('backgroundcolor') }} @else {{ $holidaycalendarday->backgroundcolor }} @endif" name="backgroundcolor" class="form-control">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                                <span id="color_status" style="display: none" data-attr="{{ $holidaycalendarday->color }}"></span>
                                
                                <div class="form-group">
                                    <label for="type_status">{!!trans('main.Date')!!}</label>
                                    <input class="form-control pull-right datepicker" id="start_time" name="start_time" type="text" value="@if (old('start_time')) {{ old('start_time') }} @else {{ $holidaycalendarday->start_time }} @endif">                                
                                </div>
                                
                            </div>
                            <div class="col-md-8">
                                <div class="tour-packages"></div>
                                <div class="row">
                                    <div class="col-md-6">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="itinerary" class="tab-pane fade">

                        </div>
{{--                        <button class='btn btn-primary' type='submit'>Save</button>--}}
                    </div>
                </form>
            </div>
            <div class="box-footer">
                <button type="button" class="btn btn-success update-holiday">{!!trans('main.Save')!!}</button>
                <a href="{{\App\Helper\AdminHelper::getBackButton(route('restaurant.index'))}}">
                    <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                </a>
            </div>
        </div>
    </section>

    <script>
        function colorView(_this) {
                var color = $('#color_status').attr('data-attr');
                $('#color_field').val('');
                $('#block_color_field_bg').css({'background-color' : 'transparent'});
                $('.color_view').css({'display': 'block'});
                $('.update-holiday').click(function(){
                    $('#update_holiday_form').submit();
                });
        }

        $(document).ready(function (e) {
            colorView($('#type_status'));

            var color = $('#color_status').attr('data-attr');
            $('#color_field').val(color);
        });
    </script>
@endsection

@section('colorpicker-js')
    <script src="{{ asset('js/colorpicker.js') }}"></script>
    <div class="colorpicker dropdown-menu colorpicker-hidden colorpicker-with-alpha colorpicker-right"><div class="colorpicker-saturation"><i><b></b></i></div><div class="colorpicker-hue"><i></i></div><div class="colorpicker-alpha"><i></i></div><div class="colorpicker-color"><div></div></div><div class="colorpicker-selectors"></div></div>
    <script>
        $(function() {
            $('#cp2').colorpicker();
        });
    </script>
@endsection

@section('colorpicker-css')
    <link rel="stylesheet" href="{{ asset('css/colorpicker.css') }}">
@endsection