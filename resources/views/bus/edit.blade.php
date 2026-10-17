@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Bus')
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
                <form enctype="multipart/form-data" method='POST' action='{!! url("bus")!!}/{!!$bus->id!!}/update'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="{{ route('bus.index') }}" class="btn btn-primary back_btn">{{ trans('main.Back') }}</a>
                            </div>
                        </div>
                    </div>
                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bus-name">{!!trans('main.Name')!!}</label>
                                    <input type="text" value="{{ old('name', $bus->name) }}" name="name" id="bus-name" required minlength="2" maxlength="191" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="bus-number">{!!trans('main.Busnumber')!!}</label>
                                    <input type="text" value="{{ old('bus_number', $bus->bus_number) }}" name="bus_number" id="bus-number" required maxlength="50" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>{!!trans('main.BusCompany')!!}</label>
                                    {!! Form::select('transfer_id', $transfers, old('transfer_id', $bus->transfer_id), ['class' => 'form-control', 'id' => 'transfer', 'placeholder' => 'Select bus company (optional)']) !!}
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