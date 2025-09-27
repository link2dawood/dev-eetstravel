@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
       ['title' => 'Status', 'sub_title' => 'Status Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Statuses', 'icon' => null, 'route' => route('status.index')],
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

                <form method='POST' action='{!!url("status")!!}'>
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

                            <div class="form-group">
                                <label for="type_status">{!!trans('main.Type')!!}</label>
                                <select name="type" id="type_status" class="form-control" onchange="colorView($(this));">
                                    @foreach($status_types as $status_type)
                                        <option {{ old('type') == $status_type->type ? 'selected' : '' }}
                                                value="{{ $status_type->type }}">{{ $status_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{!!trans('main.Sortorder')!!}</label>
                                <input type="text" value="{{ old('sort_order') }}" name="sort_order" class="form-control" required oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity('')">
                            </div>

                            <div class="form-group color_view" style="display: none">
                                <label>{!!trans('main.Color')!!}</label>
                                <div id="cp2" class="input-group colorpicker-component">
                                    <input type="text" value="{{ old('color') }}" name="color" class="form-control">
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </form>
            </div>
        </div>
    </section>


    <script>
        function colorView(_this) {
            if($(_this).val() === 'tour' || $(_this).val() === 'bus'){
                $('.color_view').css({'display': 'block'});
            }else{
                $('.color_view').css({'display': 'none'});
            }
        }

        $(document).ready(function (e) {
            colorView($('#type_status'));
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

