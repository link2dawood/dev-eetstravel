@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Exchange Rate')
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
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mt-3">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>
                            <input type='hidden' name='modal_create' value="0">
                            <div class="form-group mb-3">
                                <label >{!!trans('main.Currency')!!}</label>
                                @php $currencyCodes = \App\Currencies::query()->orderBy('code')->get(['code', 'name']); $currentCode = (string) old('currency'); @endphp
                                <select name="currency" class="form-select" required>
                                    <option value="">Select currency</option>
                                    @foreach($currencyCodes as $currencyOption)
                                        <option value="{{ $currencyOption->code }}" {{ strtoupper($currentCode) === strtoupper($currencyOption->code) ? 'selected' : '' }}>{{ $currencyOption->code }} - {{ $currencyOption->name }}</option>
                                    @endforeach
                                    @if($currentCode !== '' && !$currencyCodes->contains(function ($x) use ($currentCode) { return strtoupper($x->code) === strtoupper($currentCode); }))
                                        <option value="{{ $currentCode }}" selected>{{ $currentCode }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label >{!!trans('main.Rate')!!}</label>
                                <input type="number" step="0.000001" min="0" name="rate" value="{{ old('rate') }}" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="date">{!!trans('main.Date')!!}</label>

                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    {!! Form::text('date', old('date', date('Y-m-d')), ['class' => 'form-control datepicker', 'id' => 'date', 'autocomplete' => 'off', 'placeholder' => 'YYYY-MM-DD', 'required']) !!}
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

