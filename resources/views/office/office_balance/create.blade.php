@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Office balances', 'sub_title' => 'Office balances Create',
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
                <form method='POST' action='{{route('office_balance.store')}}' enctype="multipart/form-data">
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
								<input type = "hidden" name = "office_id" value = "{{$office->id}}">
                                <div class="form-group ">
                                    <label for="Subject">{!!trans('Subject')!!}</label>
                                    <input id="subject_of_balance" name="subject_of_balance" type="text" class="form-control" value="{{old('subject_of_balance')}}">
                                </div>
                              
                                <div class="form-group">
									<label for="name">{!! trans(' Month') !!} *</label>
									{!! Form::text('month', '', ['class' => 'form-control pull-right datepicker']) !!}
								</div>

                                <div class="form-group">
                                    <label for="Total_Amount">{!!trans('Total Amount')!!}</label>
                                    <input id="total_amount" name="total_amount" type="text" class="form-control" value="{{old('total_amount')}}">
                                </div>

                                <div class="form-group">
                                    <label for="Due_Date">{!!trans('Due Date')!!}</label>
                                    <input id="due_date" name="due_date" type="date" class="form-control" value="{{old('total_amount')}}">
                                </div>


                              
                                
                                
                                
                                
                                
                                
                            
                                
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection