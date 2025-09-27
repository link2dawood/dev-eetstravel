@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
       ['title' => 'Criteria', 'sub_title' => 'Criteria Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Criteria', 'icon' => null, 'route' => route('criteria.index')],
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
                <form method='POST' action='{!!url("criteria")!!}'>
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
                                <label >{!!trans('main.ShortName')!!}</label>
                                <input type="text" name="short_name" value="{{ old('short_name') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label >{!!trans('main.Icon')!!}</label>
                                <input type="text" name="icon" value="{{ old('icon') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="criteria_type">{!!trans('main.Type')!!}</label>
                                <select name="criteria_type" id="criteria_type" class="form-control">
                                    @foreach($criteria_types as $criteria_type)
                                        <option {{ old('criteria_type') == $criteria_type->id ? 'selected' : '' }} value="{{ $criteria_type->id }}">{{ $criteria_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

