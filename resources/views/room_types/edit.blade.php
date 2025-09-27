@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
           ['title' => 'Room Type', 'sub_title' => 'Room Type Edit',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Room Types', 'icon' => null, 'route' => route('room_types.index')],
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
                <form method='POST' action='{!! url("room_types")!!}/{!!$room_types->id!!}/update' enctype ="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{{trans('main.Back')}}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{{trans('main.Save')}}</button>
                            </div>
                        </div>
                    </div>
                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label >{{trans('main.Name')}}</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $room_types->name}}{{ old('name') }}" name="name" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>{{trans('main.Code')}}</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $room_types->code}}{{ old('code') }}" name="code" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>{{trans('main.Sortorder')}}</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $room_types->sort_order}}{{ old('sort_order') }}" name="sort_order" class="form-control">
                                </div>
								<div class="form-group">
                                <label >{{trans('Image')}}</label>
                                <input type="file" name="image" value="{{ old('name') }}" class="form-control" required>
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
                        <button class='btn btn-success' type='submit'>{{trans('main.Save')}}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('room_types.index'))}}">
                            <button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection