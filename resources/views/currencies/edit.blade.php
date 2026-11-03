@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Currency')
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
                <form method="POST" action="{{ route('currencies.update', ['currency' => $currencies->id]) }}">
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-lg-6 mt-3">
                                <div class="form-group mb-3">
                                    <label >{!!trans('main.Name')!!}</label>
                                    <input type="text" name="name" value="{{ old('name', $currencies->name) }}" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label >{!!trans('main.Code')!!}</label>
                                    <input type="text" name="code" value="{{ old('code', $currencies->code) }}" class="form-control" maxlength="3" pattern="[A-Za-z]{3}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label >{!!trans('main.Symbol')!!}</label>
                                    <input type="text" name="symbol" value="{{ old('symbol', $currencies->symbol) }}" class="form-control" maxlength="10" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label >{!!trans('main.Cent')!!}</label>
                                    <input type="text" name="cent" value="{{ old('cent', $currencies->cent) }}" class="form-control" maxlength="10">
                                </div>
                                <div class="form-group mb-3">
                                    <label >{!!trans('main.SymbolCent')!!}</label>
                                    <input type="number" name="symbol_cent" value="{{ old('symbol_cent', $currencies->symbol_cent) }}" class="form-control" min="1" max="2147483647" step="1">
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
