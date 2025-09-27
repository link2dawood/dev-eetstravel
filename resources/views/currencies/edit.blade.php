@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
       ['title' => 'Currencies', 'sub_title' => 'Currencies Edit',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Currencies', 'icon' => null, 'route' => route('currencies.index')],
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
                <form method='POST' action='{!! url("currencies")!!}/{!!$currencies->id!!}/update'>
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
                                    <input type="text" name="name" value="{{ $errors != null && count($errors) > 0 ? '' : $currencies->name}}{{ old('name') }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label >{!!trans('main.Code')!!}</label>
                                    <input type="text" name="code" value="{{ $errors != null && count($errors) > 0 ? '' : $currencies->code}}{{ old('code') }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label >{!!trans('main.Symbol')!!}</label>
                                    <input type="text" name="symbol" value="{{ $errors != null && count($errors) > 0 ? '' : $currencies->symbol}}{{ old('symbol') }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label >{!!trans('main.Cent')!!}</label>
                                    <input type="text" name="cent" value="{{ $errors != null && count($errors) > 0 ? '' : $currencies->cent}}{{ old('cent') }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label >{!!trans('main.SymbolCent')!!}</label>
                                    <input type="text" name="symbol_cent" value="{{ $errors != null && count($errors) > 0 ? '' : $currencies->symbol_cent}}{{ old('symbol_cent') }}" class="form-control">
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
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('currencies.index'))}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection