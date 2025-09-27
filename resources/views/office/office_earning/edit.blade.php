@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
   ['title' => 'Office Earnings', 'sub_title' => 'Office Earnings Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Clients', 'icon' => 'handshake-o', 'route' => route('clients.index')],
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
                <form method='POST' action='{{route('office_earning.update', ['office_earning' => $office_earnings->id])}}' enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Edit')!!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">

                                {{csrf_field()}}
								<input type = "hidden" name = "office_id" value = "1">
                                <div class="form-group">
									<label for="name">{!! trans(' Month') !!} *</label>
									<input class="form-control pull-right datepicker" name="month" type="text" value="{{$office_earnings->month}}">
								</div>
                                <div class="form-group">
                                    <label for="Revenue">{!!trans('Revenue')!!}</label>
                                    <input id="revenue" name="revenue" type="text" class="form-control" value="{{$office_earnings->revenue}}">
                                </div>

                                <div class="form-group">
                                    <label for="Profit">{!!trans('Profit')!!}</label>
                                    <input id="profit" name="profit" type="text" class="form-control" value="{{$office_earnings->profit}}">
                                </div>

                              
                                
                                
                                
                                
                                
                                
                            
                                
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection