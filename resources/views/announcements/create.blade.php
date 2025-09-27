@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Announcement', 'sub_title' => 'Announcement Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Announcements', 'icon' => 'coffee', 'route' => route('announcements.index')],
   ['title' => 'Create', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{{route('announcements.store')}}' enctype="multipart/form-data">
                    @csrf
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
                        <div class="col-md-6">

                            <div class="form-group {{$errors->has('title') ? 'has-error' : ''}}">
                                <label for="title">{!!trans('main.Title')!!}</label>
                                {!! Form::input('text', 'title', $title, ['class' => 'form-control', 'id' => 'title']) !!}
                                @if($errors->has('title'))
                                    <strong>{{$errors->first('title')}}</strong>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="content">{!!trans('main.Content')!!}</label>
                                {!! Form::textarea('content', '', ['id' => 'content', 'class' => 'form-control']) !!}
                            </div>

                            {{Form::hidden('parent_id', $parent_id)}}

                                <div class="form-group">
                         <label>{!! trans('main.Files') !!}</label>
                   <input type="file" name="files[]" class="form-control" multiple>
                 </div>


                            <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
