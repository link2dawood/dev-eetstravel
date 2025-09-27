@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
       ['title' => 'Criteria', 'sub_title' => 'Criteria Edit',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Criteria', 'icon' => null, 'route' => route('criteria.index')],
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
                <form method='POST' action='{!! url("criteria")!!}/{!!$criteria->id!!}/update'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-4">
                                <input type='hidden' name='_token' value='{{Session::token()}}'>
                                <div class="form-group">
                                    <label >{!!trans('main.Name')!!}</label>
                                    <input type="text" name="name" value="{{ $errors != null && count($errors) > 0 ? '' : $criteria->name}}{{ old('name') }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label >{!!trans('main.ShortName')!!}</label>
                                    <input type="text" name="short_name" value="{{ $errors != null && count($errors) > 0 ? '' : $criteria->short_name}}{{ old('short_name') }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label >{!!trans('main.Icon')!!}</label>
                                    <input type="text" name="icon" value="{{ $errors != null && count($errors) > 0 ? '' : $criteria->icon}}{{ old('icon') }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="criteria_type">{!!trans('main.Criteriatype')!!}</label>
                                    <select name="criteria_type" id="criteria_type" class="form-control">
                                        @foreach($criteria_types as $criteria_type)
                                            <option value="{{ $criteria_type->id }}" {{ $errors != null && count($errors) > 0 ? old('criteria_type') == $criteria_type->id ? 'selected' : '' : $criteria->criteria_type == $criteria_type->id ? 'selected' : '' }}>{{ $criteria_type->name }}</option>
                                        @endforeach
                                    </select>
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
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('criteria.index'))}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection