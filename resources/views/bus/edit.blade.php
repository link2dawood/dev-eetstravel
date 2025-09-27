@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
           ['title' => 'Bus', 'sub_title' => 'Bus Edit',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Buses', 'icon' => 'bus', 'route' => route('bus.index')],
           ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
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
                <form method='POST' action='{!! url("bus")!!}/{!!$bus->id!!}/update'>
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
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $bus->name}}{{ old('name') }}" name="name" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>{!!trans('main.Busnumber')!!}</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $bus->bus_number}}{{ old('bus_number') }}" name="bus_number" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>{!!trans('main.BusCompany')!!}</label>
                                    {!! Form::select('transfer_id', $transfers, $errors != null && count($errors) > 0 ? '' : $bus->transfer_id, ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    <label for="attach">{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>
                                @component('component.files', ['files' => $files])@endcomponent
                            </div>
                        </div>

                        <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('bus.index'))}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection