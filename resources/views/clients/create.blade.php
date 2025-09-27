@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Client', 'sub_title' => 'Client Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Clients', 'icon' => 'handshake-o', 'route' => route('clients.index')],
   ['title' => 'Create', 'route' => null]]])
    <section class="content" style="padding-bottom: 40px;">
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
                <form method='POST' action='{{route('clients.store')}}' enctype="multipart/form-data">
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

                                <div class="form-group ">
                                    <label for="name">{!!trans('main.Name')!!}</label>
                                    <input id="name" name="name" type="text" class="form-control" value="{{old('name')}}">
                                </div>
							
							{{--
								<div class="form-group ">
                                    <label for="name">{!!trans('Department')!!}</label>
                                    <input id="name" name="department" type="text" class="form-control" value="{{old('name')}}">
                                </div>
							
								<div class="form-group">

                                <label for="departure_date">Dep Date *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input class="form-control pull-right datepicker" id="departure_date" autocomplete="off" name="dep_date" type="text" value="">
                                </div>
                            </div>
							
							
							<div class="form-group ">
                                    <label for="name">{!!trans('Duration')!!}</label>
                                    <input id="name" name="duration" type="text" class="form-control" value="{{old('name')}}">
                                </div>
							--}}
							
                                <div class="form-group">
                                    <label for="address">{!!trans('main.Address')!!}</label>
                                    <input id="address" name="address" type="text" class="form-control" value="{{old('address')}}">
                                </div>
							{{--new--}}
								<div class="form-group">
                                    <label for="account_no">{!!trans('Account No')!!}</label>
                                    <input id="account_no" name="account_no" type="text" class="form-control" value="{{old('account_no')}}">
                                </div>
								<div class="form-group">
                                    <label for="company_address">{!!trans('Company Address')!!}</label>
                                    <input id="company_address" name="company_address" type="text" class="form-control" value="{{old('company_address')}}">
                                </div>
								<div class="form-group">
                                    <label for="invoice_address">{!!trans('Invoice Address')!!}</label>
                                    <input id="invoice_address" name="invoice_address" type="text" class="form-control" value="{{old('invoice_address')}}">
                                </div>
								@component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' =>0,
                                        'city_label' => 'city','city_translation' =>'main.City', 'city_default' => 0])
                                @endcomponent
                      
							
                                </div>
							
							<div class="col-md-6">
								          <div class="form-group">
                                    <label for="work_phone">{!!trans('main.WorkPhone')!!}</label>
                                    <input id="work_phone" name="work_phone" type="text" class="form-control" value="{{old('work_phone')}}">
                                </div>
							
                                <div class="form-group">
                                    <label for="contact_phone">{!!trans('main.ContactPhone')!!}</label>
                                    <input id="contact_phone" name="contact_phone" type="text" class="form-control" value="{{old('contact_phone')}}">
                                </div>
                                <div class="form-group">
                                    <label for="work_email">{!!trans('main.WorkEmail')!!}</label>
                                    <input id="work_email" name="work_email" type="text" class="form-control" value="{{old('work_email')}}">
                                </div>
                                <div class="form-group">
                                    <label for="contact_email">{!!trans('main.ContactEmail')!!}</label>
                                    <input id="contact_email" name="contact_email" type="text" class="form-control" value="{{old('contact_email')}}">
                                </div>
								<div class="form-group">
                                    <label for="password">{!!trans('password')!!}</label>
                                    <input id="password" name="password" type="password" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label for="work_fax">{!!trans('main.WorkFax')!!}</label>
                                    <input id="work_fax" name="work_fax" type="text" class="form-control" value="{{old('work_fax')}}">
                                </div>
                                <div class="form-group">
                                    <label>{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
  

@endsection