@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Utility Expense', 'sub_title' => 'Utility Expense Create',
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
                <form method='POST' action='{{route('utility_expenses.store')}}' enctype="multipart/form-data">
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
                                    <input id="subject_of_expense" name="subject_of_expense" type="text" class="form-control" value="{{old('subject_of_expense')}}">
                                </div>
                         
							
								<div class="form-group">
									<label for="name">{!! trans(' Month') !!} *</label>
									{!! Form::text('month', '', ['class' => 'form-control pull-right datepicker']) !!}
								</div>

                                <div class="form-group">
                                    <label for="Monthly_Expense">{!!trans('Monthly Expense')!!}</label>
                                    <input id="monthly_expense" name="monthly_expense" type="text" class="form-control" value="{{old('monthly_expense')}}">
                                </div>
                                
                                
                            
                                
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection