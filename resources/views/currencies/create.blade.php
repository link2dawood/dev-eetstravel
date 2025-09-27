@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
       ['title' => 'Currencies', 'sub_title' => 'Currencies Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Currencies', 'icon' => null, 'route' => route('currencies.index')],
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
                <form method='POST' action='{!!url("currencies")!!}'>
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
                                <label >{!!trans('main.Name')!!}</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label >{!!trans('main.Code')!!}</label>
                                <input type="text" name="code" value="{{ old('code') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label >{!!trans('main.Symbol')!!}</label>
                                <input type="text" name="symbol" value="{{ old('symbol') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label >{!!trans('main.Cent')!!}</label>
                                <input type="text" name="cent" value="{{ old('cent') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label >{!!trans('main.SymbolCent')!!}</label>
                                <input type="text" name="symbol_cent" value="{{ old('symbol_cent') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

