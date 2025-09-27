@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
           ['title' => 'Status', 'sub_title' => 'Status Edit',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Statuses', 'icon' => null, 'route' => route('status.index')],
           ['title' => 'Edit', 'route' => null]]])
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
                <form method='POST' action='{!! url("status")!!}/{!!$status->id!!}/update'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>
                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label >{!!trans('main.Name')!!}</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $status->name}}{{ old('name') }}" name="name" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="type">{!!trans('main.Ratetype')!!}</label>
                                    <select name="type" id="type_status" class="form-control" onchange="colorView($(this))">
                                        @foreach($status_types as $status_type)
                                            <option value="{{ $status_type->type }}" {{ $errors != null && count($errors) > 0 ? old('type') == $status_type->type ? 'selected' : '' : $status->type == $status_type->type ? 'selected' : '' }}>{{ $status_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{!!trans('main.Sortorder')!!}</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $status->sort_order}}{{ old('sort_order') }}" name="sort_order" class="form-control">
                                </div>

                                <div class="form-group color_view" style="display: none">
                                    <label>{!!trans('main.Color')!!}</label>
                                    <div id="cp2" class="input-group colorpicker-component">
                                        <input type="text" id="color_field" name="color" class="form-control">
                                        <span  class="input-group-addon"><i id="block_color_field_bg"></i></span>
                                    </div>
                                </div>
                                <span id="color_status" style="display: none" data-attr="{{ $status->color }}"></span>
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
                        <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('status.index'))}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        function colorView(_this) {
            if($(_this).val() === 'tour' || $(_this).val() === 'bus'){
                var color = $('#color_status').attr('data-attr');
                $('#color_field').val('');
                $('#block_color_field_bg').css({'background-color' : 'transparent'});
                $('.color_view').css({'display': 'block'});
            }else{
                $('#color_field').val('');
                $('#block_color_field_bg').css({'background-color' : 'transparent'});
                $('.color_view').css({'display': 'none'});
            }
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