@extends('scaffold-interface.layouts.app')
@section('title', 'Create')
@section('content')
    @include('layouts.title', [
        'title' => 'Tax Edit',
        'sub_title' => 'Edit Tax',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Taxes', 'icon' => 'suitcase', 'route' => route('taxes.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action="{{route('taxes_update', ['tax' => $tax->id])}}"  id="data-form" enctype="multipart/form-data">
                    {{ csrf_field() }}
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!! trans('main.Back') !!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!! trans('main.Save') !!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
                            <div class="alert alert-danger" style="display:none;" id="error_block">
                            </div>
                            <div class="form-group">
                                <label for="name">{!! trans(' TAX Name') !!} *</label>
                                {!! Form::text('name', $tax->name, ['class' => 'form-control', 'id' => 'name']) !!}
                            </div>

                           

                            <div class="form-group">
                                <label for="name">{!! trans('Percentage') !!} *</label>
                                {!! Form::number('percentageInput', $tax->value, ['class' => 'form-control', 'id' => 'percentageInput','min' => 1, 'max'=>100]) !!}
                               
                            </div>


                        </div>
                    </div>

                </form>
            </div>
        </div>
       
       
    </section>
   
@endsection
