@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
       ['title' => 'Bus', 'sub_title' => 'Bus Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Buses', 'icon' => 'bus', 'route' => route('bus.index')],
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
                    <form method='POST' action='{!!url("bus")!!}'>
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
                                    <label>{!!trans('main.Name')!!}</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>{!!trans('main.Busnumber')!!}</label>
                                    <input type="text" value="{{ old('bus_number') }}" name="bus_number" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="transfer">{!!trans('main.BusCompany')!!}</label>
                                    {!! Form::select('transfer_id', $transfers, 0,['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    <label>Files</label>
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

