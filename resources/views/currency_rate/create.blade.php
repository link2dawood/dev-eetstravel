@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
       ['title' => 'Currency Rate', 'sub_title' => 'Currency Rate Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Currency Rates', 'icon' => null, 'route' => route('currency_rate.index')],
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
                <form method='POST' action='{!!url("currency_rate")!!}'>
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
                                <label >{!!trans('main.Currency')!!}</label>
                                <input type="text" name="currency" value="{{ old('currency') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label >{!!trans('main.Rate')!!}</label>
                                <input type="text" name="rate" value="{{ old('rate') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="date">{!!trans('main.Date')!!}</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'date']) !!}
                                </div>

                            </div>
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

