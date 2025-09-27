@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
       ['title' => 'Currency Rate', 'sub_title' => 'Currency Rate Edit',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Currency Rates', 'icon' => null, 'route' => route('currency_rate.index')],
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
                <form method='POST' action='{!! url("currency_rate")!!}/{!!$currency_rate->id!!}/update'>
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
                                    <label >{!!trans('main.Currency')!!}</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $currency_rate->currency}}{{ old('currency') }}" name="currency" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label >{!!trans('main.Rate')!!}</label>
                                    <input type="text" value="{{ $errors != null && count($errors) > 0 ? '' : $currency_rate->rate}}{{ old('rate') }}" name="rate" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="date">{!!trans('main.Date')!!}</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        {!! Form::text('date', $currency_rate->date, ['class' => 'form-control pull-right datepicker', 'id' => 'date']) !!}
                                    </div>

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
                        <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('currency_rate.index'))}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection