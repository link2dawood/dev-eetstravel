@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Rate')
@section('content')
    @include('layouts.title',
           ['title' => 'Rate', 'sub_title' => 'Rate Edit',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Rates', 'icon' => null, 'route' => route('rate.index')],
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
                <form method="POST" action="{{ route('rate.update', ['rate' => $rate->id]) }}">
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{{trans('main.Back')}}</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-lg-6 mt-3">
                                <div class="form-group mb-3">
                                    <label >{{trans('main.Name')}}</label>
                                    <input type="text" value="{{ old('name', $rate->name) }}" name="name" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label >{{trans('main.Mark')}}</label>
                                    <input type="text" value="{{ old('mark', $rate->mark) }}" name="mark" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="rate_type">{{trans('main.Ratetype')}}</label>
                                    <select name="rate_type" id="rate_type" class="form-control">
                                        <option value="">Choose a rate type</option>
                                        @foreach($rate_types as $rate_type)
                                            <option value="{{ $rate_type->id }}" {{ old('rate_type', $rate->rate_type) == $rate_type->id ? 'selected' : '' }}>{{ $rate_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label>{{trans('main.Sortorder')}}</label>
                                    <input type="text" value="{{ old('sort_order', $rate->sort_order) }}" name="sort_order" class="form-control">
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
                        <button class='btn btn-success' type='submit'>{{trans('main.Save')}}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('rate.index'))}}">
                            <button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
