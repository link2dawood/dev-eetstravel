@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Employee Salary', 'sub_title' => 'Employee Salary Create',
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
                <form method='POST' action='{{route('employes-salary.store')}}' enctype="multipart/form-data">
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
                                    <label for="Name">{!!trans('Name')!!}</label>
                                    <input id="employe_name" name="employe_name" type="text" class="form-control" value="{{old('employ_name')}}">
                                </div>
                                <div class="form-group">
                                    <label for="Salary">{!!trans('Salary')!!}</label>
                                    <input id="employe_salary" name="employe_salary" type="text" class="form-control" value="{{old('employe_salary')}}">
                                </div>

                                <div class="form-group">
									<label for="name">{!! trans(' Month') !!} *</label>
									{!! Form::text('month', '', ['class' => 'form-control pull-right datepicker']) !!}
								</div>

                                <div class="form-group">
                                    <label for="Bonuses">{!!trans('Bonuses')!!}</label>
                                    <input id="bonuses" name="bonuses" type="text" class="form-control" value="{{old('bonuses')}}">
                                </div>
                                
                                
                                
                                
                                
                                
                            
                                
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection