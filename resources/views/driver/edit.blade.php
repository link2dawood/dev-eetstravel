@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
           ['title' => 'Driver', 'sub_title' => 'Driver Edit',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Drivers', 'icon' => null, 'route' => route('driver.index')],
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
                {{ Form::model($driver, array('route' => array('driver.update', $driver->id), 'method' => 'PUT', 'enctype' => 'multipart/form-data')) }}
                {{--<form method='POST' action='{!! url("driver")!!}/{!!$driver->id!!}/update'>--}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">Back</button>
                                </a>
                                <button class='btn btn-success' type='submit'>Save</button>
                            </div>
                        </div>
                    </div>
                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $driver->name}}" name="name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" value="{{ $errors != null && count($errors) > 0 ? '' : $driver->phone}}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $driver->email}}" name="email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="rate_type">Bus Company</label>
                                    {!! Form::select('transfer_id', $transfers, $errors != null && count($errors) > 0 ? '' : $driver->transfer_id, ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    <label for="attach">Files</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>
                                @component('component.files', ['files' => $files])@endcomponent
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
                        <button class='btn btn-success' type='submit'>Save</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('driver.index'))}}">
                            <button class='btn btn-warning' type='button'>Cancel</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection