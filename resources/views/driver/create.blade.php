@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
       ['title' => 'Driver', 'sub_title' => 'Driver Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Drivers', 'icon' => null, 'route' => route('driver.index')],
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
                <form method='POST' action='{!!url("driver")!!}'  enctype="multipart/form-data">
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
                            <div class="form-group">
                                <label for="name">{!!trans('main.Name')!!}</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                @if($errors->has('name'))
                                    <strong>{{$errors->first('name')}}</strong>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="phone">{!!trans('main.Phone')!!}</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                                @if($errors->has('phone'))
                                    <strong>{{$errors->first('phone')}}</strong>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>{!!trans('main.Email')!!}</label>
                                <input type="text" name="email" value="{{ old('email') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="transfer">{!!trans('main.BusCompany')!!}</label>
                                {!! Form::select('transfer_id', $transfers, 0,['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group">
                                <label>{!!trans('main.Files')!!}</label>
                                @component('component.file_upload_field')@endcomponent
                            </div>
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

